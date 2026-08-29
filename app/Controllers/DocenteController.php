<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\AsignacionService;
use Src\Models\Services\PlanEvaluacionService;
use Src\Models\Services\CalificacionService;
use Src\Models\Services\AsistenciaService;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\RevisionService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class DocenteController extends Controller
{
    private AsignacionService $asignacionService;
    private PlanEvaluacionService $planEvaluacionService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private EstudianteService $estudianteService;
    private RevisionService $revisionService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->asignacionService = new AsignacionService();
        $this->planEvaluacionService = new PlanEvaluacionService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->estudianteService = new EstudianteService();
        $this->revisionService = new RevisionService();
        $this->auditoriaService = new AuditoriaService();
    }

    private function obtenerProfesorId(): int
    {
        if (isset($_SESSION['profesor_id']) && (int)$_SESSION['profesor_id'] > 0) {
            return (int)$_SESSION['profesor_id'];
        }
        if (isset($_SESSION['usuario_id'])) {
            $docenteRepo = new \Src\Models\Repositories\DocenteRepository();
            $docente = $docenteRepo->obtenerPorUsuario((int)$_SESSION['usuario_id']);
            if ($docente) {
                $_SESSION['profesor_id'] = (int)$docente['id'];
                return (int)$docente['id'];
            }
        }
        return 0;
    }

    /**
     * Dashboard del docente
     */
    public function index(): void
    {
        $idProfesor = $this->obtenerProfesorId();
        $asignaciones = $this->asignacionService->obtenerPorProfesor($idProfesor);
        $solicitudesRevision = $this->revisionService->contarPendientesPorProfesor($idProfesor);
        
        $datosDashboard = [
            'asignaciones' => $asignaciones,
            'totalEstudiantes' => $this->asignacionService->contarEstudiantesPorProfesor($idProfesor),
            'actividadesEvaluativas' => $this->planEvaluacionService->contarPorProfesor($idProfesor),
            'ultimasCalificaciones' => $this->calificacionService->obtenerUltimasPorProfesor($idProfesor, 5),
            'solicitudesRevision' => $solicitudesRevision,
            'estadisticas' => [
                'total_asignaciones' => count($asignaciones),
                'total_estudiantes' => $this->asignacionService->contarEstudiantesPorProfesor($idProfesor),
                'calificaciones_pendientes' => $this->planEvaluacionService->contarPorProfesor($idProfesor),
                'solicitudes_revision' => $solicitudesRevision,
                'promedio_general' => '15.4'
            ]
        ];

        $this->render('docente/dashboard', $datosDashboard);
    }

    /**
     * Listado de asignaciones para calificar
     */
    public function calificaciones(): void
    {
        $idProfesor = $this->obtenerProfesorId();
        $asignaciones = $this->asignacionService->obtenerPorProfesor($idProfesor);

        foreach ($asignaciones as &$asn) {
            $actividades = $this->planEvaluacionService->obtenerPorAsignacion((int)$asn['id']);
            $asn['actividades_count'] = count($actividades);
            $asn['total_ponderacion'] = array_sum(array_column($actividades, 'ponderacion'));
            $asn['tiene_plan'] = ($asn['actividades_count'] > 0);
            $asn['actividades'] = $actividades;
        }
        unset($asn);

        $this->render('docente/calificaciones/index', compact('asignaciones'));
    }

    /**
     * Mostrar formulario para registrar calificaciones
     */
    public function registrarCalificaciones(int $idAsignacion): void
    {
        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        
        if (!$asignacion) {
            $_SESSION['flash_error'] = 'Asignación no encontrada';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $idProfesor = $this->obtenerProfesorId();
        // Verificar que el docente sea el propietario (si hay idProfesor resolved)
        if ($idProfesor > 0 && (int)$asignacion['profesor_id'] !== $idProfesor) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $seccionId = (int)($asignacion['seccion_id'] ?? $asignacion['id_seccion'] ?? 0);
        $estudiantes = $this->estudianteService->obtenerPorSeccion($seccionId);
        $planEvaluacion = $this->planEvaluacionService->obtenerPorAsignacion($idAsignacion);
        $notasExistentes = $this->calificacionService->obtenerNotasPorAsignacion($idAsignacion);

        // Organizar notas por estudiante y actividad
        $notasPorEstudiante = [];
        foreach ($notasExistentes as $nota) {
            $estId = $nota['estudiante_id'] ?? $nota['id_estudiante'] ?? 0;
            $planId = $nota['plan_evaluacion_id'] ?? $nota['id_plan_evaluacion'] ?? 0;
            $notasPorEstudiante[$estId][$planId] = $nota['nota'];
        }

        $this->render('docente/calificaciones/registrar', [
            'asignacion' => $asignacion,
            'estudiantes' => $estudiantes,
            'planEvaluacion' => $planEvaluacion,
            'evaluaciones' => $planEvaluacion,
            'notasExistentes' => $notasExistentes,
            'notas' => $notasExistentes,
            'notasPorEstudiante' => $notasPorEstudiante
        ]);
    }

    /**
     * Guardar calificaciones masivamente
     */
    public function guardarCalificaciones(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/docente/calificaciones');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $idAsignacion = (int)($_POST['id_asignacion'] ?? 0);
        $notas = $_POST['notas'] ?? [];

        if ($idAsignacion === 0 || empty($notas)) {
            $_SESSION['flash_error'] = 'Datos inválidos';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        if (!$asignacion || $asignacion['profesor_id'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para realizar esta acción';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        try {
            $contador = 0;
            foreach ($notas as $idEstudiante => $actividades) {
                foreach ($actividades as $idPlanEvaluacion => $nota) {
                    if ($nota !== '' && $nota !== null) {
                        $notaFloat = (float)$nota;
                        if ($this->calificacionService->registrarNota(
                            (int)$idEstudiante,
                            (int)$idPlanEvaluacion,
                            $notaFloat,
                            $_SESSION['profesor_id']
                        )) {
                            $contador++;
                        }
                    }
                }
            }

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'REGISTRO_CALIFICACIONES',
                'calificaciones',
                $idAsignacion,
                "Se registraron {$contador} calificaciones para la asignación {$idAsignacion}"
            );

            $_SESSION['flash_success'] = "Se registraron {$contador} calificaciones exitosamente";
            $this->redirigir('/docente/calificaciones');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar las calificaciones: ' . $e->getMessage();
            $this->redirigir('/docente/calificaciones/registrar/' . $idAsignacion);
        }
    }

    /**
     * Listado de asignaciones para asistencia
     */
    public function asistencia(): void
    {
        $idProfesor = $this->obtenerProfesorId();
        $asignaciones = $this->asignacionService->obtenerPorProfesor($idProfesor);

        $this->render('docente/asistencia/index', compact('asignaciones'));
    }

    /**
     * Mostrar formulario para registrar asistencia
     */
    public function registrarAsistencia(int $idAsignacion): void
    {
        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        
        if (!$asignacion) {
            $_SESSION['flash_error'] = 'Asignación no encontrada';
            $this->redirigir('/docente/asistencia');
            return;
        }

        if ($asignacion['profesor_id'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/asistencia');
            return;
        }

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $estudiantes = $this->estudianteService->obtenerPorSeccion($asignacion['id_seccion']);
        $asistenciasExistentes = $this->asistenciaService->obtenerPorFechaYAsignacion($fecha, $idAsignacion);

        // Organizar asistencias por estudiante
        $asistenciasPorEstudiante = [];
        foreach ($asistenciasExistentes as $asistencia) {
            $asistenciasPorEstudiante[$asistencia['id_estudiante']] = $asistencia['estado'];
        }

        $this->render('docente/asistencia/registrar', [
            'asignacion' => $asignacion,
            'estudiantes' => $estudiantes,
            'fecha' => $fecha,
            'asistenciasPorEstudiante' => $asistenciasPorEstudiante
        ]);
    }

    /**
     * Guardar asistencia masivamente
     */
    public function guardarAsistencia(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/docente/asistencia');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/docente/asistencia');
            return;
        }

        $idAsignacion = (int)($_POST['id_asignacion'] ?? 0);
        $fecha = $_POST['fecha'] ?? '';
        $asistencias = $_POST['asistencias'] ?? [];

        if ($idAsignacion === 0 || empty($fecha) || empty($asistencias)) {
            $_SESSION['flash_error'] = 'Datos inválidos';
            $this->redirigir('/docente/asistencia');
            return;
        }

        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        if (!$asignacion || $asignacion['profesor_id'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para realizar esta acción';
            $this->redirigir('/docente/asistencia');
            return;
        }

        try {
            $datosAsistencias = [];
            foreach ($asistencias as $idEstudiante => $estado) {
                $datosAsistencias[] = [
                    'id_asignacion' => $idAsignacion,
                    'id_estudiante' => (int)$idEstudiante,
                    'fecha' => $fecha,
                    'estado' => in_array($estado, ['presente', 'ausente', 'tarde', 'justificado']) ? $estado : 'ausente'
                ];
            }

            $registradas = $this->asistenciaService->registrarMasiva($datosAsistencias);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'REGISTRO_ASISTENCIA',
                'asistencias',
                $idAsignacion,
                "Se registraron {$registradas} asistencias para la fecha {$fecha}"
            );

            $_SESSION['flash_success'] = "Se registraron {$registradas} asistencias exitosamente";
            $this->redirigir('/docente/asistencia');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar las asistencias: ' . $e->getMessage();
            $this->redirigir('/docente/asistencia/registrar/' . $idAsignacion . '?fecha=' . $fecha);
        }
    }

    /**
     * Gestión del plan de evaluación
     */
    public function planEvaluacion(int $idAsignacion): void
    {
        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        
        $idProfesor = $this->obtenerProfesorId();
        if (!$asignacion || ($idProfesor > 0 && (int)$asignacion['profesor_id'] !== $idProfesor)) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $actividades = $this->planEvaluacionService->obtenerPorAsignacion($idAsignacion);
        $totalPonderacion = array_sum(array_column($actividades, 'ponderacion'));

        $this->render('docente/planevaluacion', [
            'asignacion' => $asignacion,
            'actividades' => $actividades,
            'planes' => $actividades,
            'totalPonderacion' => $totalPonderacion
        ]);
    }

    /**
     * Crear actividad de evaluación
     */
    public function crearActividad(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/docente/calificaciones');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $idAsignacion = (int)($_POST['id_asignacion'] ?? $_POST['asignacion_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = $_POST['tipo'] ?? 'examen';
        $ponderacion = (float)($_POST['ponderacion'] ?? 0);
        $fechaProgramada = $_POST['fecha_programada'] ?? date('Y-m-d');
        $descripcion = trim($_POST['descripcion'] ?? '');

        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        $idProfesor = $this->obtenerProfesorId();
        if (!$asignacion || ($idProfesor > 0 && (int)$asignacion['profesor_id'] !== $idProfesor)) {
            $_SESSION['flash_error'] = 'No tiene permiso para realizar esta acción';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        if (empty($nombre) || empty($tipo) || $ponderacion <= 0) {
            $_SESSION['flash_error'] = 'Todos los campos son requeridos y la ponderación debe ser mayor a 0';
            $this->redirigir('/docente/plan-evaluacion/' . $idAsignacion);
            return;
        }

        // Validar que la suma no supere 100%
        $actividadesExistentes = $this->planEvaluacionService->obtenerPorAsignacion($idAsignacion);
        $totalActual = array_sum(array_column($actividadesExistentes, 'ponderacion'));
        
        if (($totalActual + $ponderacion) > 100) {
            $_SESSION['flash_error'] = 'La suma de las ponderaciones no puede superar el 100%. Actual: ' . $totalActual . '%';
            $this->redirigir('/docente/plan-evaluacion/' . $idAsignacion);
            return;
        }

        try {
            $this->planEvaluacionService->crear([
                'asignacion_id' => $idAsignacion,
                'id_asignacion' => $idAsignacion,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'tipo_evaluacion' => $tipo,
                'ponderacion' => $ponderacion,
                'fecha_programada' => $fechaProgramada,
                'descripcion' => $descripcion
            ], $_SESSION['usuario_id'] ?? 0);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREAR_ACTIVIDAD_EVALUATIVA',
                'planes_evaluacion',
                null,
                "Actividad '{$nombre}' creada para asignación {$idAsignacion}"
            );

            $_SESSION['flash_success'] = 'Actividad creada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al crear la actividad: ' . $e->getMessage();
        }

        $this->redirigir('/docente/plan-evaluacion/' . $idAsignacion);
    }

    /**
     * Eliminar actividad de evaluación
     */
    public function eliminarActividad(int $idPlan): void
    {
        $plan = $this->planEvaluacionService->obtenerPorId($idPlan);
        
        if (!$plan) {
            $_SESSION['flash_error'] = 'Actividad no encontrada';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $idAsignacion = (int)($plan['asignacion_id'] ?? $plan['id_asignacion'] ?? 0);
        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        if (!$asignacion || (int)($asignacion['profesor_id'] ?? 0) !== (int)($_SESSION['profesor_id'] ?? 0)) {
            $_SESSION['flash_error'] = 'No tiene permiso para realizar esta acción';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        try {
            $this->planEvaluacionService->eliminar($idPlan);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'ELIMINAR_ACTIVIDAD_EVALUATIVA',
                'planes_evaluacion',
                $idPlan,
                "Actividad '{$plan['nombre']}' eliminada"
            );

            $_SESSION['flash_success'] = 'Actividad eliminada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al eliminar la actividad: ' . $e->getMessage();
        }

        $this->redirigir('/docente/calificaciones');
    }

    /**
     * Crear varias actividades evaluativas en lote desde el Modal
     */
    public function crearActividadesLote(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/docente/calificaciones');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $idAsignacion = (int)($_POST['id_asignacion'] ?? $_POST['asignacion_id'] ?? 0);
        $actividades = $_POST['actividades'] ?? [];

        $idProfesor = $this->obtenerProfesorId();
        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        if (!$asignacion || ($idProfesor > 0 && (int)$asignacion['profesor_id'] !== $idProfesor)) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        try {
            $creados = $this->planEvaluacionService->crearLote($idAsignacion, $actividades, $_SESSION['usuario_id'] ?? 0);
            $_SESSION['flash_success'] = 'Se registraron ' . count($creados) . ' actividades evaluativas exitosamente.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al registrar plan de evaluación: ' . $e->getMessage();
        }

        $this->redirigir('/docente/calificaciones');
    }

    /**
     * Listar solicitudes de revisión de notas recibidas
     */
    public function revisiones(): void
    {
        $idProfesor = $this->obtenerProfesorId();
        $solicitudes = $this->revisionService->obtenerPorProfesor($idProfesor);

        $this->render('docente/revisiones/index', [
            'solicitudes' => $solicitudes
        ]);
    }

    /**
     * Responder solicitud de revisión de notas
     */
    public function responderRevision(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/docente/revisiones');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/docente/revisiones');
            return;
        }

        $idSolicitud = (int)($_POST['id_solicitud'] ?? 0);
        $estado = $_POST['estado'] ?? 'aprobada';
        $respuesta = trim($_POST['respuesta'] ?? '');

        try {
            $this->revisionService->responder($idSolicitud, $estado, $respuesta, $_SESSION['usuario_id'] ?? 0);
            $_SESSION['flash_success'] = 'Solicitud de revisión respondida exitosamente.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar solicitud: ' . $e->getMessage();
        }

        $this->redirigir('/docente/revisiones');
    }
}
