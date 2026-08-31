<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\DashboardCacheRepository;
use Src\Models\Repositories\EstudianteRepository;
use Src\Models\Repositories\DocenteRepository;
use Src\Models\Repositories\MateriaRepository;
use Src\Models\Repositories\SeccionRepository;
use Src\Models\Repositories\InscripcionRepository;
use Src\Models\Repositories\ConstanciaRepository;
use Src\Models\Repositories\AsignacionRepository;
use Src\Models\Repositories\CalificacionRepository;

class DashboardService
{
    private DashboardCacheRepository $cacheRepo;
    private EstudianteRepository $estudianteRepo;
    private DocenteRepository $docenteRepo;
    private MateriaRepository $materiaRepo;
    private SeccionRepository $seccionRepo;
    private InscripcionRepository $inscripcionRepo;
    private ConstanciaRepository $constanciaRepo;
    private AsignacionRepository $asignacionRepo;
    private CalificacionRepository $calificacionRepo;

    public function __construct()
    {
        $this->cacheRepo        = new DashboardCacheRepository();
        $this->estudianteRepo   = new EstudianteRepository();
        $this->docenteRepo      = new DocenteRepository();
        $this->materiaRepo      = new MateriaRepository();
        $this->seccionRepo      = new SeccionRepository();
        $this->inscripcionRepo  = new InscripcionRepository();
        $this->constanciaRepo   = new ConstanciaRepository();
        $this->asignacionRepo   = new AsignacionRepository();
        $this->calificacionRepo = new CalificacionRepository();
    }

    public function obtenerDatosAdmin(?string $periodo = null, ?int $gradoId = null): array
    {
        $db = \Src\Core\Database::getInstance()->getConnection();

        // 1. Total Estudiantes (filtrado por grado si aplica)
        if ($gradoId) {
            $stmt = $db->prepare("SELECT COUNT(DISTINCT e.id) FROM estudiantes e JOIN inscripciones i ON e.id = i.estudiante_id JOIN secciones s ON i.seccion_id = s.id WHERE s.grado_id = ?");
            $stmt->execute([$gradoId]);
            $totalEstudiantes = (int)$stmt->fetchColumn();
        } else {
            $stmt = $db->query("SELECT COUNT(*) FROM estudiantes");
            $totalEstudiantes = (int)($stmt ? $stmt->fetchColumn() : 0);
        }

        // 2. Total Docentes
        $stmt = $db->query("SELECT COUNT(*) FROM profesores");
        $totalDocentes = (int)($stmt ? $stmt->fetchColumn() : 0);

        // 3. Total Materias
        $stmt = $db->query("SELECT COUNT(*) FROM materias");
        $totalMaterias = (int)($stmt ? $stmt->fetchColumn() : 0);

        // 4. Total Secciones (filtrado por grado si aplica)
        if ($gradoId) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM secciones WHERE grado_id = ?");
            $stmt->execute([$gradoId]);
            $totalSecciones = (int)$stmt->fetchColumn();
        } else {
            $stmt = $db->query("SELECT COUNT(*) FROM secciones");
            $totalSecciones = (int)($stmt ? $stmt->fetchColumn() : 0);
        }

        // 5. Inscripciones año lectivo / período
        $ano = $periodo ? (int)$periodo : (int)date('Y');
        $stmt = $db->prepare("SELECT COUNT(*) FROM inscripciones WHERE ano_academico LIKE ? OR DATE_FORMAT(fecha_inscripcion, '%Y') = ?");
        $stmt->execute(["%{$ano}%", (string)$ano]);
        $inscripcionesAno = (int)$stmt->fetchColumn();

        // 6. Constancias pendientes
        $stmt = $db->query("SELECT COUNT(*) FROM solicitudes_constancia WHERE estado = 'pendiente'");
        $constanciasPendientes = (int)($stmt ? $stmt->fetchColumn() : 0);

