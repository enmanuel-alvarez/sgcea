<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class InstitucionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM instituciones ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM instituciones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO instituciones (nombre, codigo_dependencia, direccion, telefono, email, director_nombre, director_cedula) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['codigo_dependencia'] ?? null,
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['director_nombre'] ?? null,
            $datos['director_cedula'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE instituciones 
                SET nombre = ?, 
                    codigo_dependencia = ?, 
                    direccion = ?, 
                    telefono = ?, 
                    email = ?, 
                    director_nombre = ?, 
                    director_cedula = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['codigo_dependencia'] ?? null,
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['director_nombre'] ?? null,
            $datos['director_cedula'] ?? null,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM instituciones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function contarTotal(): int
    {
        $sql = "SELECT COUNT(*) as total FROM instituciones";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }
}

