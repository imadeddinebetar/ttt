<?php

namespace App\Models;

use App\Core\Model as BaseModel;

class Driver extends BaseModel
{
    protected $table = 'drivers';

    public function createDriver($data):array
    {
        return $this->insert($data);
    }

    public function getDriver($params):array{
        return $this->select("SELECT * FROM {$this->table} WHERE phone1 = :phone1 OR email = :email", $params);   
    }

}
