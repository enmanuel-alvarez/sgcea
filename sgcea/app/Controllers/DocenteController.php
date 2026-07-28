<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Models\Services\AsignacionService;
use Src\Models\Services\PlanEvaluacionService;
use Src\Models\Services\CalificacionService;
use Src\Models\Services\AsistenciaService;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class DocenteController extends Controller
{
    private AsignacionService $asignacionService;
    private PlanEvaluacionService $planEvaluacionService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private EstudianteService $estudianteService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->asignacionService = new AsignacionService();
        $this->planEvaluacionService = new PlanEvaluacionService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->estudianteService = new EstudianteService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Dashboard del docente
     */
    public function index(): void
    {
        $idProfesor = $_SESSION['profesor_id'] ?? 0;
        
        $datosDashboard = [
            'asignaciones' => $this->asignacionService->obtenerPorProfesor($idProfesor),
            'totalEstudiantes' => $this->asignacionService->contarEstudiantesPorProfesor($idProfesor),
            'actividadesEvaluativas' => $this->planEvaluacionService->contarPorProfesor($idProfesor),
            'ultimasCalificaciones' => $this->calificacionService->obtenerUltimasPorProfesor($idProfesor, 5)
        ];

        $this->render('docente/dashboard', $datosDashboard);
    }

    /**
     * Listado de asignaciones para calificar
     */
    public function calificaciones(): void
    {
        $idProfesor = $_SESSION['profesor_id'] ?? 0;
        $asignaciones = $this->asignacionService->obtenerPorProfesor($idProfesor);

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

        // Verificar que el docente sea el propietario
        if ($asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $estudiantes = $this->estudianteService->obtenerPorSeccion($asignacion['id_seccion']);
        $planEvaluacion = $this->planEvaluacionService->obtenerPorAsignacion($idAsignacion);
        $notasExistentes = $this->calificacionService->obtenerNotasPorAsignacion($idAsignacion);

        // Organizar notas por estudiante y actividad
        $notasPorEstudiante = [];
        foreach ($notasExistentes as $nota) {
            $notasPorEstudiante[$nota['id_estudiante']][$nota['id_plan_evaluacion']] = $nota['nota'];
        }

        $this->render('docente/calificaciones/registrar', [
            'asignacion' => $asignacion,
            'estudiantes' => $estudiantes,
            'planEvaluacion' => $planEvaluacion,
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
        if (!$asignacion || $asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
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
        $idProfesor = $_SESSION['profesor_id'] ?? 0;
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

        if ($asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
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
        if (!$asignacion || $asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
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
        
        if (!$asignacion || $asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para gestionar esta asignación';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        $actividades = $this->planEvaluacionService->obtenerPorAsignacion($idAsignacion);
        $totalPonderacion = array_sum(array_column($actividades, 'ponderacion'));

        $this->render('docente/calificaciones/plan_evaluacion', [
            'asignacion' => $asignacion,
            'actividades' => $actividades,
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

        $idAsignacion = (int)($_POST['id_asignacion'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $ponderacion = (float)($_POST['ponderacion'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');

        $asignacion = $this->asignacionService->obtenerPorId($idAsignacion);
        if (!$asignacion || $asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
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
                'id_asignacion' => $idAsignacion,
                'nombre' => $nombre,
                'tipo' => $tipo,
                'ponderacion' => $ponderacion,
                'descripcion' => $descripcion
            ]);

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

        $asignacion = $this->asignacionService->obtenerPorId($plan['id_asignacion']);
        if (!$asignacion || $asignacion['id_profesor'] !== $_SESSION['profesor_id']) {
            $_SESSION['flash_error'] = 'No tiene permiso para realizar esta acción';
            $this->redirigir('/docente/calificaciones');
            return;
        }

        try {
            $this->planEvaluacionService->eliminar($idPlan);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'ELIMINAR_ACTIVIDAD_EVALUATIVA',
                'planes_evaluacion',
                $idPlan,
                "Actividad '{$plan['nombre']}' eliminada"
            );

            $_SESSION['flash_success'] = 'Actividad eliminada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al eliminar la actividad: ' . $e->getMessage();
        }

        $this->redirigir('/docente/plan-evaluacion/' . $plan['id_asignacion']);
    }
}
