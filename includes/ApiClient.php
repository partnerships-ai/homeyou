<?php
/**
 * WiserLeads API cURL Client with Mock Mode support
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

class ApiClient {
    private string $campaignCode;
    private string $campaignToken;
    private string $pingUrl;
    private string $postUrl;
    private bool $mockMode;

    public function __construct() {
        $settings = [];
        if (Database::isSaveEnabled()) {
            // Load initial values from settings table
            try {
                $db = Database::getConnection();
                $stmt = $db->query("SELECT `key`, `value` FROM settings");
                while ($row = $stmt->fetch()) {
                    $settings[$row['key']] = $row['value'];
                }
            } catch (Exception $e) {
                Logger::error("Settings load failed inside ApiClient: " . $e->getMessage());
            }
        }

        // Get fallbacks from config
        $config = require dirname(__DIR__) . '/config/campaign.php';

        $this->campaignCode = $settings['campaign_code'] ?? $config['campaign_code'];
        $this->campaignToken = $settings['campaign_token'] ?? $config['campaign_token'];
        $this->pingUrl = $settings['ping_url'] ?? $config['ping_url'];
        $this->postUrl = $settings['post_url'] ?? $config['post_url'];
        $this->mockMode = isset($settings['mock_mode']) 
            ? filter_var($settings['mock_mode'], FILTER_VALIDATE_BOOLEAN) 
            : $config['mock_mode'];
    }

    /**
     * Send Ping API Request (Non-PII qualification step)
     */
    public function ping(int $leadId, array $data): array {
        $payload = [
            'campaign' => $this->campaignCode,
            'campaign_token' => $this->campaignToken,
            'service' => $data['service'] ?? '',
            'dynamic_questions' => json_decode($data['dynamic_questions'] ?? '{}', true),
            'home_ownership' => $data['home_ownership'] ?? '',
            'timeline' => $data['timeline'] ?? '',
            'hiring_status' => $data['hiring_status'] ?? '',
            'zip_code' => $data['zip_code'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        Logger::info("Initiating Ping API Call", ['lead_id' => $leadId, 'mock_mode' => $this->mockMode]);

        if ($this->mockMode) {
            // Trigger rejection if ZIP code starts with 999
            if (str_starts_with($payload['zip_code'], '999')) {
                $responseBody = json_encode([
                    'status' => 'rejected',
                    'reason' => 'No active service providers matching ZIP code ' . htmlspecialchars($payload['zip_code'])
                ]);
                $statusCode = 200;
            } else {
                $responseBody = json_encode([
                    'status' => 'success',
                    'lead_token' => 'mock_token_' . bin2hex(random_bytes(12)),
                    'providers' => [
                        ['id' => 'angi_pro', 'name' => 'Angi Roofing & Siding Solutions'],
                        ['id' => 'modernize_net', 'name' => 'Modernize Certified Pros'],
                        ['id' => 'homeyou_net', 'name' => 'homeyou Certified Contractor Network']
                    ]
                ]);
                $statusCode = 200;
            }

            $this->logApiCall($leadId, 'ping', $this->pingUrl, $payload, $statusCode, $responseBody);
            return json_decode($responseBody, true);
        }

        return $this->sendRequest($leadId, 'ping', $this->pingUrl, $payload);
    }

    /**
     * Send Post API Request (PII details step)
     */
    public function post(int $leadId, string $leadToken, array $data): array {
        $payload = [
            'campaign' => $this->campaignCode,
            'campaign_token' => $this->campaignToken,
            'lead_token' => $leadToken,
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'street_address' => $data['street_address'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zip_code' => $data['zip_code'] ?? ''
        ];

        Logger::info("Initiating Post API Call", ['lead_id' => $leadId, 'mock_mode' => $this->mockMode]);

        if ($this->mockMode) {
            // Trigger post failure if email matches fail@test.com
            if ($payload['email'] === 'fail@test.com') {
                $responseBody = json_encode([
                    'status' => 'failed',
                    'errors' => ['Mock Validation: Submission rejected due to test blacklist email.']
                ]);
                $statusCode = 400;
            } else {
                $responseBody = json_encode([
                    'status' => 'success',
                    'redirect_url' => '', // Optional redirection URL
                    'message' => 'Lead successfully generated and syndicated.'
                ]);
                $statusCode = 200;
            }

            $this->logApiCall($leadId, 'post', $this->postUrl, $payload, $statusCode, $responseBody);
            return json_decode($responseBody, true);
        }

        return $this->sendRequest($leadId, 'post', $this->postUrl, $payload);
    }

    /**
     * Execute curl request
     */
    private function sendRequest(int $leadId, string $type, string $url, array $payload): array {
        $ch = curl_init($url);
        $jsonPayload = json_encode($payload);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $errorBody = json_encode([
                'status' => 'failed',
                'errors' => ['cURL connection error: ' . $err]
            ]);
            $this->logApiCall($leadId, $type, $url, $payload, 500, $errorBody, $headers);
            return json_decode($errorBody, true);
        }

        $this->logApiCall($leadId, $type, $url, $payload, $statusCode, $responseBody, $headers);
        
        $decoded = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'failed',
                'errors' => ['Malformed server response received.']
            ];
        }
        return $decoded;
    }

    /**
     * Save requests and responses in DB log tables
     */
    private function logApiCall(int $leadId, string $type, string $url, array $requestBody, int $statusCode, string $responseBody, array $headers = []): void {
        if (!Database::isSaveEnabled()) {
            Logger::info("API Call Mock Log", [
                'lead_id' => $leadId,
                'api_type' => $type,
                'url' => $url,
                'status_code' => $statusCode,
                'response_body' => $responseBody
            ]);
            return;
        }

        try {
            $db = Database::getConnection();

            // Insert API Request record
            $reqStmt = $db->prepare("INSERT INTO api_requests (lead_id, api_type, url, request_body, headers) VALUES (?, ?, ?, ?, ?)");
            $reqStmt->execute([
                $leadId,
                $type,
                $url,
                json_encode($requestBody),
                json_encode($headers ?: ['Content-Type' => 'application/json'])
            ]);

            // Insert API Response record
            $resStmt = $db->prepare("INSERT INTO api_responses (lead_id, api_type, status_code, response_body) VALUES (?, ?, ?, ?)");
            $resStmt->execute([
                $leadId,
                $type,
                $statusCode,
                $responseBody
            ]);
        } catch (Exception $e) {
            Logger::error("API logging transaction failed: " . $e->getMessage(), [
                'lead_id' => $leadId,
                'api_type' => $type
            ]);
        }
    }
}
