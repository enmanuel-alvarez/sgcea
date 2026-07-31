<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\ConstanciaService;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class ConstanciaController extends Controller
{
    private ConstanciaService $constanciaService;
    private EstudianteService $estudianteService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->constanciaService = new ConstanciaService();
        $this->estudianteService = new EstudianteService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Listado de solicitudes de constancias (para admin)
     */
    public function index(): void
    {
        $estado = $_GET['estado'] ?? 'pendiente';
        $solicitudes = $this->constanciaService->obtenerTodas($estado);

        $this->render('admin/constancias/index', [
            'solicitudes' => $solicitudes,
            'estado' => $estado
        ]);
    }

    /**
     * Aprobar una solicitud de constancia
     */
    public function aprobar(int $idSolicitud): void
    {
        $solicitud = $this->constanciaService->obtenerPorId($idSolicitud);
        
        if (!$solicitud) {
            $_SESSION['flash_error'] = 'Solicitud no encontrada';
            $this->redirigir('/admin/constancias');
            return;
        }

        if ($solicitud['estado'] !== 'pendiente') {
            $_SESSION['flash_error'] = 'La solicitud ya ha sido procesada';
            $this->redirigir('/admin/constancias');
            return;
        }

        try {
            $this->constanciaService->aprobar($idSolicitud, $_SESSION['usuario_id']);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'APROBAR_CONSTANCIA',
                'solicitudes_constancia',
                $idSolicitud,
                "Solicitud {$idSolicitud} aprobada"
            );

            $_SESSION['flash_success'] = 'Constancia aprobada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al aprobar la constancia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/constancias');
    }

    /**
     * Rechazar una solicitud de constancia
     */
    public function rechazar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/constancias');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/admin/constancias');
            return;
        }

        $idSolicitud = (int)($_POST['id_solicitud'] ?? 0);
        $motivoRechazo = trim($_POST['motivo_rechazo'] ?? '');

        if ($idSolicitud === 0) {
            $_SESSION['flash_error'] = 'Solicitud no válida';
            $this->redirigir('/admin/constancias');
            return;
        }

        $solicitud = $this->constanciaService->obtenerPorId($idSolicitud);
        if (!$solicitud) {
            $_SESSION['flash_error'] = 'Solicitud no encontrada';
            $this->redirigir('/admin/constancias');
            return;
        }

        if ($solicitud['estado'] !== 'pendiente') {
            $_SESSION['flash_error'] = 'La solicitud ya ha sido procesada';
            $this->redirigir('/admin/constancias');
            return;
        }

        if (empty($motivoRechazo)) {
            $_SESSION['flash_error'] = 'Debe especificar un motivo de rechazo';
            $this->redirigir('/admin/constancias');
            return;
        }

        try {
            $this->constanciaService->rechazar($idSolicitud, $_SESSION['usuario_id'], $motivoRechazo);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'RECHAZAR_CONSTANCIA',
                'solicitudes_constancia',
                $idSolicitud,
                "Solicitud {$idSolicitud} rechazada: {$motivoRechazo}"
            );

            $_SESSION['flash_success'] = 'Solicitud rechazada correctamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al rechazar la solicitud: ' . $e->getMessage();
        }

        $this->redirigir('/admin/constancias');
    }

    /**
     * Imprimir constancia
     */
    public function imprimir(int $idSolicitud): void
    {
        $solicitud = $this->constanciaService->obtenerPorId($idSolicitud);
        
        if (!$solicitud) {
            $_SESSION['flash_error'] = 'Solicitud no encontrada';
            $this->redirigir('/');
            return;
        }

        // Verificar que esté aprobada (excepto para admin)
        $esAdmin = in_array('admin_ver_constancias', $_SESSION['usuario_permisos'] ?? []);
        if (!$esAdmin && $solicitud['estado'] !== 'aprobada') {
            $_SESSION['flash_error'] = 'Solo puede imprimir constancias aprobadas';
            $this->redirigir('/');
            return;
        }

        $estudiante = $this->estudianteService->obtenerPorId($solicitud['id_estudiante']);
        $inscripcion = $this->constanciaService->obtenerInscripcionPorEstudiante($solicitud['id_estudiante']);
        
        // Obtener configuraciones del sistema
        $configuraciones = $this->constanciaService->obtenerConfiguraciones();

        // Generar contenido según el tipo de constancia
        $contenido = $this->generarContenidoConstancia($solicitud, $estudiante, $inscripcion, $configuraciones);

        $this->render('constancias/imprimir', [
            'solicitud' => $solicitud,
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcion,
            'contenido' => $contenido,
            'configuraciones' => $configuraciones,
            'layout_print' => true
        ], true);
    }

    /**
     * Generar contenido HTML de la constancia según el tipo
     */
    private function generarContenidoConstancia(array $solicitud, array $estudiante, ?array $inscripcion, array $configuraciones): string
    {
        $fechaEmision = date('d/m/Y');
        $nombreCompleto = $estudiante['nombre'] . ' ' . $estudiante['apellido'];
        $cedula = $estudiante['cedula'];
        
        switch ($solicitud['tipo']) {
            case 'estudio':
                return $this->plantillaConstanciaEstudio(
                    $nombreCompleto, $cedula, $inscripcion, $configuraciones, $fechaEmision
                );
            
            case 'notas':
                return $this->plantillaConstanciaNotas(
                    $nombreCompleto, $cedula, $inscripcion, $configuraciones, $fechaEmision
                );
            
            case 'conducta':
                return $this->plantillaConstanciaConducta(
                    $nombreCompleto, $cedula, $inscripcion, $configuraciones, $fechaEmision
                );
            
            case 'personalizada':
            default:
                return $this->plantillaConstanciaPersonalizada(
                    $nombreCompleto, $cedula, $solicitud['motivo'], $configuraciones, $fechaEmision
                );
        }
    }

    /**
     * Plantilla para constancia de estudio
     */
    private function plantillaConstanciaEstudio(string $nombre, string $cedula, ?array $inscripcion, array $config, string $fecha): string
    {
        $grado = $inscripcion['grado_nombre'] ?? 'N/A';
        $seccion = $inscripcion['seccion_nombre'] ?? 'N/A';
        $anoAcademico = $config['ano_academico_actual'] ?? date('Y');
        
        return <<<HTML
        <div class="constancia-content">
            <h2>CONSTANCIA DE ESTUDIO</h2>
            <p class="numero">Número: CST-{$anoAcademico}-{$inscripcion['id']}</p>
            
            <p>
                Quien suscribe, Director(a) Académico de <strong>{$config['nombre_institucion']}</strong>,
                hace constar que el(la) ciudadano(a) <strong>{$nombre}</strong>, 
                titular de la Cédula de Identidad N° <strong>{$cedula}</strong>,
                es estudiante regular de esta institución, cursando actualmente el 
                <strong>{$grado}</strong> grado, sección <strong>{$seccion}</strong>,
                durante el año académico <strong>{$anoAcademico}</strong>.
            </p>
            
            <p>
                Se expide la presente constancia a petición de la parte interesada
                para los fines que estime conveniente.
            </p>
            
            <p class="fecha">Dada en la ciudad, a los {$fecha}.</p>
            
            <div class="firma">
                <p>_____________________________</p>
                <p>Director(a) Académico</p>
                <p>{$config['nombre_institucion']}</p>
            </div>
        </div>
HTML;
    }

    /**
     * Plantilla para constancia de notas
     */
    private function plantillaConstanciaNotas(string $nombre, string $cedula, ?array $inscripcion, array $config, string $fecha): string
    {
        $grado = $inscripcion['grado_nombre'] ?? 'N/A';
        $seccion = $inscripcion['seccion_nombre'] ?? 'N/A';
        $anoAcademico = $config['ano_academico_actual'] ?? date('Y');
        
        // Obtener promedio del estudiante
        $promedio = $inscripcion['promedio_general'] ?? 'N/A';
        
        return <<<HTML
        <div class="constancia-content">
            <h2>CONSTANCIA DE NOTAS</h2>
            <p class="numero">Número: CST-{$anoAcademico}-{$inscripcion['id']}</p>
            
            <p>
                Quien suscribe, Director(a) Académico de <strong>{$config['nombre_institucion']}</strong>,
                certifica que el(la) estudiante <strong>{$nombre}</strong>, 
                titular de la Cédula de Identidad N° <strong>{$cedula}</strong>,
                matriculado(a) en el <strong>{$grado}</strong> grado, sección <strong>{$seccion}</strong>,
                ha obtenido un promedio general de <strong>{$promedio}</strong> puntos
                durante el año académico <strong>{$anoAcademico}</strong>.
            </p>
            
            <p>
                Se expide la presente constancia para los fines legales correspondientes.
            </p>
            
            <p class="fecha">Dada en la ciudad, a los {$fecha}.</p>
            
            <div class="firma">
                <p>_____________________________</p>
                <p>Director(a) Académico</p>
                <p>{$config['nombre_institucion']}</p>
            </div>
        </div>
HTML;
    }

    /**
     * Plantilla para constancia de conducta
     */
    private function plantillaConstanciaConducta(string $nombre, string $cedula, ?array $inscripcion, array $config, string $fecha): string
    {
        $grado = $inscripcion['grado_nombre'] ?? 'N/A';
        $seccion = $inscripcion['seccion_nombre'] ?? 'N/A';
        $anoAcademico = $config['ano_academico_actual'] ?? date('Y');
        
        return <<<HTML
        <div class="constancia-content">
            <h2>CONSTANCIA DE CONDUCTA</h2>
            <p class="numero">Número: CST-{$anoAcademico}-{$inscripcion['id']}</p>
            
            <p>
                La Dirección de <strong>{$config['nombre_institucion']}</strong> hace constar
                que el(la) estudiante <strong>{$nombre}</strong>, titular de la Cédula de Identidad
                N° <strong>{$cedula}</strong>, matriculado(a) en el <strong>{$grado}</strong> grado,
                sección <strong>{$seccion}</strong>, ha demostrado durante el año académico
                <strong>{$anoAcademico}</strong> un comportamiento ejemplar, cumpliendo con las
                normas de convivencia establecidas en esta institución educativa.
            </p>
            
            <p>
                Se expide la presente a solicitud del interesado para los fines que considere oportunos.
            </p>
            
            <p class="fecha">Dada en la ciudad, a los {$fecha}.</p>
            
            <div class="firma">
                <p>_____________________________</p>
                <p>Director(a) Académico</p>
                <p>{$config['nombre_institucion']}</p>
            </div>
        </div>
HTML;
    }

    /**
     * Plantilla para constancia personalizada
     */
    private function plantillaConstanciaPersonalizada(string $nombre, string $cedula, string $motivo, array $config, string $fecha): string
    {
        $anoAcademico = $config['ano_academico_actual'] ?? date('Y');
        
        return <<<HTML
        <div class="constancia-content">
            <h2>CONSTANCIA</h2>
            <p class="numero">Número: CST-{$anoAcademico}-PERSONALIZADA</p>
            
            <p>
                Por medio de la presente, <strong>{$config['nombre_institucion']}</strong>
                hace constar lo siguiente:
            </p>
            
            <p class="motivo">
                {$motivo}
            </p>
            
            <p>
                El(la) ciudadano(a) <strong>{$nombre}</strong>, titular de la Cédula de Identidad
                N° <strong>{$cedula}</strong>, es miembro de nuestra comunidad estudiantil.
            </p>
            
            <p>
                Se expide la presente constancia para los fines que el interesado estime conveniente.
            </p>
            
            <p class="fecha">Dada en la ciudad, a los {$fecha}.</p>
            
            <div class="firma">
                <p>_____________________________</p>
                <p>Director(a) Académico</p>
                <p>{$config['nombre_institucion']}</p>
            </div>
        </div>
HTML;
    }
}
