<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Services\ConfiguracionService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;
use Src\Core\Database;
use Exception;
use PDO;

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
            $this->redirigir('/admin/configuracion');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/admin/configuracion');
            return;
        }

        $datos = [
            'nombre_sistema' => trim($_POST['nombre_sistema'] ?? ''),
            'nombre_institucion' => trim($_POST['nombre_institucion'] ?? ''),
            'ano_academico_actual' => trim($_POST['ano_academico_actual'] ?? '2024-2025'),
            'nota_minima_aprobacion' => (float)($_POST['nota_minima_aprobacion'] ?? 10),
            'max_solicitudes_constancia' => (int)($_POST['max_solicitudes_constancia'] ?? 5)
        ];

        if (empty($datos['nombre_sistema'])) {
            $_SESSION['flash_error'] = 'El nombre del sistema es requerido';
            $this->redirigir('/admin/configuracion');
            return;
        }

        try {
            foreach ($datos as $clave => $valor) {
                $this->configuracionService->actualizar($clave, (string)$valor);
            }

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'ACTUALIZAR_CONFIGURACION',
                'configuraciones',
                null,
                'Configuración general del sistema actualizada'
            );

            $_SESSION['flash_success'] = 'Configuración guardada exitosamente';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar la configuración: ' . $e->getMessage();
        }

        $this->redirigir('/admin/configuracion');
    }

    /**
     * Danger Zone: Reiniciar data del sistema
     */
    public function reiniciarSistema(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/configuracion');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/admin/configuracion');
            return;
        }

        $confirmarCheckbox = $_POST['confirmar_checkbox'] ?? '';
        $fraseConfirmacion = trim($_POST['frase_confirmacion'] ?? '');

        // Validación estricta
        if ($confirmarCheckbox !== '1' || strtoupper($fraseConfirmacion) !== 'REINICIAR SISTEMA SGCEA') {
            $_SESSION['flash_error'] = 'Verificación fallida: Debe activar el checkbox y escribir exactamente la frase "REINICIAR SISTEMA SGCEA".';
            $this->redirigir('/admin/configuracion');
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $usuarioActualId = (int)($_SESSION['usuario_id'] ?? 0);

            $db->beginTransaction();
            $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

            // Limpieza de tablas operacionales
            $tablasACrearVacías = [
                'calificaciones',
                'planes_evaluacion',
                'asistencias',
                'solicitudes_constancia',
                'asignaciones',
                'estudiantes',
                'profesores',
                'auditoria',
                'cache_dashboard_admin',
                'cache_dashboard_docente',
                'intentos_login'
            ];

            foreach ($tablasACrearVacías as $tabla) {
                $db->exec("TRUNCATE TABLE `{$tabla}`");
            }

            // Preservar al usuario administrador actual para evitar bloqueos
            if ($usuarioActualId > 0) {
                $stmt = $db->prepare("DELETE FROM `usuario_permisos` WHERE usuario_id != ?");
                $stmt->execute([$usuarioActualId]);

                $stmt2 = $db->prepare("DELETE FROM `usuarios` WHERE id != ?");
                $stmt2->execute([$usuarioActualId]);
            }

            $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $db->commit();

            // Auditoría
            $this->auditoriaService->registrar(
                $usuarioActualId,
                'REINICIAR_SISTEMA',
                'sistema',
                null,
                'DANGER ZONE: Sistema reiniciado a estado inicial por el administrador'
            );

            $_SESSION['flash_success'] = '¡El sistema ha sido reiniciado con éxito! Todos los datos operativos han sido eliminados de forma segura preservando la cuenta activa.';
            $this->redirigir('/admin/configuracion');

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
                $db->rollBack();
            }
            $_SESSION['flash_error'] = 'Error crítico al reiniciar el sistema: ' . $e->getMessage();
            $this->redirigir('/admin/configuracion');
        }
    }
}
