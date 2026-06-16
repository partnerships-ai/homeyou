<?php
/**
 * Database Initialization Script
 */

$dbConfig = require __DIR__ . '/config/database.php';

echo "----------------------------------------\n";
echo "Starting Lead Funnel Database Installer\n";
echo "----------------------------------------\n";

try {
    // Connect to MySQL server (without database name first)
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "Status: Connected to MySQL server successfully.\n";
    
    // Create database dynamically
    $dbName = $dbConfig['name'];
    echo "Status: Creating database `{$dbName}` if it doesn't exist...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Connect to the specific target database
    $dsnDb = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbName};charset=utf8mb4";
    $pdoDb = new PDO($dsnDb, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    // Read schema.sql content
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found in " . __DIR__);
    }
    
    $sql = file_get_contents($schemaFile);
    
    echo "Status: Importing schema.sql into database `{$dbName}`...\n";
    $pdoDb->exec($sql);
    
    echo "Success: Database and tables initialized successfully.\n";
    echo "----------------------------------------\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "Please ensure MySQL is running and that user credentials in .env are correct.\n";
    echo "----------------------------------------\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "----------------------------------------\n";
    exit(1);
}
