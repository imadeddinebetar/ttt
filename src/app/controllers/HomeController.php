<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;
use App\Core\Middleware;
class HomeController extends BaseController
{

    public function __construct()
    {
        Middleware::auth();
    }

    public function index()
    {
        $this->render('home/index', [
            'title' => 'Home Page',
        ]);
    }
}