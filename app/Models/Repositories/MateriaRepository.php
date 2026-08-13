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
        $sql = "SELECT 
                    m.id,
                    m.codigo,
                    m.nombre,
                    m.creditos,
                    m.estado AS activo
                FROM materias m 
                ORDER BY m.nombre";
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
        $sql = "INSERT INTO materias (codigo, nombre, descripcion, creditos, estado) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['codigo'] ?? '',
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['creditos'] ?? 1,
            $datos['estado'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE materias SET codigo = ?, nombre = ?, descripcion = ?, creditos = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['codigo'] ?? '',
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['creditos'] ?? 1,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE materias SET estado = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerActivas(): array
    {
        $sql = "SELECT * FROM materias WHERE estado = 1 ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorGrado(int $grado_id): array
    {
        $sql = "SELECT DISTINCT m.* 
                FROM materias m 
                INNER JOIN asignaciones a ON m.id = a.materia_id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                WHERE s.grado_id = ? AND m.estado = 1 AND a.estado = 1
                ORDER BY m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$grado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}