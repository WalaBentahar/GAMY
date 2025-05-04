<?php
require __DIR__ . '/../app/config/config.php';

// Sanitize and parse URL
$requestUri = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : '';
$requestUri = str_replace('public/', '', $requestUri);
$url = explode('/', rtrim($requestUri, '/'));

// Default to video/index if no segments
if (empty($url[0])) {
    $url = ['video', 'index'];
}

// Define controller and action (case-sensitive)
$controllerName = ucfirst(strtolower($url[0])) . 'Controller';
$actionName = isset($url[1]) ? strtolower($url[1]) : 'index';

// Load controller
$controllerFile = APP_PATH . "controllers/{$controllerName}.php";
if (file_exists($controllerFile)) {
    require $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
        } else {
            header('Location: ' . BASE_URL . 'error/not_found');
            exit;
        }
    } else {
        header('Location: ' . BASE_URL . 'error/not_found');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . 'error/not_found');
    exit;
}
?>