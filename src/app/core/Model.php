<?php

namespace App\Core;

use \App\Core\DB;

class Model
{
    private $connection;
    protected $table;

    public function __construct()
    {
        $this->connection = DB::getInstance()->getConnection();
    }

    protected function getTableName()
    {
        return $this->table;
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
            dd($e->getMessage(), $sql, $data);
            return [];
        }
    }

    protected function bulkInsert(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        try {
            $this->connection->beginTransaction();

            // Columns from first row
            $columns = array_keys(reset($rows));

            // (?,?,?,?) placeholder
            $rowPlaceholder = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';

            // (?, ?, ?), (?, ?, ?), ...
            $placeholders = implode(',', array_fill(0, count($rows), $rowPlaceholder));

            $sql = 'INSERT INTO ' . $this->table .
                ' (' . implode(',', $columns) . ') VALUES ' . $placeholders;

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                $this->connection->rollBack();
                return 0;
            }

            // Flatten values
            $values = [];
            foreach ($rows as $row) {
                foreach ($columns as $column) {
                    $values[] = $row[$column];
                }
            }

            $stmt->execute($values);
            $this->connection->commit();

            return $stmt->rowCount(); // number of inserted rows

        } catch (\PDOException $e) {
            $this->connection->rollBack();
            throw $e; // do NOT swallow bulk errors
        }
    }

    protected function update(array $data): array
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
             dd($e->getMessage(), $sql, $data);
            return [];
        }
    }

    protected function delete(array $data): bool
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
            $stmt->execute(['id' => $id]);

            return true;
        } catch (\PDOException $e) {
            // Optional: log error
            return false;
        }
    }

    protected function select(string $sql, array $params = []): array
    {
        try {

            $stmt = $this->connection->prepare($sql);
            if ($stmt === false) {
                return [];
            }
            $stmt->execute($params);

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $results ?: [];
        } catch (\PDOException $e) {
            dd($e->getMessage(), $sql, $params);
            return [];
        }
    }
}
