<?php
/**
 * Setting Model
 */

require_once dirname(__DIR__) . '/includes/Database.php';

class Setting {
    /**
     * Get single configuration key value
     */
    public static function get(string $key, $default = null): ?string {
        if (!Database::isSaveEnabled()) {
            return $default;
        }
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key` = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            return $row ? $row['value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Set configuration key value
     */
    public static function set(string $key, ?string $value): void {
        if (!Database::isSaveEnabled()) {
            return;
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->execute([$key, $value, $value]);
    }

    /**
     * Get all configuration settings as associative array
     */
    public static function getAll(): array {
        if (!Database::isSaveEnabled()) {
            return [];
        }
        $db = Database::getConnection();
        $settings = [];
        try {
            $stmt = $db->query("SELECT `key`, `value` FROM settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['key']] = $row['value'];
            }
        } catch (Exception $e) {
            Logger::error("Setting::getAll query failed: " . $e->getMessage());
        }
        return $settings;
    }
}
