<?php
require __DIR__ . '/../app/config/config.php';

// Parse URL path
$path = str_replace('/gamyy/public/admin/', '', $_SERVER['REQUEST_URI']);
$segments = explode('/', trim($path, '/'));

// Route mapping
$action = $segments[0] ?? 'dashboard';
$subaction = $segments[1] ?? null;
$id = $segments[2] ?? null;

// StreamController routes
if ($action === 'streams') {
    require APP_PATH . 'controllers/StreamController.php';
    $controller = new StreamController();

    switch(true) {
        case ($subaction === 'add' && $_SERVER['REQUEST_METHOD'] === 'GET'):
            $controller->create();
            break;
        case ($subaction === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST'):
            $controller->store();
            break;
        case ($subaction === 'delete' && isset($id)):
            $controller->delete($id);
            break;
        case ($subaction === 'live'): // NOUVELLE ROUTE LIVE
            $controller->showLive();
            break;
        default:
            $controller->index();
    }
    exit;
}

// Default to AdminController
require APP_PATH . 'controllers/AdminController.php';
$controller = new AdminController();
$controller->dashboard();