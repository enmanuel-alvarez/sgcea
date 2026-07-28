<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class DocenteRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT d.*, u.correo 
                FROM docentes d 
                LEFT JOIN usuarios u ON d.usuario_id = u.id 
                ORDER BY d.apellido, d.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT d.*, u.id as usuario_id, u.correo 
                FROM docentes d 
                LEFT JOIN usuarios u ON d.usuario_id = u.id 
                WHERE d.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        $sql = "SELECT d.* FROM docentes d WHERE d.usuario_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO docentes (usuario_id, codigo, nombre, apellido, especialidad, telefono, titulo, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['usuario_id'],
            $datos['codigo'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['especialidad'],
            $datos['telefono'],
            $datos['titulo'],
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE docentes SET 
                codigo = ?, nombre = ?, apellido = ?, especialidad = ?, telefono = ?, titulo = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['codigo'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['especialidad'],
            $datos['telefono'],
            $datos['titulo'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE docentes SET estado = 'eliminado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarCodigoUnico(string $codigo, ?int $excluir_id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM docentes WHERE codigo = ?";
        if ($excluir_id) {
            $sql .= " AND id != ?";
        }
        $stmt = $this->db->prepare($sql);
        if ($excluir_id) {
            $stmt->execute([$codigo, $excluir_id]);
        } else {
            $stmt->execute([$codigo]);
        }
        return (int) $stmt->fetchColumn() === 0;
    }

    public function obtenerConAsignacionesActivas(): array
    {
        $sql = "SELECT DISTINCT d.* 
                FROM docentes d 
                INNER JOIN asignaciones a ON d.id = a.docente_id 
                WHERE a.estado = 'activa' AND d.estado = 'activo'
                ORDER BY d.apellido, d.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
