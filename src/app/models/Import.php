<?php

namespace App\Models;

use App\Core\Model as BaseModel;

class Import extends BaseModel
{
    protected $table = 'imports';

    public function getAllImports()
    {
        return $this->select("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    public function createImport(array $data): array
    {
        return $this->insert($data);
    }

    public function updateImport(array $data): array
    {
        return $this->update($data);
    }
}
