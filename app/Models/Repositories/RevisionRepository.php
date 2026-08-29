<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class RevisionRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
            return;
        }
        $this->db = Database::getInstance()->getConnection();
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO solicitudes_revision (estudiante_id, asignacion_id, plan_evaluacion_id, calificacion_id, motivo, estado) 
                VALUES (?, ?, ?, ?, ?, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['asignacion_id'],
            $datos['plan_evaluacion_id'] ?? null,
            $datos['calificacion_id'] ?? null,
            $datos['motivo']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT sr.*, m.nombre as materia_nombre, pe.nombre as evaluacion_nombre, c.nota as nota_actual
                FROM solicitudes_revision sr
                INNER JOIN asignaciones a ON sr.asignacion_id = a.id
                INNER JOIN materias m ON a.materia_id = m.id
                LEFT JOIN planes_evaluacion pe ON sr.plan_evaluacion_id = pe.id
                LEFT JOIN calificaciones c ON sr.calificacion_id = c.id
                WHERE sr.estudiante_id = ?
                ORDER BY sr.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorProfesor(int $profesor_id): array
    {
        $sql = "SELECT sr.*, m.nombre as materia_nombre, pe.nombre as evaluacion_nombre, c.nota as nota_actual,
                       u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, g.nombre as grado_nombre, sec.nombre as seccion_nombre
                FROM solicitudes_revision sr
                INNER JOIN asignaciones a ON sr.asignacion_id = a.id
                INNER JOIN materias m ON a.materia_id = m.id
                INNER JOIN secciones sec ON a.seccion_id = sec.id
                INNER JOIN grados g ON sec.grado_id = g.id
                INNER JOIN estudiantes e ON sr.estudiante_id = e.id
                INNER JOIN usuarios u ON e.usuario_id = u.id
                LEFT JOIN planes_evaluacion pe ON sr.plan_evaluacion_id = pe.id
                LEFT JOIN calificaciones c ON sr.calificacion_id = c.id
                WHERE a.profesor_id = ?
                ORDER BY sr.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT sr.*, a.profesor_id
                FROM solicitudes_revision sr
                INNER JOIN asignaciones a ON sr.asignacion_id = a.id
                WHERE sr.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function responder(int $id, string $estado, string $respuesta): bool
    {
        $sql = "UPDATE solicitudes_revision 
                SET estado = ?, respuesta = ?, fecha_respuesta = NOW() 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$estado, $respuesta, $id]);
    }

    public function contarPendientesPorProfesor(int $profesor_id): int
    {
        $sql = "SELECT COUNT(*) 
                FROM solicitudes_revision sr
                INNER JOIN asignaciones a ON sr.asignacion_id = a.id
                WHERE a.profesor_id = ? AND sr.estado = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return (int) $stmt->fetchColumn();
    }

    public function contarActivasPorEstudianteYAsignacion(int $estudiante_id, int $asignacion_id): int
    {
        $sql = "SELECT COUNT(*) FROM solicitudes_revision 
                WHERE estudiante_id = ? AND asignacion_id = ? AND estado IN ('pendiente', 'en_revision')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $asignacion_id]);
        return (int) $stmt->fetchColumn();
    }
}
