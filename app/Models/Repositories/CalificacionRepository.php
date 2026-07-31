<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class CalificacionRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerPorEstudianteYAsignacion(int $estudiante_id, int $asignacion_id): array
    {
        $sql = "SELECT c.*, pu_est.nombre as evaluacion_nombre, pe.ponderacion 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.estudiante_id = ? AND pe.asignacion_id = ? 
                ORDER BY pe.orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $asignacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorPlanEvaluacion(int $plan_evaluacion_id): array
    {
        $sql = "SELECT c.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                WHERE c.plan_evaluacion_id = ? 
                ORDER BY u_est.apellido, u_est.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$plan_evaluacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudiante(int $estudiante_id, string $ano_lectivo): array
    {
        $sql = "SELECT c.nota, pu_est.nombre as evaluacion, pe.ponderacion, m.nombre as materia,
                       a.id as asignacion_id, pe.id as plan_id
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                WHERE c.estudiante_id = ? AND a.ano_lectivo = ?
                ORDER BY m.nombre, pe.orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_lectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotasPorSeccionYMateria(int $seccion_id, int $materia_id, array $evaluaciones): array
    {
        $placeholders = implode(',', array_fill(0, count($evaluaciones), '?'));
        $sql = "SELECT e.id as estudiante_id, u_est.nombre, u_est.apellido, e.codigo,
                " . implode(', ', array_map(function($id, $index) {
                    return "MAX(CASE WHEN pe.id = $id THEN c.nota END) AS nota_$index";
                }, $evaluaciones, array_keys($evaluaciones))) . "
                FROM estudiantes e 
                LEFT JOIN calificaciones c ON e.id = c.estudiante_id 
                LEFT JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE e.seccion_id = ? AND pe.asignacion_id IN (
                    SELECT id FROM asignaciones WHERE seccion_id = ? AND materia_id = ? AND estado = 'activa'
                ) AND pe.id IN ($placeholders)
                GROUP BY e.id, u_est.nombre, u_est.apellido, e.codigo
                ORDER BY u_est.apellido, u_est.nombre";
        
        $params = array_merge([$seccion_id, $seccion_id, $materia_id], $evaluaciones);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO calificaciones (estudiante_id, plan_evaluacion_id, nota, fecha_registro, profesor_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['plan_evaluacion_id'],
            $datos['nota'],
            $datos['fecha_registro'] ?? date('Y-m-d H:i:s'),
            $datos['profesor_id']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE calificaciones SET nota = ?, fecha_registro = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nota'],
            $datos['fecha_registro'] ?? date('Y-m-d H:i:s'),
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM calificaciones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerPorEstudianteYPlan(int $estudiante_id, int $plan_evaluacion_id): ?array
    {
        $sql = "SELECT * FROM calificaciones WHERE estudiante_id = ? AND plan_evaluacion_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $plan_evaluacion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function calcularPromedioPorMateria(int $estudiante_id, int $asignacion_id): float
    {
        $sql = "SELECT AVG(c.nota * pe.ponderacion) / SUM(pe.ponderacion) as promedio
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.estudiante_id = ? AND pe.asignacion_id = ? AND pe.estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $asignacion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['promedio'] : 0.0;
    }

    public function contarEvaluadosPorPlan(int $plan_evaluacion_id): int
    {
        $sql = "SELECT COUNT(DISTINCT estudiante_id) FROM calificaciones WHERE plan_evaluacion_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$plan_evaluacion_id]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerUltimasPorProfesor(int $profesor_id, int $limite = 5): array
    {
        $sql = "SELECT c.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido, pu_est.nombre as evaluacion_nombre 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE c.profesor_id = ? 
                ORDER BY c.fecha_registro DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $profesor_id, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerNotasPorAsignacion(int $asignacion_id): array
    {
        $sql = "SELECT c.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido, pu_est.nombre as evaluacion_nombre 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                WHERE pe.asignacion_id = ? 
                ORDER BY u_est.apellido, u_est.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$asignacion_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerRendimientoPorMateria(): array
    {
        $sql = "SELECT m.nombre as materia, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                GROUP BY m.id, m.nombre 
                ORDER BY promedio DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPromediosPorSeccion(int $seccion_id): array
    {
        $sql = "SELECT m.nombre as materia, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                WHERE a.seccion_id = ? 
                GROUP BY m.id, m.nombre 
                ORDER BY m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorPeriodoYSeccion(string $periodo, int $seccion_id): array
    {
        $sql = "SELECT c.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido, m.nombre as materia 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                WHERE a.ano_lectivo = ? AND a.seccion_id = ? 
                ORDER BY u_est.apellido, u_est.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodo, $seccion_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerRendimientoPorSeccion(int $seccion_id, ?int $materia_id = null, ?string $periodo = null): array
    {
        $sql = "SELECT u_est.nombre, u_est.apellido, AVG(c.nota) as promedio 
                FROM calificaciones c 
                INNER JOIN estudiantes e ON c.estudiante_id = e.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                WHERE a.seccion_id = ?";
        $params = [$seccion_id];

        if ($materia_id) {
            $sql .= " AND a.materia_id = ?";
            $params.append($materia_id);
        }

        if ($periodo) {
            $sql .= " AND a.ano_lectivo = ?";
            $params.append($periodo);
        }

        $sql .= " GROUP BY e.id, u_est.nombre, u_est.apellido ORDER BY promedio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
