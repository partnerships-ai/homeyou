<?php
/**
 * Database Configuration Loader
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
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'name' => env('DB_NAME', 'home_leads'),
    'user' => env('DB_USER', 'root'),
    'pass' => env('DB_PASS', ''),
    'save_enabled' => env('DB_SAVE_ENABLED', 'true') !== 'false',
];

