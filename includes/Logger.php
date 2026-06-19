<?php
/**
 * Structured JSON Logger
 */

class Logger {
    private static string $logFile = '';

    /**
     * Set up log file location
     */
    private static function init(): void {
        if (empty(self::$logFile)) {
            self::$logFile = dirname(__DIR__) . '/logs/app.json.log';
            $logDir = dirname(self::$logFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
        }
    }

    /**
     * General log entry writer
     * 
     * @param string $level Log category (INFO, WARNING, ERROR, DEBUG)
     * @param string $message Clear text explanation of the event
     * @param array $context Additional array values to dump
     */
    public static function log(string $level, string $message, array $context = []): void {
        self::init();
        $entry = [
            'timestamp' => date('c'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
        ];
        
        @file_put_contents(self::$logFile, json_encode($entry, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::log('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void {
        self::log('DEBUG', $message, $context);
    }
}
