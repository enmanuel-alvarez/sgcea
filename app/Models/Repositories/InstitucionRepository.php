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
        $sql = "INSERT INTO instituciones (nombre, direccion, telefono, email) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE instituciones SET nombre = ?, direccion = ?, telefono = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
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
