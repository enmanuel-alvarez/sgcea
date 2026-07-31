<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class AsignacionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT a.*, 
                       u_doc.nombre as docente_nombre, u_doc.apellido as docente_apellido, 
                       m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN profesores p ON a.profesor_id = p.id 
                INNER JOIN usuarios u_doc ON p.usuario_id = u_doc.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT a.*, 
                       u_doc.nombre as docente_nombre, u_doc.apellido as docente_apellido, 
                       m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN profesores p ON a.profesor_id = p.id 
                INNER JOIN usuarios u_doc ON p.usuario_id = u_doc.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                WHERE a.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO asignaciones (profesor_id, materia_id, seccion_id, ano_academico, periodo, estado) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['profesor_id'],
            $datos['materia_id'],
            $datos['seccion_id'],
            $datos['ano_academico'],
            $datos['periodo'] ?? 'anual',
            $datos['estado'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE asignaciones SET profesor_id = ?, materia_id = ?, seccion_id = ?, 
                ano_academico = ?, periodo = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['profesor_id'],
            $datos['materia_id'],
            $datos['seccion_id'],
            $datos['ano_academico'],
            $datos['periodo'] ?? 'anual',
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE asignaciones SET estado = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerPorDocente(int $profesor_id): array
    {
        $sql = "SELECT a.*, m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                WHERE a.profesor_id = ? AND a.estado = 1
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorSeccionYMateria(int $seccion_id, int $materia_id): ?array
    {
        $sql = "SELECT a.* FROM asignaciones a 
                WHERE a.seccion_id = ? AND a.materia_id = ? AND a.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id, $materia_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function verificarDuplicado(int $profesor_id, int $materia_id, int $seccion_id, string $ano_academico): bool
    {
        $sql = "SELECT COUNT(*) FROM asignaciones 
                WHERE profesor_id = ? AND materia_id = ? AND seccion_id = ? AND ano_academico = ? AND estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profesor_id, $materia_id, $seccion_id, $ano_academico]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public function obtenerConPlanEvaluacion(int $profesor_id): array
    {
        $sql = "SELECT a.*, m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre,
                       CASE WHEN pe.id IS NOT NULL THEN 1 ELSE 0 END as tiene_plan
                FROM asignaciones a 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                LEFT JOIN planes_evaluacion pe ON a.id = pe.asignacion_id AND pe.estado = 1
                WHERE a.profesor_id = ? AND a.estado = 1
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}