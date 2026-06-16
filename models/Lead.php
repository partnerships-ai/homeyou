<?php
/**
 * Lead Model
 */

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/Logger.php';

class Lead {
    /**
     * Create a draft lead entry (qualification details only)
     */
    public static function createDraft(array $data): int {
        if (!Database::isSaveEnabled()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            $mockId = time(); // Use timestamp as mock ID
            $_SESSION['mock_lead'] = [
                'id' => $mockId,
                'service' => $data['service'] ?? '',
                'dynamic_questions' => $data['dynamic_questions'] ?? null,
                'home_ownership' => $data['home_ownership'] ?? '',
                'timeline' => $data['timeline'] ?? '',
                'hiring_status' => $data['hiring_status'] ?? '',
                'zip_code' => $data['zip_code'] ?? '',
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s')
            ];
            return $mockId;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO leads (service, dynamic_questions, home_ownership, timeline, hiring_status, zip_code, description, status) 
            VALUES (:service, :dynamic_questions, :home_ownership, :timeline, :hiring_status, :zip_code, :description, 'draft')
        ");
        $stmt->execute([
            ':service' => $data['service'] ?? '',
            ':dynamic_questions' => $data['dynamic_questions'] ?? null,
            ':home_ownership' => $data['home_ownership'] ?? '',
            ':timeline' => $data['timeline'] ?? '',
            ':hiring_status' => $data['hiring_status'] ?? '',
            ':zip_code' => $data['zip_code'] ?? '',
            ':description' => $data['description'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Update status of lead
     */
    public static function updateStatus(int $id, string $status, ?string $token = null): void {
        if (!Database::isSaveEnabled()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            if (isset($_SESSION['mock_lead']) && $_SESSION['mock_lead']['id'] == $id) {
                $_SESSION['mock_lead']['status'] = $status;
                if ($token !== null) {
                    $_SESSION['mock_lead']['lead_token'] = $token;
                }
            }
            return;
        }

        $db = Database::getConnection();
        if ($token !== null) {
            $stmt = $db->prepare("UPDATE leads SET status = :status, lead_token = :token WHERE id = :id");
            $stmt->execute([':status' => $status, ':token' => $token, ':id' => $id]);
        } else {
            $stmt = $db->prepare("UPDATE leads SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
        }
    }

    /**
     * Update PII contact information and set status
     */
    public static function updatePII(int $id, array $data, string $status): void {
        if (!Database::isSaveEnabled()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            if (isset($_SESSION['mock_lead']) && $_SESSION['mock_lead']['id'] == $id) {
                $_SESSION['mock_lead']['first_name'] = $data['first_name'] ?? '';
                $_SESSION['mock_lead']['last_name'] = $data['last_name'] ?? '';
                $_SESSION['mock_lead']['phone'] = $data['phone'] ?? '';
                $_SESSION['mock_lead']['email'] = $data['email'] ?? '';
                $_SESSION['mock_lead']['street_address'] = $data['street_address'] ?? '';
                $_SESSION['mock_lead']['city'] = $data['city'] ?? '';
                $_SESSION['mock_lead']['state'] = $data['state'] ?? '';
                $_SESSION['mock_lead']['zip_code'] = $data['zip_code'] ?? '';
                $_SESSION['mock_lead']['status'] = $status;
            }
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE leads 
            SET first_name = :first_name, 
                last_name = :last_name, 
                phone = :phone, 
                email = :email, 
                street_address = :street_address, 
                city = :city, 
                state = :state, 
                status = :status 
            WHERE id = :id
        ");
        $stmt->execute([
            ':first_name' => $data['first_name'] ?? '',
            ':last_name' => $data['last_name'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':email' => $data['email'] ?? '',
            ':street_address' => $data['street_address'] ?? '',
            ':city' => $data['city'] ?? '',
            ':state' => $data['state'] ?? '',
            ':status' => $status,
            ':id' => $id
        ]);
    }

    /**
     * Log a lead's specific processing action
     */
    public static function logAction(int $leadId, string $action, string $message): void {
        if (!Database::isSaveEnabled()) {
            Logger::info("Lead Action Mock Log - Lead ID: {$leadId}, Action: {$action}, Message: {$message}");
            return;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO lead_logs (lead_id, action, message) VALUES (?, ?, ?)");
            $stmt->execute([$leadId, $action, $message]);
        } catch (Exception $e) {
            Logger::error("Database Lead Log failure: " . $e->getMessage());
        }
    }

    /**
     * Find single lead by ID
     */
    public static function find(int $id): ?array {
        if (!Database::isSaveEnabled()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            if (isset($_SESSION['mock_lead']) && $_SESSION['mock_lead']['id'] == $id) {
                return $_SESSION['mock_lead'];
            }
            return null;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        $lead = $stmt->fetch();
        return $lead ? $lead : null;
    }

    /**
     * Get all leads ordered by date
     */
    public static function getAll(): array {
        if (!Database::isSaveEnabled()) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            return isset($_SESSION['mock_lead']) ? [$_SESSION['mock_lead']] : [];
        }

        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM leads ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Get processing logs for a specific lead
     */
    public static function getLogs(int $leadId): array {
        if (!Database::isSaveEnabled()) {
            return [];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM lead_logs WHERE lead_id = ? ORDER BY created_at ASC");
        $stmt->execute([$leadId]);
        return $stmt->fetchAll();
    }

    /**
     * Get API payload logs for a specific lead
     */
    public static function getApiLogs(int $leadId): array {
        if (!Database::isSaveEnabled()) {
            return [];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.api_type, r.url, r.request_body, r.headers, r.created_at as req_time, 
                   s.status_code, s.response_body, s.created_at as res_time
            FROM api_requests r
            LEFT JOIN api_responses s ON r.lead_id = s.lead_id AND r.api_type = s.api_type
            WHERE r.lead_id = ?
            ORDER BY r.created_at ASC
        ");
        $stmt->execute([$leadId]);
        return $stmt->fetchAll();
    }

    /**
     * Get statistics for admin panel dashboard
     */
    public static function getStats(): array {
        if (!Database::isSaveEnabled()) {
            return [
                'total' => 0,
                'success' => 0,
                'rejected' => 0,
                'revenue' => 0.00,
                'conv_rate' => 0.0
            ];
        }

        $db = Database::getConnection();
        $stats = [
            'total' => 0,
            'success' => 0,
            'rejected' => 0,
            'revenue' => 0.00,
            'conv_rate' => 0.0
        ];
        
        try {
            $res = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'posted' THEN 1 ELSE 0 END) as success,
                    SUM(CASE WHEN status IN ('ping_failed', 'post_failed') THEN 1 ELSE 0 END) as rejected
                FROM leads
            ")->fetch();
            
            if ($res) {
                $stats['total'] = (int)$res['total'];
                $stats['success'] = (int)$res['success'];
                $stats['rejected'] = (int)$res['rejected'];
                // Assume $35.00 flat revenue per verified successful post payout
                $stats['revenue'] = $stats['success'] * 35.00;
                
                if ($stats['total'] > 0) {
                    $stats['conv_rate'] = round(($stats['success'] / $stats['total']) * 100, 1);
                }
            }
        } catch (Exception $e) {
            Logger::error("Stats query failed: " . $e->getMessage());
        }

        return $stats;
    }
}
