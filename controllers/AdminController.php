<?php
/**
 * Admin Panel Controller handles leads dashboard, grids, details overlays, settings, and logging
 */

require_once dirname(__DIR__) . '/models/Lead.php';
require_once dirname(__DIR__) . '/models/Setting.php';
require_once dirname(__DIR__) . '/includes/Logger.php';
require_once dirname(__DIR__) . '/includes/CSRF.php';

class AdminController {

    /**
     * Enforce authentication session check
     */
    private function checkAuth(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['admin_logged_in'])) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * Helper to perform subdirectory-aware redirects
     */
    private function redirect(string $path): void {
        $baseDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($baseDir === '/' || $baseDir === '\\') {
            $baseDir = '';
        }
        header("Location: " . $baseDir . $path);
        exit;
    }

    /**
     * Show/handle Admin login form page
     */
    public function showLogin(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!empty($_SESSION['admin_logged_in'])) {
            $this->redirect('/admin');
        }

        $csrfToken = CSRF::generateToken();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? null)) {
                $error = "CSRF security check failed.";
            } else {
                $user = trim($_POST['username'] ?? '');
                $pass = trim($_POST['password'] ?? '');

                $config = require dirname(__DIR__) . '/config/campaign.php';

                if ($user === $config['admin_user'] && $pass === $config['admin_pass']) {
                    $_SESSION['admin_logged_in'] = true;
                    Logger::info("Admin login success", ['username' => $user]);
                    $this->redirect('/admin');
                } else {
                    $error = "Invalid username or password.";
                    Logger::warning("Admin login failed attempt", ['username' => $user]);
                }
            }
        }

        require dirname(__DIR__) . '/views/admin/login.php';
    }

    /**
     * Terminate admin session
     */
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['admin_logged_in']);
        session_destroy();
        $this->redirect('/admin/login');
    }

    /**
     * Display Dashboard metrics panels
     */
    public function showDashboard(): void {
        $this->checkAuth();
        $stats = Lead::getStats();
        require dirname(__DIR__) . '/views/admin/dashboard.php';
    }

    /**
     * Display Leads collection lists
     */
    public function showLeads(): void {
        $this->checkAuth();
        $leads = Lead::getAll();
        require dirname(__DIR__) . '/views/admin/leads.php';
    }

    /**
     * Get details, action logs and API transaction bodies for specific lead (JSON)
     */
    public function getLeadDetails(): void {
        header('Content-Type: application/json');
        $this->checkAuth();

        $leadId = (int)($_GET['id'] ?? 0);
        if (!$leadId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid lead parameter ID.']);
            return;
        }

        $lead = Lead::find($leadId);
        if (!$lead) {
            http_response_code(404);
            echo json_encode(['error' => 'Lead object not found.']);
            return;
        }

        $logs = Lead::getLogs($leadId);
        $apiLogs = Lead::getApiLogs($leadId);

        $processedApiLogs = [];
        foreach ($apiLogs as $apiLog) {
            $processedApiLogs[] = [
                'api_type' => $apiLog['api_type'],
                'url' => $apiLog['url'],
                'request' => json_decode($apiLog['request_body'], true) ?? $apiLog['request_body'],
                'headers' => json_decode($apiLog['headers'], true) ?? $apiLog['headers'],
                'status_code' => (int)$apiLog['status_code'],
                'response' => json_decode($apiLog['response_body'], true) ?? $apiLog['response_body'],
                'req_time' => $apiLog['req_time'],
                'res_time' => $apiLog['res_time']
            ];
        }

        echo json_encode([
            'lead' => $lead,
            'logs' => $logs,
            'api_logs' => $processedApiLogs
        ]);
    }

    /**
     * Export all Leads list into CSV attachment
     */
    public function exportCSV(): void {
        $this->checkAuth();

        $leads = Lead::getAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=leads_export_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        
        // Write CSV Columns Header
        fputcsv($output, [
            'ID', 'Service', 'Dynamic Questions', 'Home Ownership', 'Timeline', 
            'Hiring Status', 'ZIP Code', 'Description', 'First Name', 'Last Name', 
            'Phone', 'Email', 'Street Address', 'City', 'State', 'Lead Token', 
            'Status', 'Created At'
        ]);

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead['id'],
                $lead['service'],
                $lead['dynamic_questions'],
                $lead['home_ownership'],
                $lead['timeline'],
                $lead['hiring_status'],
                $lead['zip_code'],
                $lead['description'],
                $lead['first_name'],
                $lead['last_name'],
                $lead['phone'],
                $lead['email'],
                $lead['street_address'],
                $lead['city'],
                $lead['state'],
                $lead['lead_token'],
                $lead['status'],
                $lead['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Render and save Administrative config overrides
     */
    public function showSettings(): void {
        $this->checkAuth();

        $csrfToken = CSRF::generateToken();
        $message = null;
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CSRF::validateToken($_POST['csrf_token'] ?? null)) {
                $error = "CSRF verification failed.";
            } else {
                $campaignCode = trim($_POST['campaign_code'] ?? '');
                $campaignToken = trim($_POST['campaign_token'] ?? '');
                $pingUrl = trim($_POST['ping_url'] ?? '');
                $postUrl = trim($_POST['post_url'] ?? '');
                $mockMode = isset($_POST['mock_mode']) ? '1' : '0';

                if (empty($campaignCode) || empty($campaignToken) || empty($pingUrl) || empty($postUrl)) {
                    $error = "All URL, token, and campaign code fields are required.";
                } else {
                    Setting::set('campaign_code', $campaignCode);
                    Setting::set('campaign_token', $campaignToken);
                    Setting::set('ping_url', $pingUrl);
                    Setting::set('post_url', $postUrl);
                    Setting::set('mock_mode', $mockMode);

                    $message = "Dynamic settings successfully updated.";
                    Logger::info("Admin settings updated.");
                }
            }
        }

        $settings = Setting::getAll();
        
        $config = require dirname(__DIR__) . '/config/campaign.php';
        $campaignCode = $settings['campaign_code'] ?? $config['campaign_code'];
        $campaignToken = $settings['campaign_token'] ?? $config['campaign_token'];
        $pingUrl = $settings['ping_url'] ?? $config['ping_url'];
        $postUrl = $settings['post_url'] ?? $config['post_url'];
        $mockMode = isset($settings['mock_mode']) 
            ? filter_var($settings['mock_mode'], FILTER_VALIDATE_BOOLEAN) 
            : $config['mock_mode'];

        require dirname(__DIR__) . '/views/admin/settings.php';
    }
}
