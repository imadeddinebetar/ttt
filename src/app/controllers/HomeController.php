<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;

class HomeController extends BaseController
{
    public function index($id)
    {
        $this->render('home/index', [
            'title' => 'Welcome to the Home Page',
            'id' => $id
        ]);
    }
}
