<?php

namespace Src\Core;

use PDO;
use PDOException;
use PDOStatement;
use Exception;

/**
 * Clase Database - Singleton para conexión PDO
 */

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private array $config;

    private function __construct()
    {
        $this->config = require __DIR__ . '/../config/database.php';
        $this->connect();
    }

    private function connect(): void
    {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $this->config['host'],
            $this->config['database'],
            $this->config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE {$this->config['collation']}"
        ];

        try {
            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $options
            );
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                throw $e;
            }
            error_log("Error de conexión a BD: " . $e->getMessage());
            die("Error de conexión a la base de datos");
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return (int) $this->connection->lastInsertId();
    }

    public function update(string $table, int $id, array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $sets);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        return $this->query($sql, $data)->rowCount() > 0;
    }

    public function delete(string $table, int $id): bool
    {
        $sql = "DELETE FROM {$table} WHERE id = :id";
        return $this->query($sql, ['id' => $id])->rowCount() > 0;
    }

    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup(): void
    {
        throw new Exception("No se puede deserializar el singleton");
    }
}


