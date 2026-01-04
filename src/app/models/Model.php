<?php

namespace App\Models;

class Model
{
    private $dbConnection;
    protected $table;
    public function __construct()
    {
        $databaseConfig = config('database');

        $mysqlConfig = $databaseConfig['mysql'] ?? null;

        if ($mysqlConfig) {
            $this->dbConnection = mysqli_connect(
                $mysqlConfig['host'],
                $mysqlConfig['username'],
                $mysqlConfig['password'],
                $mysqlConfig['database'],
                $mysqlConfig['port'],
            );

            if (!$this->dbConnection) {
                die("MySQL Database connection failed. | " . mysqli_connect_error());
            }
        } else {
            die("No database configuration found.");
        }
    }

    protected function db_insert(array $data): array
    {
        $sql = "INSERT INTO " . $this->table;
        $columns = "";
        $values = "";
        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $values .= " '$value',";
        }
        $columns = rtrim($columns, ',');
        $values = rtrim($values, ',');
        $sql .= " ($columns) VALUES ($values); ";
        mysqli_query($this->dbConnection, $sql);
        $id =  mysqli_insert_id($this->dbConnection);
        $row = mysqli_query($this->dbConnection, "select * from $this->table where id = $id");
        $result =  mysqli_fetch_assoc($row);
        return $result;
    }

    protected function db_update(array $data, int $id): array
    {
        $sql = "UPDATE $this->table SET ";
        foreach ($data as $key => $value) {
            $sql .= "$key = '$value',";
        }
        $sql = rtrim($sql, ',');
        $sql .= " WHERE id=$id;";
        mysqli_query($this->dbConnection, $sql);
        $row = mysqli_query($this->dbConnection, "select * from $this->table where id = $id");
        $result =  mysqli_fetch_assoc($row);
        return $result;
    }

    protected function db_delete(int $id): bool
    {
        $sql = "DELETE FROM $this->table WHERE id=$id;";
        mysqli_query($this->dbConnection, $sql);
        return true;
    }

    protected function db_get(string $query): array
    {
        $result = [];
        $row = mysqli_query($this->dbConnection, $query);
        while ($data = mysqli_fetch_assoc($row)) {
            $result[] = $data;
        }
        return $result;
    }
}
