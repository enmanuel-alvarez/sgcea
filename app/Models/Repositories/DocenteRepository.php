<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class DocenteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.cedula, u.email 
                FROM profesores p 
                INNER JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.estado = 1 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.cedula, u.email, u.estado as usuario_estado 
                FROM profesores p 
                INNER JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.cedula, u.email 
                FROM profesores p 
                INNER JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.usuario_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO profesores (usuario_id, especialidad, titulo, fecha_ingreso, estado) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['usuario_id'],
            $datos['especialidad'] ?? null,
            $datos['titulo'] ?? null,
            $datos['fecha_ingreso'] ?? date('Y-m-d'),
            $datos['estado'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE profesores SET 
                especialidad = ?, titulo = ?, fecha_ingreso = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['especialidad'] ?? null,
            $datos['titulo'] ?? null,
            $datos['fecha_ingreso'] ?? date('Y-m-d'),
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE profesores SET estado = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarCodigoUnico(string $codigo, ?int $excluir_id = null): bool
    {
        return true; // No aplica, la unicidad está en usuarios.cedula
    }

    public function obtenerConAsignacionesActivas(): array
    {
        $sql = "SELECT DISTINCT p.*, u.nombre, u.apellido 
                FROM profesores p 
                INNER JOIN usuarios u ON p.usuario_id = u.id 
                INNER JOIN asignaciones a ON p.id = a.profesor_id 
                WHERE a.estado = 1 AND p.estado = 1 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}