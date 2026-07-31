<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class MateriaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM materias ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM materias WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO materias (nombre, descripcion, estado) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE materias SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE materias SET estado = 'eliminado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerActivas(): array
    {
        $sql = "SELECT * FROM materias WHERE estado = 'activo' ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorGrado(int $grado_id): array
    {
        $sql = "SELECT DISTINCT m.* 
                FROM materias m 
                INNER JOIN asignaciones a ON m.id = a.materia_id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                WHERE s.grado_id = ? AND m.estado = 'activo' AND a.estado = 'activa'
                ORDER BY m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$grado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}