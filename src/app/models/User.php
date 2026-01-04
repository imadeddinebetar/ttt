<?php

namespace App\Models;

use App\Models\Model as BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function getAllUsers()
    {
        // Logic to retrieve all users from the database
        return $this->db_get("SELECT * FROM $this->table");
    }

    public function getUserById($id)
    {
        // Logic to retrieve a user by ID from the database
        $results = $this->db_get("SELECT * FROM $this->table WHERE id = $id");
        return $results ? $results[0] : null;
    }
}
