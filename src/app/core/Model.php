<?php

namespace App\Core;

class Model
{
    private $connection;
    protected $table;
    public function __construct()
    {
        $this->connection = \App\Core\DB::getInstance()->getConnection();
    }

    protected function insert(array $data): array
    {
        try {
            $columns = array_keys($data);
            $placeholders = array_map(fn($c) => ':' . $c, $columns);
            $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                return [];
            }
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();

            $id = (int) $this->connection->lastInsertId();

            $select = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
            if ($select === false) {
                return [];
            }
            $select->execute([':id' => $id]);
            $result = $select->fetch(\PDO::FETCH_ASSOC);

            return $result ?: [];
        } catch (\PDOException $e) {
            // Optional: log error
            return [];
        }
    }

    public function update(array $data): array
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('ID is required for update');
        }
        try {
            $id = $data['id'];
            unset($data['id']);

            $setClauses = [];
            foreach ($data as $key => $value) {
                $setClauses[] = $key . ' = :' . $key;
            }
            $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $setClauses) . ' WHERE id = :id';

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                return [];
            }
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $select = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
            if ($select === false) {
                return [];
            }
            $select->execute([':id' => $id]);
            $result = $select->fetch(\PDO::FETCH_ASSOC);

            return $result ?: [];
        } catch (\PDOException $e) {
            // Optional: log error
            return [];
        }
    }

    public function delete(array $data): bool
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException('ID is required for delete');
        }
        try {
            $id = $data['id'];

            $stmt = $this->connection->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
            if ($stmt === false) {
                return false;
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            // Optional: log error
            return false;
        }
    }

    public function select(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                return [];
            }
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Optional: log error message, e.g. error_log($e->getMessage());
            return [];
        }
    }
}
