<?php
// routes/web.php
use App\Controllers\UserController;

$router->get('/users', UserController::class, 'index');
$router->get('/users/{id}', UserController::class, 'show');
