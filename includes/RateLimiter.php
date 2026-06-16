<?php
/**
 * Session-based sliding-window rate limiter
 */

class RateLimiter {
    private static int $maxRequests = 45; // Max allowed requests
    private static int $timeWindow = 60;  // Window duration in seconds (1 minute)

    /**
     * Check if the request is within rate limit limits
     * 
     * @return bool True if permitted, False if limit exceeded
     */
    public static function check(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        if (!isset($_SESSION['rate_limit_timestamps'])) {
            $_SESSION['rate_limit_timestamps'] = [];
        }

        // Clean up older timestamps outside the current window
        $_SESSION['rate_limit_timestamps'] = array_filter(
            $_SESSION['rate_limit_timestamps'],
            function($timestamp) use ($now) {
                return $timestamp > ($now - self::$timeWindow);
            }
        );

        if (count($_SESSION['rate_limit_timestamps']) >= self::$maxRequests) {
            return false;
        }

        // Append current request timestamp
        $_SESSION['rate_limit_timestamps'][] = $now;
        return true;
    }
}
