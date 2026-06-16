<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($requestUri, PHP_URL_PATH);

if (strpos($path, '/api') !== false) {
    require_once __DIR__ . '/public/index.php';
} else {
    header("Location: public/");
    exit;
}
