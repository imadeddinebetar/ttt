<?php

namespace App\Models;

use App\Core\Model as BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function getAllUsers()
    {
        return $this->select("SELECT * FROM {$this->table}");
    }

    public function getUserByUsername(string $username): array
    {
        return $this->select("SELECT * FROM {$this->table} WHERE username like :username", ['username' => $username]);
    }
}
