<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{
    public function index()
    {
        $user = new User();
        $users = $user->getAllUsers();
        view('users', ['users' => $users]);
    }

    public function show($id)
    {
        $user = new User();
        $userData = $user->getUserById($id);
        if ($userData) {
            view('users-show', ['user' => $userData]);
        } else {
            http_response_code(404);
        }
    }
}
