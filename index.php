<?php
require __DIR__ . '/config/Database.php';


$url = explode('/', $_GET['url'] ?? 'home/index');
$controller = $url[0] . 'Controller';
$action = $url[1] ?? 'index';

if (file_exists(__DIR__ . "/controllers/$controller.php")) {
    require __DIR__ . "/controllers/$controller.php";
    if (in_array($action, ['delete', 'edit', 'update']) && isset($url[2])) {
        (new $controller())->$action($url[2]); // Pass ID parameter
    } else {
        (new $controller())->$action();
    }
} else {
    http_response_code(404);
    die("Page not found");
}
if ($url[0] === 'admin') {
    $controller = new AdminController();
    
    if ($url[1] === 'edit' && isset($url[2])) {
        $controller->edit($url[2]);
        exit;
    }
    
    if ($url[1] === 'update' && isset($url[2])) {
        $controller->update($url[2]);
        exit;
    }
    
    if ($url[1] === 'delete' && isset($url[2])) {
        $controller->delete($url[2]);
        exit;
    }
}
?>