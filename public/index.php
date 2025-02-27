<?php
// Security headers (for CSP, clickjacking, etc.)
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");

// Cache static assets for 1 day
if (preg_match('/\.(?:css|js)$/', $_SERVER['REQUEST_URI'])) {
    header('Cache-Control: public, max-age=86400');
}

// Autoloader: Automatically load classes from the /app directory
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../app/controllers/';
    // Replace namespace separators with directory separators
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Simple routing: parse the URL to decide which controller and action to call
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// For example, using query parameters: ?controller=job&action=index
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'JobController';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Include a basic Router utility. 
// We could encapsulate routing logic into a dedicated class (e.g., app/utils/Router.php) if the project grows. 
// For now, the inline router is sufficient.
if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        // Dispatch the request to the specified action
        $controller->{$action}();
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Action not found";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Controller not found";
}
