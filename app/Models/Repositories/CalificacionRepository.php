<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class CalificacionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerPorEstudianteYAsignacion(int $estudiante_id, int $asignacion_id): array
    {
        $sql = "SELECT c.*, pe.nombre as evaluacion_nombre, pe.ponderacion 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.estudiante_id = ? AND pe.asignacion_id = ? 
                ORDER BY pe.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $asignacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudiante(int $estudiante_id, string $ano_academico): array
    {
        $sql = "SELECT c.nota, pe.nombre as evaluacion, pe.ponderacion, m.nombre as materia,
                       a.id as asignacion_id, pe.id as plan_id
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                WHERE c.estudiante_id = ? AND a.ano_academico = ?
                ORDER BY m.nombre, pe.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_academico]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudianteYPlan(int $estudiante_id, int $plan_evaluacion_id): ?array
    {
        $sql = "SELECT * FROM calificaciones WHERE estudiante_id = ? AND plan_evaluacion_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $plan_evaluacion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO calificaciones (estudiante_id, plan_evaluacion_id, nota, profesor_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['plan_evaluacion_id'],
            $datos['nota'],
            $datos['profesor_id']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE calificaciones SET nota = ?, fecha_registro = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$datos['nota'], $id]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM calificaciones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function calcularPromedioPorMateria(int $estudiante_id, int $asignacion_id): float
    {
        $sql = "SELECT SUM(c.nota * pe.ponderacion) / SUM(pe.ponderacion) as promedio
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.estudiante_id = ? AND pe.asignacion_id = ? AND pe.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $asignacion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['promedio'] : 0.0;
    }

    public function obtenerUltimasPorProfesor(int $profesor_id, int $limite = 5): array
    {
        $sql = "SELECT c.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, pe.nombre as evaluacion_nombre 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.profesor_id = ? 
                ORDER BY c.fecha_registro DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $profesor_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotasPorAsignacion(int $asignacion_id): array
    {
        $sql = "SELECT c.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, pe.nombre as evaluacion_nombre 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE pe.asignacion_id = ? 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$asignacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function obtenerRendimientoPorMateria(?int $materia_id = null): array
    {
        $sql = "SELECT m.nombre as materia, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id";
        $params = [];

        if ($materia_id) {
            $sql .= " WHERE m.id = ?";
            $params[] = $materia_id;
        }

        $sql .= " GROUP BY m.id, m.nombre ORDER BY promedio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function obtenerPromediosPorSeccion(?int $seccion_id = null): array
    {
        $sql = "SELECT m.nombre as materia, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id";
        $params = [];

        if ($seccion_id) {
            $sql .= " WHERE a.seccion_id = ?";
            $params[] = $seccion_id;
        }

        $sql .= " GROUP BY m.id, m.nombre ORDER BY m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorPeriodoYSeccion(string $periodo, int $seccion_id): array
    {
        $sql = "SELECT c.*, u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, m.nombre as materia 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                WHERE a.ano_academico = ? AND a.seccion_id = ? 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodo, $seccion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRendimientoPorSeccion(int $seccion_id, ?int $materia_id = null, ?string $periodo = null): array
    {
        $sql = "SELECT u.nombre, u.apellido, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                WHERE a.seccion_id = ?";
        $params = [$seccion_id];

        if ($materia_id) {
            $sql .= " AND a.materia_id = ?";
            $params[] = $materia_id;
        }
        if ($periodo) {
            $sql .= " AND a.ano_academico = ?";
            $params[] = $periodo;
        }

        $sql .= " GROUP BY e.id, u.nombre, u.apellido ORDER BY promedio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}