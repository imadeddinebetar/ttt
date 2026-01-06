<?php

declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

use App\Core\Env;
use App\Core\Router;
use App\Core\Logger;

Env::load(__DIR__ . '/../.env');

$router = new Router();
$router->dispatch(trim($_SERVER['REQUEST_URI'], env('PROJECT_PREFIXE')), $_SERVER['REQUEST_METHOD']);

// ----------------- END OF FILE -----------------

// // public/index.php
// use App\Core\Router;

// $container = require_once __DIR__ . '/../bootstrap/app.php';

// // Create router with container
// $router = new Router($container);

// // Load routes
// require_once __DIR__ . '/../routes/web.php';

// // Dispatch
// $requestMethod = $_SERVER['REQUEST_METHOD'];
// $requestUri = $_SERVER['REQUEST_URI'];

// $router->dispatch($requestMethod, $requestUri);
