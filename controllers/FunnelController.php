<?php
/**
 * Funnel Controller handles routing actions for the lead funnel steps and AJAX endpoints
 */

require_once dirname(__DIR__) . '/includes/CSRF.php';
require_once dirname(__DIR__) . '/includes/RateLimiter.php';
require_once dirname(__DIR__) . '/includes/Logger.php';
require_once dirname(__DIR__) . '/includes/ApiClient.php';
require_once dirname(__DIR__) . '/models/Lead.php';

class FunnelController {
    
    /**
     * Show front-end lead generation funnel wizard page
     */
    public function showFunnel(): void {
        $csrfToken = CSRF::generateToken();
        require dirname(__DIR__) . '/views/funnel.php';
    }

    /**
     * Handle the AJAX Ping request (qualify service data)
     */
    public function handlePing(): void {
        header('Content-Type: application/json');
        
        // Rate limiting check
        if (!RateLimiter::check()) {
            http_response_code(429);
            echo json_encode(['status' => 'failed', 'errors' => ['Too many requests. Please wait and try again.']]);
            return;
        }

        // Parse JSON body
        $inputData = json_decode(file_get_contents('php://input'), true);
        if (!$inputData) {
            http_response_code(400);
            echo json_encode(['status' => 'failed', 'errors' => ['Invalid input format.']]);
            return;
        }

        // Validate CSRF token
        if (!CSRF::validateToken($inputData['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(['status' => 'failed', 'errors' => ['Security verification failed. Please reload the page.']]);
            return;
        }

        // Field validations
        $errors = [];
        $service = trim($inputData['service'] ?? '');
        $homeOwnership = trim($inputData['home_ownership'] ?? '');
        $timeline = trim($inputData['timeline'] ?? '');
        $hiringStatus = trim($inputData['hiring_status'] ?? '');
        $zipCode = trim($inputData['zip_code'] ?? '');
        $description = trim($inputData['description'] ?? '');
        $dynamicQuestions = $inputData['dynamic_questions'] ?? [];

        if (empty($service)) $errors[] = "Please select a service type.";
        if (empty($homeOwnership)) $errors[] = "Home ownership field is required.";
        if (empty($timeline)) $errors[] = "Timeline option is required.";
        if (empty($hiringStatus)) $errors[] = "Hiring status is required.";
        if (empty($zipCode) || !preg_match('/^\d{5}(-\d{4})?$/', $zipCode)) {
            $errors[] = "Please provide a valid 5-digit ZIP code.";
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['status' => 'failed', 'errors' => $errors]);
            return;
        }

        try {
            // Save lead details
            $leadId = Lead::createDraft([
                'service' => $service,
                'dynamic_questions' => json_encode($dynamicQuestions, JSON_UNESCAPED_SLASHES),
                'home_ownership' => $homeOwnership,
                'timeline' => $timeline,
                'hiring_status' => $hiringStatus,
                'zip_code' => $zipCode,
                'description' => $description
            ]);

            Lead::logAction($leadId, 'draft_created', "Lead wizard options logged. ZIP: {$zipCode}");

            // Initiate API call
            $apiClient = new ApiClient();
            $response = $apiClient->ping($leadId, [
                'service' => $service,
                'dynamic_questions' => json_encode($dynamicQuestions, JSON_UNESCAPED_SLASHES),
                'home_ownership' => $homeOwnership,
                'timeline' => $timeline,
                'hiring_status' => $hiringStatus,
                'zip_code' => $zipCode,
                'description' => $description
            ]);

            if (($response['status'] ?? '') === 'success') {
                $leadToken = $response['lead_token'] ?? '';
                Lead::updateStatus($leadId, 'ping_success', $leadToken);
                Lead::logAction($leadId, 'ping_success', "Ping request accepted. Provider list generated.");
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['current_lead_id'] = $leadId;
                $_SESSION['current_lead_token'] = $leadToken;

                echo json_encode([
                    'status' => 'success',
                    'lead_id' => $leadId,
                    'lead_token' => $leadToken,
                    'providers' => $response['providers'] ?? []
                ]);
            } else {
                $reason = $response['reason'] ?? 'No matches found in this region.';
                Lead::updateStatus($leadId, 'ping_failed');
                Lead::logAction($leadId, 'ping_rejected', "Ping rejected. Reason: {$reason}");
                echo json_encode([
                    'status' => 'rejected',
                    'reason' => $reason
                ]);
            }
        } catch (Exception $e) {
            Logger::error("Ping endpoint error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'failed', 'errors' => ['An error occurred while processing your request. Please try again.']]);
        }
    }

    /**
     * Handle the AJAX Post request (PII submission details)
     */
    public function handlePost(): void {
        header('Content-Type: application/json');

        if (!RateLimiter::check()) {
            http_response_code(429);
            echo json_encode(['status' => 'failed', 'errors' => ['Too many requests. Please wait and try again.']]);
            return;
        }

        $inputData = json_decode(file_get_contents('php://input'), true);
        if (!$inputData) {
            http_response_code(400);
            echo json_encode(['status' => 'failed', 'errors' => ['Invalid input format.']]);
            return;
        }

        if (!CSRF::validateToken($inputData['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(['status' => 'failed', 'errors' => ['Security verification failed. Please reload the page.']]);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $leadId = $_SESSION['current_lead_id'] ?? (int)($inputData['lead_id'] ?? 0);
        $leadToken = $_SESSION['current_lead_token'] ?? trim($inputData['lead_token'] ?? '');

        if (!$leadId || empty($leadToken)) {
            http_response_code(400);
            echo json_encode(['status' => 'failed', 'errors' => ['Lead verification session expired. Please start over.']]);
            return;
        }

        // Contact and Address validations
        $errors = [];
        $firstName = trim($inputData['first_name'] ?? '');
        $lastName = trim($inputData['last_name'] ?? '');
        $phone = trim($inputData['phone'] ?? '');
        $email = trim($inputData['email'] ?? '');
        $streetAddress = trim($inputData['street_address'] ?? '');
        $city = trim($inputData['city'] ?? '');
        $state = trim($inputData['state'] ?? '');
        $zipCode = trim($inputData['zip_code'] ?? '');
        $tcpaChecked = filter_var($inputData['tcpa_consent'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (empty($firstName)) $errors[] = "First name is required.";
        if (empty($lastName)) $errors[] = "Last name is required.";
        
        // Match standard US 10-digit number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone) || strlen($cleanPhone) < 10) {
            $errors[] = "Please enter a valid 10-digit phone number.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
        if (empty($streetAddress)) $errors[] = "Street address is required.";
        if (empty($city)) $errors[] = "City is required.";
        if (empty($state)) $errors[] = "State name is required.";
        if (!$tcpaChecked) $errors[] = "You must accept the TCPA communications consent checkbox.";

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['status' => 'failed', 'errors' => $errors]);
            return;
        }

        try {
            // Save PII to database immediately before the cURL request to ensure contact details are never lost
            Lead::updatePII($leadId, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'street_address' => $streetAddress,
                'city' => $city,
                'state' => $state,
                'zip_code' => $zipCode
            ], 'ping_success');

            Lead::logAction($leadId, 'post_initiated', "PII values collected. Posting payload.");

            $apiClient = new ApiClient();
            $response = $apiClient->post($leadId, $leadToken, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $cleanPhone,
                'email' => $email,
                'street_address' => $streetAddress,
                'city' => $city,
                'state' => $state,
                'zip_code' => $zipCode
            ]);

            if (($response['status'] ?? '') === 'success') {
                Lead::updatePII($leadId, [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'email' => $email,
                    'street_address' => $streetAddress,
                    'city' => $city,
                    'state' => $state,
                    'zip_code' => $zipCode
                ], 'posted');

                Lead::logAction($leadId, 'posted_success', "Lead successfully processed and posted to API partner.");

                $_SESSION['success_lead_id'] = $leadId;
                
                // Clear active lead sessions
                unset($_SESSION['current_lead_id']);
                unset($_SESSION['current_lead_token']);

                $redirectUrl = $response['redirect_url'] ?? '';
                if (empty($redirectUrl)) {
                    $redirectUrl = '/success';
                }
                if (str_starts_with($redirectUrl, '/')) {
                    $baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
                    if ($baseDir === '/' || $baseDir === '\\') {
                        $baseDir = '';
                    }
                    $redirectUrl = $baseDir . $redirectUrl;
                }
                echo json_encode([
                    'status' => 'success',
                    'redirect_url' => $redirectUrl
                ]);
            } else {
                $postErrors = $response['errors'] ?? ['Lead post failed at endpoints.'];
                Lead::updatePII($leadId, [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'email' => $email,
                    'street_address' => $streetAddress,
                    'city' => $city,
                    'state' => $state,
                    'zip_code' => $zipCode
                ], 'post_failed');

                Lead::logAction($leadId, 'posted_failed', "Lead post rejected: " . implode(', ', $postErrors));
                echo json_encode([
                    'status' => 'failed',
                    'errors' => $postErrors
                ]);
            }
        } catch (Exception $e) {
            Logger::error("Post endpoint error: " . $e->getMessage());
            
            // On exception, save contact details and mark as post_failed so it is in the database
            try {
                Lead::updatePII($leadId, [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'email' => $email,
                    'street_address' => $streetAddress,
                    'city' => $city,
                    'state' => $state,
                    'zip_code' => $zipCode
                ], 'post_failed');
                Lead::logAction($leadId, 'post_exception', "Internal error: " . $e->getMessage());
            } catch (Exception $dbEx) {
                Logger::error("Fallback updatePII failed on exception: " . $dbEx->getMessage());
            }

            http_response_code(500);
            echo json_encode(['status' => 'failed', 'errors' => ['Internal server error during final submission.']]);
        }
    }

    /**
     * Show success thank-you landing page
     */
    public function showSuccess(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $leadId = $_SESSION['success_lead_id'] ?? null;
        
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
        }
        
        require dirname(__DIR__) . '/views/success.php';
    }

    /**
     * Show Terms & Conditions page
     */
    public function showTerms(): void {
        require dirname(__DIR__) . '/views/terms.php';
    }

    /**
     * Show Privacy Policy page
     */
    public function showPrivacy(): void {
        require dirname(__DIR__) . '/views/privacy.php';
    }
}
