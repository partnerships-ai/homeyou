<?php
/**
 * Database Singleton Connection Wrapper
 */

class Database {
    private static ?PDO $instance = null;

    /**
     * Get instance of PDO connection
     * @return PDO
     * @throws Exception
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require dirname(__DIR__) . '/config/database.php';
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
            } catch (PDOException $e) {
                throw new Exception("Database connection error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * Check if database saving is enabled
     */
    public static function isSaveEnabled(): bool {
        $config = require dirname(__DIR__) . '/config/database.php';
        return isset($config['save_enabled']) ? (bool)$config['save_enabled'] : true;
    }
}

