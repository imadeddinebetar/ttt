<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Request;
use App\Models\User;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        // Session::clear();
        Middleware::guest();
        $this->userModel = new User();
    }

    public function login()
    {
        $this->render('auth/login', [
            'title' => 'Login',
        ]);
    }

    public function auth()
    {
        // Middleware::rateLimit('login');

        $username = Request::sanitizeInput('username');
        $password = Request::sanitizeInput('password');

        $user = $this->userModel->getUserByUsername($username);

        if (!empty($user) && password_verify(trim($password), trim($user[0]['password']))) {
            Session::set('login', true);
            Session::set('user_id', $user[0]['id']);
            Session::set('first_name', $user[0]['first_name']);
            Session::set('last_name', $user[0]['last_name']);
            $this->redirect(''); // home page
        }

        $this->redirect('login');
    }
}
