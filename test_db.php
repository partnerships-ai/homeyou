<?php
require_once __DIR__ . '/includes/Database.php';

header('Content-Type: text/plain');
echo "Testing Database Connection on Live Server...\n";
try {
    $db = Database::getConnection();
    echo "STATUS: SUCCESS!\n";
} catch (Exception $e) {
    echo "STATUS: FAILED!\n";
    echo "ERROR: " . $e->getMessage() . "\n";
}
