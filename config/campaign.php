<?php
/**
 * Campaign and API Integration Settings
 */

if (!function_exists('env')) {
    /**
     * Parse and retrieve environment variables from .env
     */
    function env($key, $default = null) {
        static $env = null;
        if ($env === null) {
            $env = [];
            $envPath = dirname(__DIR__) . '/.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, '#') === 0) {
                        continue;
                    }
                    if (strpos($line, '=') !== false) {
                        list($k, $v) = explode('=', $line, 2);
                        $env[trim($k)] = trim($v);
                    }
                }
            }
        }
        return isset($env[$key]) ? $env[$key] : $default;
    }
}

return [
    'campaign_code' => env('CAMPAIGN_CODE', 'default_campaign_code'),
    'campaign_token' => env('CAMPAIGN_TOKEN', 'default_campaign_token_123'),
    'ping_url' => env('PING_URL', 'https://api.wiserleads.com/services/ping'),
    'post_url' => env('POST_URL', 'https://api.wiserleads.com/services/post'),
    'mock_mode' => filter_var(env('MOCK_MODE', 'true'), FILTER_VALIDATE_BOOLEAN),
    'admin_user' => env('ADMIN_USER', 'admin'),
    'admin_pass' => env('ADMIN_PASS', 'admin123'),
];
