<?php

namespace App\Models;

use App\Core\Model as BaseModel;

class Vehicle extends BaseModel
{
    protected $table = 'vehicles';

    public function getVehicleById($data): array
    {
       return $this->select("SELECT * FROM {$this->table} WHERE id = :id", $data);
    }

    public function getVehicleByVin($data): array
    {
       return $this->select("SELECT * FROM {$this->table} WHERE vin LIKE :vin", $data);
    }

    public function updateVehicle(array $data): void
    {
        $this->update($data);
    }

    public function createVehicle(array $data): array{
        // dd($data);
        return $this->insert($data);
    }
    
}