<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\EstudianteService;
use Src\Models\Services\InscripcionService;
use Src\Models\Services\CalificacionService;
use Src\Models\Services\AsistenciaService;
use Src\Models\Services\ConstanciaService;
use Src\Models\Services\RevisionService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class EstudianteController extends Controller
{
    private EstudianteService $estudianteService;
    private InscripcionService $inscripcionService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private ConstanciaService $constanciaService;
    private RevisionService $revisionService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->estudianteService = new EstudianteService();
        $this->inscripcionService = new InscripcionService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->constanciaService = new ConstanciaService();
        $this->revisionService = new RevisionService();
        $this->auditoriaService = new AuditoriaService();
    }

    private function obtenerEstudianteId(): int
    {
        if (isset($_SESSION['estudiante_id']) && (int)$_SESSION['estudiante_id'] > 0) {
            return (int)$_SESSION['estudiante_id'];
        }
        if (isset($_SESSION['usuario_id'])) {
            $estudianteRepo = new \Src\Models\Repositories\EstudianteRepository();
            $estudiante = $estudianteRepo->obtenerPorUsuario((int)$_SESSION['usuario_id']);
            if ($estudiante) {
                $_SESSION['estudiante_id'] = (int)$estudiante['id'];
                return (int)$estudiante['id'];
            }
        }
        return 0;
    }

    /**
     * Dashboard del estudiante
     */
    public function index(): void
    {
        $idEstudiante = $this->obtenerEstudianteId();
        
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
        $idEstudiante = $this->obtenerEstudianteId();
        $periodo = $_GET['periodo'] ?? null;
        
        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        $inscripcion = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);
        
        if (!$inscripcion) {
            $_SESSION['flash_error'] = 'No tiene una inscripción activa';
            $this->redirigir('/estudiante');
            return;
        }

        $datosBoletin = $this->calificacionService->obtenerBoletinConEstado($idEstudiante, $periodo);
        $periodosDisponibles = $this->calificacionService->obtenerPeriodosPorEstudiante($idEstudiante);

        $this->render('estudiante/boletin', [
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcion,
            'calificaciones' => $datosBoletin['materias'],
            'boletinCompleto' => $datosBoletin['boletin_completo'],
            'totalMaterias' => $datosBoletin['total_materias'],
            'materiasCompletas' => $datosBoletin['materias_completas'],
            'periodo' => $periodo,
            'periodosDisponibles' => $periodosDisponibles
        ]);
    }

    /**
     * Mostrar historial de asistencia
     */
    public function asistencia(): void
    {
        $idEstudiante = $this->obtenerEstudianteId();
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
        $idEstudiante = $this->obtenerEstudianteId();
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

        $idEstudiante = $this->obtenerEstudianteId();
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
        $idEstudiante = $this->obtenerEstudianteId();
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
        if ((int)$estudianteId !== $this->obtenerEstudianteId()) {
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
        $idEstudiante = $this->obtenerEstudianteId();
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

        $idEstudiante = $this->obtenerEstudianteId();

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

    /**
     * Listar solicitudes de revisión de notas del estudiante
     */
    public function revisiones(): void
    {
        $idEstudiante = $this->obtenerEstudianteId();
        $solicitudes = $this->revisionService->obtenerPorEstudiante($idEstudiante);
        $boletin = $this->calificacionService->obtenerBoletinPorEstudiante($idEstudiante);
        $inscripcion = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);

        // Obtenemos asignaciones para el selector de solicitudes
        $asignaciones = [];
        if ($inscripcion) {
            $db = \Src\Core\Database::getInstance()->getConnection();
            $sql = "SELECT a.id, m.nombre as materia_nombre 
                    FROM asignaciones a 
                    INNER JOIN materias m ON a.materia_id = m.id 
                    WHERE a.seccion_id = ? AND a.ano_academico = ? AND a.estado = 1 
                    ORDER BY m.nombre";
            $stmt = $db->prepare($sql);
            $stmt->execute([(int)$inscripcion['seccion_id'], $inscripcion['ano_academico']]);
            $asignaciones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($asignaciones as &$asg) {
                $activas = $this->revisionService->contarActivasPorEstudianteYAsignacion($idEstudiante, (int)$asg['id']);
                $asg['tiene_revision_activa'] = ($activas > 0);
            }
            unset($asg);
        }

        $this->render('estudiante/revisiones/index', [
            'solicitudes' => $solicitudes,
            'asignaciones' => $asignaciones,
            'boletin' => $boletin
        ]);
    }

    /**
     * Guardar nueva solicitud de revisión de notas
     */
    public function guardarRevision(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/estudiante/revisiones');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/estudiante/revisiones');
            return;
        }

        $idEstudiante = $this->obtenerEstudianteId();
        $idAsignacion = (int)($_POST['id_asignacion'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? '');

        if ($idAsignacion === 0 || empty($motivo)) {
            $_SESSION['flash_error'] = 'Por favor complete la asignatura y el motivo de la solicitud.';
            $this->redirigir('/estudiante/revisiones');
            return;
        }

        try {
            $this->revisionService->solicitar([
                'estudiante_id' => $idEstudiante,
                'asignacion_id' => $idAsignacion,
                'motivo' => $motivo
            ], $_SESSION['usuario_id'] ?? 0);

            $_SESSION['flash_success'] = 'Solicitud de revisión enviada exitosamente al profesor.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al enviar la solicitud: ' . $e->getMessage();
        }

        $this->redirigir('/estudiante/revisiones');
    }
}
