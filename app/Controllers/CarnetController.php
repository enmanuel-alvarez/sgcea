<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\DocenteService;
use Src\Models\Services\InscripcionService;
use Src\Models\Services\ConfiguracionService;

class CarnetController extends Controller
{
    private EstudianteService $estudianteService;
    private DocenteService $docenteService;
    private InscripcionService $inscripcionService;
    private ConfiguracionService $configuracionService;

    public function __construct()
    {
        $this->estudianteService = new EstudianteService();
        $this->docenteService = new DocenteService();
        $this->inscripcionService = new InscripcionService();
        $this->configuracionService = new ConfiguracionService();
    }

    /**
     * Obtener configuraciones del sistema como array clave-valor
     */
    private function obtenerConfig(): array
    {
        $configuraciones = $this->configuracionService->obtenerTodas();
        $config = [];
        foreach ($configuraciones as $c) {
            $config[$c['clave']] = $c['valor'];
        }
        return $config;
    }

    /**
     * Generar / Visualizar Carnet Estudiantil
     */
    public function estudiante(?int $id = null): void
    {
        $usuarioTipo = $_SESSION['usuario_tipo'] ?? '';
        $idEstudiante = $id;

        // Si no se pasa ID, se busca el id_estudiante de la sesión actual
        if ($idEstudiante === null || $idEstudiante <= 0) {
            if ($usuarioTipo === 'estudiante') {
                if (isset($_SESSION['estudiante_id']) && (int)$_SESSION['estudiante_id'] > 0) {
                    $idEstudiante = (int)$_SESSION['estudiante_id'];
                } else if (isset($_SESSION['usuario_id'])) {
                    $estRepo = new \Src\Models\Repositories\EstudianteRepository();
                    $est = $estRepo->obtenerPorUsuario((int)$_SESSION['usuario_id']);
                    if ($est) {
                        $idEstudiante = (int)$est['id'];
                    }
                }
            }
        }

        if (!$idEstudiante) {
            $_SESSION['flash_error'] = 'No se encontró el estudiante especificado para generar el carnet.';
            $this->redirigir($usuarioTipo === 'admin' ? '/admin/estudiantes' : '/estudiante/perfil');
            return;
        }

        $estudiante = $this->estudianteService->obtenerPorId($idEstudiante);
        if (!$estudiante) {
            $_SESSION['flash_error'] = 'Estudiante no registrado en el sistema.';
            $this->redirigir($usuarioTipo === 'admin' ? '/admin/estudiantes' : '/estudiante/perfil');
            return;
        }

        $inscripcion = $this->inscripcionService->obtenerActivaPorEstudiante($idEstudiante);
        $config = $this->obtenerConfig();

        $this->render('carnets/estudiante', [
            'titulo' => 'Carnet Estudiantil - ' . ($estudiante['nombres'] ?? $estudiante['nombre'] ?? ''),
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcion,
            'config' => $config
        ]);
    }

    /**
     * Generar / Visualizar Carnet Docente
     */
    public function docente(?int $id = null): void
    {
        $usuarioTipo = $_SESSION['usuario_tipo'] ?? '';
        $idDocente = $id;

        // Si no se pasa ID, se busca el id_docente de la sesión actual
        if ($idDocente === null || $idDocente <= 0) {
            if ($usuarioTipo === 'docente') {
                if (isset($_SESSION['profesor_id']) && (int)$_SESSION['profesor_id'] > 0) {
                    $idDocente = (int)$_SESSION['profesor_id'];
                } else if (isset($_SESSION['usuario_id'])) {
                    $docRepo = new \Src\Models\Repositories\DocenteRepository();
                    $doc = $docRepo->obtenerPorUsuario((int)$_SESSION['usuario_id']);
                    if ($doc) {
                        $idDocente = (int)$doc['id'];
                    }
                }
            }
        }

        if (!$idDocente) {
            $_SESSION['flash_error'] = 'No se encontró el docente especificado para generar el carnet.';
            $this->redirigir($usuarioTipo === 'admin' ? '/admin/docentes' : '/docente');
            return;
        }

        $docente = $this->docenteService->obtenerPorId($idDocente);
        if (!$docente) {
            $_SESSION['flash_error'] = 'Docente no registrado en el sistema.';
            $this->redirigir($usuarioTipo === 'admin' ? '/admin/docentes' : '/docente');
            return;
        }

        $config = $this->obtenerConfig();

        $this->render('carnets/docente', [
            'titulo' => 'Carnet Docente - ' . ($docente['nombres'] ?? $docente['nombre'] ?? ''),
            'docente' => $docente,
            'config' => $config
        ]);
    }
}
