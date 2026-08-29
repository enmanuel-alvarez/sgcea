<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class InscripcionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT i.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, 
                       g.nombre as grado, s.nombre as seccion
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN secciones s ON i.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                ORDER BY i.ano_academico DESC, u.apellido, u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT i.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido 
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT i.* FROM inscripciones i WHERE i.estudiante_id = ? ORDER BY i.ano_academico DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudianteYAno(int $estudiante_id, string $ano_academico): ?array
    {
        $sql = "SELECT i.* FROM inscripciones i WHERE i.estudiante_id = ? AND i.ano_academico = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_academico]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO inscripciones (estudiante_id, seccion_id, ano_academico, fecha_inscripcion, estado) 
                VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['seccion_id'],
            $datos['ano_academico'],
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE inscripciones SET seccion_id = ?, ano_academico = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['seccion_id'],
            $datos['ano_academico'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE inscripciones SET estado = 'retirado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarInscripcionActiva(int $estudiante_id, string $ano_academico): bool
    {
        $sql = "SELECT COUNT(*) FROM inscripciones 
                WHERE estudiante_id = ? AND ano_academico = ? AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_academico]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public function obtenerPorAnoLectivo(string $ano_academico): array
    {
        $sql = "SELECT i.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, 
                       u.cedula as codigo_estudiante, g.nombre as grado, s.nombre as seccion
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN secciones s ON i.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                WHERE i.ano_academico = ? AND i.estado = 'activo'
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_academico]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPorAnoLectivo(string $ano_academico): int
    {
        $sql = "SELECT COUNT(*) FROM inscripciones WHERE ano_academico = ? AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_academico]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerActivaPorEstudiante(int $estudiante_id): ?array
    {
        $sql = "SELECT i.*, s.nombre as seccion_nombre, s.grado_id, g.nombre as grado_nombre 
                FROM inscripciones i 
                INNER JOIN secciones s ON i.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                WHERE i.estudiante_id = ? AND (i.estado = 'activo' OR i.estado = 'activa' OR i.estado = '1' OR i.estado = 1) 
                ORDER BY i.id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Alias para compatibilidad
    public function obtenerInscripcionActivaPorEstudiante(int $estudiante_id): ?array
    {
        return $this->obtenerActivaPorEstudiante($estudiante_id);
    }
}
