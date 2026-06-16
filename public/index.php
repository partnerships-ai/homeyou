<?php
/**
 * Front Controller and URL Router
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Safe for production, logged internally

require_once dirname(__DIR__) . '/includes/Logger.php';

// Global exception handler
set_exception_handler(function ($e) {
    Logger::error("Unhandled Exception: " . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['status' => 'failed', 'errors' => ['An internal server error occurred.']]);
    } else {
        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1><p>An unexpected error occurred. Please try again later.</p>";
    }
});

// Parse the request path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Clean path relative to project's public folder (supports XAMPP subdirectory layouts)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = dirname($scriptName);

if ($baseDir !== '/' && strpos($path, $baseDir) === 0) {
    $path = substr($path, strlen($baseDir));
}

// Strip /public prefix if present (e.g. under subdirectory hosting or redirected route fallbacks)
if (strpos($path, '/public') === 0) {
    $path = substr($path, 7);
}

// Clean trailing slashes
if (empty($path)) {
    $path = '/';
}
if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

// Define available route mappings
$routes = [
    '/' => ['FunnelController', 'showFunnel'],
    '/api/ping' => ['FunnelController', 'handlePing'],
    '/api/post' => ['FunnelController', 'handlePost'],
    '/success' => ['FunnelController', 'showSuccess'],
    '/terms' => ['FunnelController', 'showTerms'],
    '/privacy' => ['FunnelController', 'showPrivacy'],
    '/admin' => ['AdminController', 'showDashboard'],
    '/admin/leads' => ['AdminController', 'showLeads'],
    '/admin/lead-details' => ['AdminController', 'getLeadDetails'],
    '/admin/export' => ['AdminController', 'exportCSV'],
    '/admin/settings' => ['AdminController', 'showSettings'],
    '/admin/login' => ['AdminController', 'showLogin'],
    '/admin/logout' => ['AdminController', 'logout'],
];

if (isset($routes[$path])) {
    list($controllerClass, $method) = $routes[$path];
    
    // Auto require controller file
    require_once dirname(__DIR__) . "/controllers/{$controllerClass}.php";
    
    $controller = new $controllerClass();
    $controller->$method();
} else {
    // 404 handler
    Logger::warning("404 Route Not Found", ['path' => $path]);
    
    if (str_starts_with($path, '/api/')) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['status' => 'failed', 'errors' => ['API endpoint not found.']]);
    } else {
        http_response_code(404);
        echo "<div style='font-family: sans-serif; text-align: center; padding: 50px;'>";
        echo "<h1 style='font-size: 48px; color: #0F172A;'>404</h1>";
        echo "<p style='color: #64748B;'>The requested page '<strong>" . htmlspecialchars($path) . "</strong>' was not found.</p>";
        echo "<a href='" . htmlspecialchars($baseDir ?: '/') . "' style='color: #2563EB; text-decoration: none;'>Return Home</a>";
        echo "</div>";
    }
}
