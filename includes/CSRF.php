<?php
/**
 * CSRF Protection Token Helper
 */

class CSRF {
    /**
     * Generate and store a CSRF token in the session if not present
     * 
     * @return string
     */
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate an incoming token against the stored session token
     * 
     * @param string|null $token
     * @return bool
     */
    public static function validateToken(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate HTML input tag with CSRF token
     * 
     * @return string
     */
    public static function getFormField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
