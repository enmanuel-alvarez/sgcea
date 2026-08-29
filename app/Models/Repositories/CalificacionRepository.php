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

    public function obtenerPromedioPorEstudiante(int $estudiante_id): float
    {
        $sql = "SELECT AVG(nota) FROM calificaciones WHERE estudiante_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        $val = $stmt->fetchColumn();
        return $val !== false && $val !== null ? round((float)$val, 2) : 0.0;
    }

    public function obtenerUltimasPorEstudiante(int $estudiante_id, int $limite = 5): array
    {
        $sql = "SELECT c.nota, c.fecha_registro, pe.nombre as evaluacion_nombre, m.nombre as materia_nombre
                FROM calificaciones c
                INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id
                INNER JOIN materias m ON a.materia_id = m.id
                WHERE c.estudiante_id = ?
                ORDER BY c.fecha_registro DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $estudiante_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerBoletinConEstado(int $estudiante_id, ?string $periodo = null): array
    {
        $wherePeriodo = $periodo ? "AND i.ano_academico = :periodo" : "AND (i.estado = 'activo' OR i.estado = 'activa')";
        $sqlInsc = "SELECT i.seccion_id, i.ano_academico 
                    FROM inscripciones i 
                    WHERE i.estudiante_id = :estudiante_id {$wherePeriodo} 
                    ORDER BY i.id DESC LIMIT 1";
        $stmtInsc = $this->db->prepare($sqlInsc);
        $paramsInsc = [':estudiante_id' => $estudiante_id];
        if ($periodo) $paramsInsc[':periodo'] = $periodo;
        $stmtInsc->execute($paramsInsc);
        $insc = $stmtInsc->fetch(PDO::FETCH_ASSOC);

        if (!$insc) {
            return [
                'materias' => [],
                'boletin_completo' => false,
                'total_materias' => 0,
                'materias_completas' => 0
            ];
        }

        $seccionId = (int)$insc['seccion_id'];
        $anoAcademico = $insc['ano_academico'];

        $sqlMat = "SELECT DISTINCT a.id as asignacion_id, m.id as materia_id, m.nombre as materia_nombre, m.codigo
                   FROM asignaciones a
                   INNER JOIN materias m ON a.materia_id = m.id
                   WHERE a.seccion_id = ? AND a.ano_academico = ? AND a.estado = 1
                   ORDER BY m.nombre ASC";
        $stmtMat = $this->db->prepare($sqlMat);
        $stmtMat->execute([$seccionId, $anoAcademico]);
        $materias = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        $boletin = [];
        $materiasCompletas = 0;
        $totalMaterias = count($materias);

        foreach ($materias as $mat) {
            $asigId = (int)$mat['asignacion_id'];

            $sqlEval = "SELECT pe.id, pe.nombre, pe.ponderacion, c.nota
                        FROM planes_evaluacion pe
                        LEFT JOIN calificaciones c ON pe.id = c.plan_evaluacion_id AND c.estudiante_id = ?
                        WHERE pe.asignacion_id = ? AND (pe.estado = 'activo' OR pe.estado = '1' OR pe.estado = 1)
                        ORDER BY pe.id ASC";
            $stmtEval = $this->db->prepare($sqlEval);
            $stmtEval->execute([$estudiante_id, $asigId]);
            $evaluacionesRaw = $stmtEval->fetchAll(PDO::FETCH_ASSOC);

            $evaluaciones = [];
            $sumPonderada = 0;
            $totalPonderacionEvaluada = 0;
            $sumPonderacionTotalPlan = 0;
            $actividadesEvaluadasCount = 0;
            $actividadesTotalCount = count($evaluacionesRaw);

            foreach ($evaluacionesRaw as $ev) {
                $notaVal = $ev['nota'] !== null ? (float)$ev['nota'] : null;
                $pond = (float)$ev['ponderacion'];
                $sumPonderacionTotalPlan += $pond;

                $evaluaciones[] = [
                    'nombre' => $ev['nombre'],
                    'ponderacion' => $pond,
                    'nota' => $notaVal !== null ? number_format($notaVal, 2) : 'N/A',
                    'evaluada' => ($notaVal !== null)
                ];

                if ($notaVal !== null) {
                    $sumPonderada += ($notaVal * ($pond / 100));
                    $totalPonderacionEvaluada += $pond;
                    $actividadesEvaluadasCount++;
                }
            }

            $promedio = $totalPonderacionEvaluada > 0 ? round($sumPonderada, 2) : 0.0;
            
            // Una materia está completa si tiene al menos 1 evaluación, la suma de ponderaciones es 100% y todas tienen nota registrada
            $materiaCompleta = ($actividadesTotalCount > 0 && abs($sumPonderacionTotalPlan - 100) < 0.01 && $actividadesEvaluadasCount === $actividadesTotalCount);

            if ($materiaCompleta) {
                $materiasCompletas++;
            }

            $boletin[] = [
                'materia' => $mat['materia_nombre'],
                'materia_nombre' => $mat['materia_nombre'],
                'evaluaciones' => $evaluaciones,
                'promedio' => $promedio,
                'materia_completa' => $materiaCompleta,
                'evaluaciones_count' => $actividadesTotalCount,
                'evaluadas_count' => $actividadesEvaluadasCount,
                'ponderacion_plan' => $sumPonderacionTotalPlan,
                'ponderacion_evaluada' => $totalPonderacionEvaluada
            ];
        }

        $boletinCompleto = ($totalMaterias > 0 && $materiasCompletas === $totalMaterias);

        return [
            'materias' => $boletin,
            'boletin_completo' => $boletinCompleto,
            'total_materias' => $totalMaterias,
            'materias_completas' => $materiasCompletas
        ];
    }

    public function obtenerBoletinPorEstudiante(int $estudiante_id, ?string $periodo = null): array
    {
        $res = $this->obtenerBoletinConEstado($estudiante_id, $periodo);
        return $res['materias'];
    }

    public function obtenerPeriodosPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT DISTINCT ano_academico FROM inscripciones WHERE estudiante_id = ? ORDER BY ano_academico DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
