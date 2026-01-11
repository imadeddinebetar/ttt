<?php

namespace App\Models;

use App\Core\Model as BaseModel;

class History extends BaseModel
{
   protected $table = 'oil_change_history';

   public function getHistoryByVehicleId($data): array
   {
      return $this->select("SELECT * FROM {$this->table} WHERE vehicle_id = :vehicle_id ORDER BY oil_date ASC", $data);
   }

   public function getHistoryByVehicleVin($data): array
   {
      return $this->select("SELECT * FROM {$this->table} WHERE vin LIKE :vin ORDER BY entry_date ASC", $data);
   }

   public function importHistory($data): void
   {
      $this->bulkInsert($data);
   }

   public function createHistory($data): array
   {
      return $this->insert($data);
   }
}
