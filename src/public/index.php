<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

use App\Core\Logger;

App\Core\Env::load(__DIR__ . '/../.env');

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);
set_time_limit(0);

ini_set('display_errors', env('APP_DEBUG'));
ini_set('display_startup_errors', env('APP_DEBUG'));
error_reporting(E_ALL);

$app_dir = env('APP_DIR');
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$clean_uri = $uri;
if (!empty($app_dir) && str_starts_with($uri, $app_dir)) {
    $clean_uri = substr($uri, strlen($app_dir));
}
$router = new App\Core\Router();
$router->dispatch($clean_uri, $method);
