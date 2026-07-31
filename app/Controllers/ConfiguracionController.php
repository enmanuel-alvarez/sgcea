<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\ConfiguracionService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class ConfiguracionController extends Controller
{
    private ConfiguracionService $configuracionService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->configuracionService = new ConfiguracionService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Mostrar formulario de configuración
     */
    public function index(): void
    {
        $configuraciones = $this->configuracionService->obtenerTodas();
        
        // Organizar configuraciones por clave para acceso fácil
        $config = [];
        foreach ($configuraciones as $c) {
            $config[$c['clave']] = $c['valor'];
        }

        $this->render('configuracion/index', ['config' => $config]);
    }

    /**
     * Guardar configuración
     */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/configuracion');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/configuracion');
            return;
        }

        $datos = [
            'nombre_sistema' => trim($_POST['nombre_sistema'] ?? ''),
            'nombre_institucion' => trim($_POST['nombre_institucion'] ?? ''),
            'ano_academico_actual' => (int)($_POST['ano_academico_actual'] ?? date('Y')),
            'nota_minima_aprobacion' => (float)($_POST['nota_minima_aprobacion'] ?? 70),
            'porcentaje_asistencia_minimo' => (float)($_POST['porcentaje_asistencia_minimo'] ?? 70),
            'maximo_solicitudes_constancia' => (int)($_POST['maximo_solicitudes_constancia'] ?? 3),
            'mensaje_bienvenida' => trim($_POST['mensaje_bienvenida'] ?? ''),
            'direccion_institucion' => trim($_POST['direccion_institucion'] ?? ''),
            'telefono_institucion' => trim($_POST['telefono_institucion'] ?? ''),
            'email_institucion' => trim($_POST['email_institucion'] ?? '')
        ];

        // Validaciones básicas
        if (empty($datos['nombre_sistema'])) {
            $_SESSION['flash_error'] = 'El nombre del sistema es requerido';
            $this->redirigir('/configuracion');
            return;
        }

        if ($datos['nota_minima_aprobacion'] < 0 || $datos['nota_minima_aprobacion'] > 100) {
            $_SESSION['flash_error'] = 'La nota mínima debe estar entre 0 y 100';
            $this->redirigir('/configuracion');
            return;
        }

        if ($datos['porcentaje_asistencia_minimo'] < 0 || $datos['porcentaje_asistencia_minimo'] > 100) {
            $_SESSION['flash_error'] = 'El porcentaje de asistencia mínimo debe estar entre 0 y 100';
            $this->redirigir('/configuracion');
            return;
        }

        try {
            foreach ($datos as $clave => $valor) {
                $this->configuracionService->actualizar($clave, (string)$valor);
            }

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'ACTUALIZAR_CONFIGURACION',
                'configuraciones',
                null,
                'Configuración general del sistema actualizada'
            );

            $_SESSION['flash_success'] = 'Configuración guardada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar la configuración: ' . $e->getMessage();
        }

        $this->redirigir('/configuracion');
    }
}
