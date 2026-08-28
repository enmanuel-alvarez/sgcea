<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\EstudianteService;
use Src\Models\Services\InscripcionService;
use Src\Models\Services\CalificacionService;
use Src\Models\Services\AsistenciaService;
use Src\Models\Services\ConstanciaService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class EstudianteController extends Controller
{
    private EstudianteService $estudianteService;
    private InscripcionService $inscripcionService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private ConstanciaService $constanciaService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->estudianteService = new EstudianteService();
        $this->inscripcionService = new InscripcionService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->constanciaService = new ConstanciaService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Dashboard del estudiante
     */
    public function index(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        
        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        $inscripcionActual = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);
        
        $datosDashboard = [
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcionActual,
            'promedioGeneral' => $this->calificacionService->obtenerPromedioPorEstudiante($idEstudiante),
            'porcentajeAsistencia' => $this->asistenciaService->obtenerPorcentajePorEstudiante($idEstudiante),
            'ultimasNotas' => $this->calificacionService->obtenerUltimasPorEstudiante($idEstudiante, 5)
        ];

        $this->render('estudiante/dashboard', $datosDashboard);
    }

    /**
     * Mostrar boletín de calificaciones
     */
    public function boletin(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $periodo = $_GET['periodo'] ?? null;
        
        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        $inscripcion = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);
        
        if (!$inscripcion) {
            $_SESSION['flash_error'] = 'No tiene una inscripción activa';
            $this->redirigir('/estudiante');
            return;
        }

        $calificaciones = $this->calificacionService->obtenerBoletinPorEstudiante($idEstudiante, $periodo);
        $periodosDisponibles = $this->calificacionService->obtenerPeriodosPorEstudiante($idEstudiante);

        $this->render('estudiante/boletin', [
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcion,
            'calificaciones' => $calificaciones,
            'periodo' => $periodo,
            'periodosDisponibles' => $periodosDisponibles
        ]);
    }

    /**
     * Mostrar historial de asistencia
     */
    public function asistencia(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $mes = $_GET['mes'] ?? date('m');
        $ano = $_GET['ano'] ?? date('Y');
        
        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        $resumen = $this->asistenciaService->obtenerResumenPorEstudiante($idEstudiante, $mes, $ano);
        $detalle = $this->asistenciaService->obtenerDetallePorEstudiante($idEstudiante, $mes, $ano);

        $this->render('estudiante/asistencia', [
            'estudiante' => $estudiante,
            'resumen' => $resumen,
            'detalle' => $detalle,
            'mes' => $mes,
            'ano' => $ano
        ]);
    }

    /**
     * Formulario para solicitar constancia
     */
    public function solicitarConstancia(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $pendientes = $this->constanciaService->contarPendientesPorEstudiante($idEstudiante);
        
        $this->render('estudiante/constancias/solicitar', [
            'pendientes' => $pendientes
        ]);
    }

    /**
     * Guardar solicitud de constancia
     */
    public function guardarSolicitud(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/estudiante/constancias/solicitar');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/estudiante/constancias/solicitar');
            return;
        }

        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';
        $motivo = trim($_POST['motivo'] ?? '');

        if (empty($tipo) || empty($motivo)) {
            $_SESSION['flash_error'] = 'Todos los campos son requeridos';
            $this->redirigir('/estudiante/constancias/solicitar');
            return;
        }

        // Validar límite de solicitudes pendientes
        $pendientes = $this->constanciaService->contarPendientesPorEstudiante($idEstudiante);
        if ($pendientes >= 3) {
            $_SESSION['flash_error'] = 'Ha alcanzado el límite de 3 solicitudes pendientes. Espere a que sean procesadas.';
            $this->redirigir('/estudiante/constancias/solicitar');
            return;
        }

        try {
            $this->constanciaService->solicitar($idEstudiante, $tipo, $motivo);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'SOLICITAR_CONSTANCIA',
                'solicitudes_constancia',
                null,
                "Solicitud de constancia tipo '{$tipo}' creada"
            );

            $_SESSION['flash_success'] = 'Solicitud creada exitosamente. Espere la aprobación del administrador.';
            $this->redirigir('/estudiante/constancias/historial');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al crear la solicitud: ' . $e->getMessage();
            $this->redirigir('/estudiante/constancias/solicitar');
        }
    }

    /**
     * Historial de constancias solicitadas
     */
    public function historialConstancias(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $constancias = $this->constanciaService->obtenerPorEstudiante($idEstudiante);

        $this->render('estudiante/constancias/historial', compact('constancias'));
    }

    /**
     * Descargar constancia aprobada
     */
    public function descargarConstancia(int $idSolicitud): void
    {
        $solicitud = $this->constanciaService->obtenerPorId($idSolicitud);
        
        if (!$solicitud) {
            $_SESSION['flash_error'] = 'Solicitud no encontrada';
            $this->redirigir('/estudiante/constancias/historial');
            return;
        }

        // Verificar que pertenece al estudiante
        $estudianteId = $solicitud['estudiante_id'] ?? $solicitud['id_estudiante'] ?? null;
        if ((int)$estudianteId !== (int)($_SESSION['estudiante_id'] ?? 0)) {
            $_SESSION['flash_error'] = 'No tiene permiso para acceder a esta constancia';
            $this->redirigir('/estudiante/constancias/historial');
            return;
        }

        // Solo permitir descargar si está aprobada
        if ($solicitud['estado'] !== 'aprobada') {
            $_SESSION['flash_error'] = 'Solo puede descargar constancias aprobadas';
            $this->redirigir('/estudiante/constancias/historial');
            return;
        }

        $this->redirigir('/constancias/imprimir/' . $idSolicitud);
    }

    /**
     * Mostrar perfil del estudiante
     */
    public function perfil(): void
    {
        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;
        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        $usuario = $this->estudianteService->obtenerUsuarioPorEstudiante($idEstudiante);
        $inscripcion = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);

        $this->render('estudiante/perfil', [
            'estudiante' => $estudiante,
            'usuario' => $usuario,
            'inscripcion' => $inscripcion
        ]);
    }

    /**
     * Actualizar perfil del estudiante
     */
    public function actualizarPerfil(): void
    {
        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Token de seguridad inválido');
            $this->redirigir('/estudiante/perfil');
            return;
        }

        $idEstudiante = $_SESSION['estudiante_id'] ?? 0;

        $datos = [
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'nombre_representante' => trim($_POST['nombre_representante'] ?? ''),
            'telefono_representante' => trim($_POST['telefono_representante'] ?? '')
        ];

        try {
            $this->estudianteService->actualizar($idEstudiante, $datos);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? null,
                'actualizar_perfil',
                'estudiantes',
                $idEstudiante,
                'Perfil de estudiante actualizado'
            );

            $this->setFlash('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            $this->setFlash('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }

        $this->redirigir('/estudiante/perfil');
    }
}
