<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class SeccionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT s.*, g.nombre as grado_nombre 
                FROM secciones s 
                LEFT JOIN grados g ON s.grado_id = g.id 
                ORDER BY g.orden, s.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT s.*, g.id as grado_id, g.nombre as grado_nombre 
                FROM secciones s 
                LEFT JOIN grados g ON s.grado_id = g.id 
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO secciones (nombre, grado_id, cupo_maximo, estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['grado_id'],
            $datos['cupo_maximo'],
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE secciones SET nombre = ?, grado_id = ?, cupo_maximo = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['grado_id'],
            $datos['cupo_maximo'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE secciones SET estado = 'eliminado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerActivas(): array
    {
        $sql = "SELECT s.*, g.nombre as grado_nombre 
                FROM secciones s 
                LEFT JOIN grados g ON s.grado_id = g.id 
                WHERE s.estado = 'activo' 
                ORDER BY g.orden, s.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCupoActual(int $seccion_id): int
    {
        $sql = "SELECT COUNT(*) FROM estudiantes WHERE seccion_id = ? AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorGrado(int $grado_id): array
    {
        $sql = "SELECT * FROM secciones WHERE grado_id = ? AND estado = 'activo' ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$grado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

