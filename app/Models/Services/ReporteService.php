<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Core\Database;
use PDO;

class ReporteService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener Cuadro de Honor (Mejores promedios acumulados)
     */
    public function obtenerCuadroDeHonor(?int $seccionId = null, int $limite = 20): array
    {
        $whereSeccion = $seccionId ? "WHERE s.id = :seccion_id" : "";

        $sql = "SELECT 
                    e.id AS estudiante_id,
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    g.nombre AS grado,
                    s.nombre AS seccion,
                    COUNT(c.id) AS total_evaluaciones,
                    ROUND(AVG(c.nota), 2) AS promedio_general
                FROM estudiantes e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                INNER JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activa'
                INNER JOIN secciones s ON i.seccion_id = s.id
                INNER JOIN grados g ON s.grado_id = g.id
                LEFT JOIN calificaciones c ON e.id = c.estudiante_id
                {$whereSeccion}
                GROUP BY e.id, u.cedula, u.nombre, u.apellido, g.nombre, s.nombre
                HAVING total_evaluaciones > 0
                ORDER BY promedio_general DESC, total_evaluaciones DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        if ($seccionId) {
            $stmt->bindValue(':seccion_id', $seccionId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener Estudiantes en Riesgo Académico (Materias reprobadas o promedio bajo)
     */
    public function obtenerEstudiantesEnRiesgo(?int $seccionId = null, float $notaMinima = 10.0): array
    {
        $whereSeccion = $seccionId ? "AND s.id = :seccion_id" : "";

        $sql = "SELECT 
                    e.id AS estudiante_id,
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    e.telefono_representante,
                    g.nombre AS grado,
                    s.nombre AS seccion,
                    ROUND(AVG(c.nota), 2) AS promedio_general,
                    SUM(CASE WHEN c.nota < :nota_minima_sum THEN 1 ELSE 0 END) AS materias_reprobadas,
                    COUNT(c.id) AS total_evaluaciones
                FROM estudiantes e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                INNER JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activa'
                INNER JOIN secciones s ON i.seccion_id = s.id
                INNER JOIN grados g ON s.grado_id = g.id
                LEFT JOIN calificaciones c ON e.id = c.estudiante_id
                WHERE e.estado = 1 {$whereSeccion}
                GROUP BY e.id, u.cedula, u.nombre, u.apellido, e.telefono_representante, g.nombre, s.nombre
                HAVING materias_reprobadas > 0 OR promedio_general < :nota_minima_avg
                ORDER BY materias_reprobadas DESC, promedio_general ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nota_minima_sum', $notaMinima);
        $stmt->bindValue(':nota_minima_avg', $notaMinima);
        if ($seccionId) {
            $stmt->bindValue(':seccion_id', $seccionId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sábana de Notas Consolidada para una Sección
     */
    public function obtenerSabanaNotasSeccion(int $seccionId): array
    {
        // 1. Obtener materias de la sección
        $stmtMat = $this->db->prepare("
            SELECT DISTINCT m.id, m.nombre, m.codigo 
            FROM materias m
            INNER JOIN asignaciones a ON m.id = a.materia_id
            WHERE a.seccion_id = :seccion_id
            ORDER BY m.nombre ASC
        ");
        $stmtMat->execute([':seccion_id' => $seccionId]);
        $materias = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        // 2. Obtener lista de estudiantes
        $stmtEst = $this->db->prepare("
            SELECT e.id, u.cedula, u.nombre, u.apellido
            FROM estudiantes e
            INNER JOIN usuarios u ON e.usuario_id = u.id
            INNER JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activa'
            WHERE i.seccion_id = :seccion_id
            ORDER BY u.apellido ASC, u.nombre ASC
        ");
        $stmtEst->execute([':seccion_id' => $seccionId]);
        $estudiantes = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

        // 3. Obtener matriz de calificaciones (Estudiante -> Materia -> Promedio)
        $stmtCal = $this->db->prepare("
            SELECT c.estudiante_id, a.materia_id, ROUND(AVG(c.nota), 2) AS promedio_materia
            FROM calificaciones c
            INNER JOIN planes_evaluacion pe ON c.plan_evaluacion_id = pe.id
            INNER JOIN asignaciones a ON pe.asignacion_id = a.id
            WHERE a.seccion_id = :seccion_id
            GROUP BY c.estudiante_id, a.materia_id
        ");
        $stmtCal->execute([':seccion_id' => $seccionId]);
        $calificacionesRaw = $stmtCal->fetchAll(PDO::FETCH_ASSOC);

        $matrizCal = [];
        foreach ($calificacionesRaw as $row) {
            $matrizCal[$row['estudiante_id']][$row['materia_id']] = (float)$row['promedio_materia'];
        }

        return [
            'materias' => $materias,
            'estudiantes' => $estudiantes,
            'matriz' => $matrizCal
        ];
    }

    /**
     * Reporte de Alerta de Ausentismo Crítico (> 20% inasistencias)
     */
    public function obtenerAlertaAusentismoCritico(?int $seccionId = null, float $porcentajeLimite = 20.0): array
    {
        $whereSeccion = $seccionId ? "AND s.id = :seccion_id" : "";

        $sql = "SELECT 
                    e.id AS estudiante_id,
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    g.nombre AS grado,
                    s.nombre AS seccion,
                    COUNT(asi.id) AS total_clases,
                    SUM(CASE WHEN asi.estado = 'ausente' THEN 1 ELSE 0 END) AS ausencias,
                    SUM(CASE WHEN asi.estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas,
                    ROUND((SUM(CASE WHEN asi.estado = 'ausente' THEN 1 ELSE 0 END) / COUNT(asi.id)) * 100, 1) AS pct_ausencia
                FROM estudiantes e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                INNER JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activa'
                INNER JOIN secciones s ON i.seccion_id = s.id
                INNER JOIN grados g ON s.grado_id = g.id
                INNER JOIN asistencias asi ON e.id = asi.estudiante_id
                WHERE e.estado = 1 {$whereSeccion}
                GROUP BY e.id, u.cedula, u.nombre, u.apellido, g.nombre, s.nombre
                HAVING total_clases > 0 AND pct_ausencia >= :pct_limite
                ORDER BY pct_ausencia DESC";

        $stmt = $this->db->prepare($sql);
        if ($seccionId) {
            $stmt->bindValue(':seccion_id', $seccionId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':pct_limite', $porcentajeLimite);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen de Carga Horaria y Asignaciones Docentes
     */
    public function obtenerResumenCargaHorariaDocentes(): array
    {
        $sql = "SELECT 
                    p.id AS profesor_id,
                    u.cedula,
                    u.nombre,
                    u.apellido,
                    p.especialidad,
                    COUNT(DISTINCT a.id) AS total_asignaciones,
                    COUNT(DISTINCT a.materia_id) AS total_materias,
                    COUNT(DISTINCT a.seccion_id) AS total_secciones
                FROM profesores p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN asignaciones a ON p.id = a.profesor_id
                GROUP BY p.id, u.cedula, u.nombre, u.apellido, p.especialidad
                ORDER BY total_asignaciones DESC, u.apellido ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Expediente Ficha 360° Integral del Estudiante
     */
    public function obtenerFicha360Estudiante(int $estudianteId): array
    {
        // 1. Datos personales del estudiante
        $stmtEst = $this->db->prepare("
            SELECT e.*, u.cedula, u.nombre, u.apellido, u.email, g.nombre AS grado_nombre, s.nombre AS seccion_nombre
            FROM estudiantes e
            INNER JOIN usuarios u ON e.usuario_id = u.id
            LEFT JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activa'
            LEFT JOIN secciones s ON i.seccion_id = s.id
            LEFT JOIN grados g ON s.grado_id = g.id
            WHERE e.id = :id
        ");
        $stmtEst->execute([':id' => $estudianteId]);
        $estudiante = $stmtEst->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante) {
            return [];
        }

        // 2. Calificaciones por materia
        $stmtCal = $this->db->prepare("
            SELECT m.nombre AS materia, m.codigo, ROUND(AVG(c.nota), 2) AS promedio_materia, COUNT(c.id) AS total_evaluaciones
            FROM materias m
            INNER JOIN asignaciones a ON m.id = a.materia_id
            INNER JOIN planes_evaluacion pe ON a.id = pe.asignacion_id
            INNER JOIN calificaciones c ON pe.id = c.plan_evaluacion_id
            WHERE c.estudiante_id = :id
            GROUP BY m.id, m.nombre, m.codigo
        ");
        $stmtCal->execute([':id' => $estudianteId]);
        $calificaciones = $stmtCal->fetchAll(PDO::FETCH_ASSOC);

        // 3. Resumen de asistencias
        $stmtAsi = $this->db->prepare("
            SELECT 
                COUNT(id) AS total_clases,
                SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes,
                SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes,
                SUM(CASE WHEN estado = 'tarde' THEN 1 ELSE 0 END) AS tardanzas,
                SUM(CASE WHEN estado = 'justificado' THEN 1 ELSE 0 END) AS justificados
            FROM asistencias
            WHERE estudiante_id = :id
        ");
        $stmtAsi->execute([':id' => $estudianteId]);
        $asistencias = $stmtAsi->fetch(PDO::FETCH_ASSOC);

        // 4. Solicitudes de constancias
        $stmtCons = $this->db->prepare("
            SELECT tipo, motivo, estado, fecha_solicitud
            FROM solicitudes_constancia
            WHERE estudiante_id = :id
            ORDER BY fecha_solicitud DESC
        ");
        $stmtCons->execute([':id' => $estudianteId]);
        $constancias = $stmtCons->fetchAll(PDO::FETCH_ASSOC);

        return [
            'estudiante' => $estudiante,
            'calificaciones' => $calificaciones,
            'asistencias' => $asistencias,
            'constancias' => $constancias
        ];
    }
}

