<?php

use App\Controllers\UserController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/' && $method === 'GET') {
    echo "Welcome to the Home Page!";
} elseif (($uri === '/users' || $uri === '/users/') && $method === 'GET') {
    $controller = new UserController();
    $controller->index();
} elseif (preg_match('#^/users/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $controller = new UserController();
    $controller->show($matches[1]);
} else {
    http_response_code(404);
    echo "404 Not Found";
}
