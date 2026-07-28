<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;
use Exception;

class EstudianteRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT e.*, g.nombre as grado, s.nombre as seccion 
                FROM estudiantes e 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT e.*, g.id as grado_id, g.nombre as grado, s.id as seccion_id, s.nombre as seccion 
                FROM estudiantes e 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        $sql = "SELECT e.*, g.nombre as grado, s.nombre as seccion 
                FROM estudiantes e 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                WHERE e.usuario_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO estudiantes (usuario_id, codigo, nombre, apellido, fecha_nacimiento, genero, direccion, telefono, representante_nombre, representante_telefono, grado_id, seccion_id, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['usuario_id'],
            $datos['codigo'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['direccion'],
            $datos['telefono'],
            $datos['representante_nombre'],
            $datos['representante_telefono'],
            $datos['grado_id'],
            $datos['seccion_id'],
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE estudiantes SET 
                codigo = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, genero = ?, 
                direccion = ?, telefono = ?, representante_nombre = ?, representante_telefono = ?, 
                grado_id = ?, seccion_id = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['codigo'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['fecha_nacimiento'],
            $datos['genero'],
            $datos['direccion'],
            $datos['telefono'],
            $datos['representante_nombre'],
            $datos['representante_telefono'],
            $datos['grado_id'],
            $datos['seccion_id'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE estudiantes SET estado = 'eliminado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarCodigoUnico(string $codigo, ?int $excluir_id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM estudiantes WHERE codigo = ?";
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

    public function obtenerEstudiantesPorSeccion(int $seccion_id): array
    {
        $sql = "SELECT e.* FROM estudiantes e 
                WHERE e.seccion_id = ? AND e.estado = 'activo' 
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerConInscripcionActual(int $ano_lectivo): array
    {
        $sql = "SELECT DISTINCT e.*, g.nombre as grado, s.nombre as seccion 
                FROM estudiantes e 
                INNER JOIN inscripciones i ON e.id = i.estudiante_id 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                WHERE i.ano_lectivo = ? AND i.estado = 'activa' AND e.estado = 'activo'
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_lectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
