<?php
require __DIR__ . '/../app/config/config.php';
if (isset($_GET['url']) && $_GET['url'] === 'streams') {
    require_once __DIR__ . '/../app/controllers/StreamController.php';
    $controller = new StreamController();
    $controller->index();
    exit; // Stop further execution
}
// Parse URL FIRST
$request = $_SERVER['REQUEST_URI'];
$base_path = '/gamyy/public/';
$path = str_replace($base_path, '', parse_url($request, PHP_URL_PATH));

// Handle chat/send route
if ($path === 'chat/send') {
    require APP_PATH . 'controllers/ChatController.php';
    (new ChatController())->sendMessage();
    exit;
}

// Handle streams/live route
if ($path === 'streams/live') {
    require APP_PATH . 'controllers/StreamController.php';
    $controller = new StreamController();
    $controller->live();
    exit;
}

// Default routing logic
$requestUri = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : '';
$requestUri = str_replace('public/', '', $requestUri);
$url = explode('/', rtrim($requestUri, '/'));

if (empty($url[0])) {
    $url = ['video', 'index'];
}

$controllerName = ucfirst(strtolower($url[0])) . 'Controller';
$actionName = isset($url[1]) ? strtolower($url[1]) : 'index';

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