<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;
use App\Models\User;
use App\Core\Middleware;

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        Middleware::auth();
        $this->userModel = new User();
    }
    public function index()
    {
        $this->render('users/index', [
            'title' => 'User List',
            'users' => $this->userModel->getAllUsers()
        ]);
    }

    public function show($id)
    {
        $this->render('users/show', [
            'title' => 'User Details',
            'id' => $id
        ]);
    }
}
