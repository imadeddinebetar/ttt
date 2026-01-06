<?php
// bootstrap/app.php
require_once __DIR__ . '/../vendor/autoload.php'; // or your autoloader

use App\Core\Container;

// Create container
$container = new Container();

// Binding PDO instance
$container->singleton(\PDO::class, function () {
    return new \PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
        env('DB_USER'),
        env('DB_PASSWORD'),
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );
});



return $container;