        // 7. Últimos estudiantes registrados
        if ($gradoId) {
            $stmt = $db->prepare("SELECT e.id, u.nombre, u.apellido, u.created_at AS fecha_creacion, g.nombre AS grado_nombre FROM estudiantes e INNER JOIN usuarios u ON e.usuario_id = u.id INNER JOIN inscripciones i ON e.id = i.estudiante_id INNER JOIN secciones s ON i.seccion_id = s.id INNER JOIN grados g ON s.grado_id = g.id WHERE s.grado_id = ? ORDER BY e.id DESC LIMIT 5");
            $stmt->execute([$gradoId]);
        } else {
            $stmt = $db->query("SELECT e.id, u.nombre, u.apellido, u.created_at AS fecha_creacion, (SELECT g.nombre FROM inscripciones i JOIN secciones s ON i.seccion_id = s.id JOIN grados g ON s.grado_id = g.id WHERE i.estudiante_id = e.id ORDER BY i.id DESC LIMIT 1) AS grado_nombre FROM estudiantes e INNER JOIN usuarios u ON e.usuario_id = u.id ORDER BY e.id DESC LIMIT 5");
        }
        $ultimosEstudiantes = $stmt ? ($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: []) : [];

        // 8. Balance de Asistencias (Presentes vs Ausentes)
        $stmtPresentes = $db->query("SELECT COUNT(*) FROM asistencias WHERE estado = 'presente'");
        $asistenciasPresentes = (int)($stmtPresentes ? $stmtPresentes->fetchColumn() : 0);

        $stmtAusentes = $db->query("SELECT COUNT(*) FROM asistencias WHERE estado IN ('ausente', 'inasistencia')");
        $asistenciasAusentes = (int)($stmtAusentes ? $stmtAusentes->fetchColumn() : 0);

        // 9. Distribución de Constancias por Estado
        $stmtConstPend = $db->query("SELECT COUNT(*) FROM solicitudes_constancia WHERE estado = 'pendiente'");
        $constPendientesCount = (int)($stmtConstPend ? $stmtConstPend->fetchColumn() : 0);

        $stmtConstAprob = $db->query("SELECT COUNT(*) FROM solicitudes_constancia WHERE estado IN ('aprobada', 'emitida')");
        $constAprobadasCount = (int)($stmtConstAprob ? $stmtConstAprob->fetchColumn() : 0);

        $stmtConstRech = $db->query("SELECT COUNT(*) FROM solicitudes_constancia WHERE estado = 'rechazada'");
        $constRechazadasCount = (int)($stmtConstRech ? $stmtConstRech->fetchColumn() : 0);

        return [
            'total_estudiantes'        => $totalEstudiantes,
            'total_docentes'           => $totalDocentes,
            'total_materias'           => $totalMaterias,
            'total_secciones'          => $totalSecciones,
            'inscripciones_ano_actual' => $inscripcionesAno,
            'constancias_pendientes'   => $constanciasPendientes,
            'ultimos_estudiantes'      => $ultimosEstudiantes,
            'asistencias_presentes'    => $asistenciasPresentes,
            'asistencias_ausentes'     => $asistenciasAusentes,
            'constancias_distribucion' => [
                'pendiente' => $constPendientesCount,
                'aprobada'  => $constAprobadasCount,
                'rechazada' => $constRechazadasCount
            ]
        ];
    }

    public function obtenerDatosDocente(int $docente_id): array
    {
        if ($this->cacheRepo->esDesactualizadoDocente($docente_id)) {
            $asignaciones = $this->asignacionRepo->obtenerPorDocente($docente_id);
            $total_asignaciones = count($asignaciones);

            $total_estudiantes = 0;
            $suma_promedios = 0;
            $contador_promedios = 0;

            foreach ($asignaciones as $asignacion) {
                $estudiantes = $this->estudianteRepo->obtenerEstudiantesPorSeccion($asignacion['seccion_id']);
                $total_estudiantes += count($estudiantes);

                $promedio = $this->calificacionRepo->calcularPromedioPorMateria(
                    $estudiantes[0]['id'] ?? 0,
                    $asignacion['id']
                );

                if ($promedio > 0) {
                    $suma_promedios += $promedio;
                    $contador_promedios++;
                }
            }

            $promedio_general = $contador_promedios > 0 ? $suma_promedios / $contador_promedios : 0;

            $this->cacheRepo->actualizarDocente($docente_id, [
                'total_asignaciones'    => $total_asignaciones,
                'total_estudiantes'     => $total_estudiantes,
                'promedio_calificaciones' => round($promedio_general, 2)
            ]);
        }

        $cache = $this->cacheRepo->obtenerDocente($docente_id);
        return $cache ?: [
            'total_asignaciones'     => 0,
            'total_estudiantes'      => 0,
            'promedio_calificaciones'=> 0
        ];
    }

    public function actualizarCacheDocente(int $docente_id): void
    {
        $this->cacheRepo->esDesactualizadoDocente($docente_id, 0);
        $this->obtenerDatosDocente($docente_id);
    }
}

