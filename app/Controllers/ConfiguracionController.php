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

        $instRepo = new \Src\Models\Repositories\InstitucionRepository();
        $institucion = $instRepo->obtenerPorId(1) ?? [];
        if (!empty($institucion)) {
            $config['codigo_dependencia'] = $institucion['codigo_dependencia'] ?? ($config['codigo_dependencia'] ?? '');
            $config['direccion'] = $institucion['direccion'] ?? ($config['direccion'] ?? '');
            $config['telefono'] = $institucion['telefono'] ?? ($config['telefono'] ?? '');
            $config['email'] = $institucion['email'] ?? ($config['email'] ?? '');
            $config['director_nombre'] = $institucion['director_nombre'] ?? ($config['director_nombre'] ?? '');
            $config['director_cedula'] = $institucion['director_cedula'] ?? ($config['director_cedula'] ?? '');
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
            'codigo_dependencia' => trim($_POST['codigo_dependencia'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'director_nombre' => trim($_POST['director_nombre'] ?? ''),
            'director_cedula' => trim($_POST['director_cedula'] ?? ''),
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

            // Sincronizar en la tabla instituciones (ID 1)
            $instRepo = new \Src\Models\Repositories\InstitucionRepository();
            $instActual = $instRepo->obtenerPorId(1);
            $datosInst = [
                'nombre' => $datos['nombre_institucion'],
                'codigo_dependencia' => $datos['codigo_dependencia'],
                'direccion' => $datos['direccion'],
                'telefono' => $datos['telefono'],
                'email' => $datos['email'],
                'director_nombre' => $datos['director_nombre'],
                'director_cedula' => $datos['director_cedula']
            ];
            if ($instActual) {
                $instRepo->actualizar(1, $datosInst);
            } else {
                $instRepo->crear($datosInst);
            }

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'ACTUALIZAR_CONFIGURACION',
                'configuraciones',
                null,
                'Configuración general e institucional del sistema actualizada'
            );

            $_SESSION['flash_success'] = 'Configuración guardada exitosamente';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar la configuración: ' . $e->getMessage();
        }

        $this->redirigir('/admin/configuracion');
    }

    /**
     * Reiniciar todo el sistema a estado inicial (DANGER ZONE)
     * Elimina estudiantes, profesores, usuarios no-admin, materias, asignaciones, secciones, notas, etc.
     * Preserva los datos de usuarios administradores y todas las tablas de permisos.
     */
    public function reiniciarSistema(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/backup');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token CSRF inválido';
            $this->redirigir('/admin/backup');
            return;
        }

        $confirmarCheckbox = $_POST['confirmar_checkbox'] ?? '';
        $fraseConfirmacion = trim($_POST['frase_confirmacion'] ?? '');

        // Validación estricta
        if ($confirmarCheckbox !== '1' || strtoupper($fraseConfirmacion) !== 'REINICIAR SISTEMA SGCEA') {
            $_SESSION['flash_error'] = 'Verificación fallida: Debe activar el checkbox y escribir exactamente la frase "REINICIAR SISTEMA SGCEA".';
            $this->redirigir('/admin/backup');
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $usuarioActualId = (int)($_SESSION['usuario_id'] ?? 0);

            $db->beginTransaction();
            $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

            // Tablas operacionales a vaciar
            $tablasACrearVacías = [
                'calificaciones',
                'planes_evaluacion',
                'asistencias',
                'solicitudes_constancia',
                'asignaciones',
                'inscripciones',
                'materias',
                'secciones',
                'grados',
                'estudiantes',
                'profesores',
                'auditoria',
                'notificaciones',
                'intentos_login',
                'cache_dashboard_admin',
                'cache_dashboard_docente'
            ];

            foreach ($tablasACrearVacías as $tabla) {
                $stmt = $db->query("SHOW TABLES LIKE '{$tabla}'");
                if ($stmt && $stmt->rowCount() > 0) {
                    $db->exec("TRUNCATE TABLE `{$tabla}`");
                }
            }

            // Preservar a TODOS los usuarios administradores y sus permisos
            // Eliminar únicamente usuarios que NO sean admin (ej: docentes, estudiantes)
            $db->exec("DELETE FROM `usuario_permisos` WHERE `usuario_id` IN (SELECT id FROM `usuarios` WHERE `tipo_usuario` != 'admin')");
            $db->exec("DELETE FROM `usuarios` WHERE `tipo_usuario` != 'admin'");

            $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $db->commit();

            // Auditoría
            $this->auditoriaService->registrar(
                $usuarioActualId,
                'REINICIAR_SISTEMA',
                'sistema',
                null,
                'DANGER ZONE: Sistema reiniciado a estado inicial (Usuarios admin y permisos preservados)'
            );

            $_SESSION['flash_success'] = '¡El sistema ha sido reiniciado con éxito! Toda la data operativa (estudiantes, profesores, materias, secciones, asignaciones, calificaciones, asistencias) ha quedado en blanco. La cuenta de administrador y la matriz de permisos permanecen intactas.';
            $this->redirigir('/admin/backup');

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
                $db->rollBack();
            }
            $_SESSION['flash_error'] = 'Error crítico al reiniciar el sistema: ' . $e->getMessage();
            $this->redirigir('/admin/backup');
        }
    }

    /**
     * Mostrar vista interactiva de Políticas de Uso del Sistema (Accesible a todos los usuarios)
     */
    public function politicas(): void
    {
        $configuraciones = $this->configuracionService->obtenerTodas();
        $config = [];
        foreach ($configuraciones as $c) {
            $config[$c['clave']] = $c['valor'];
        }

        $this->render('politicas/index', [
            'titulo' => 'Políticas y Normativa de Uso del Sistema SGCEA',
            'config' => $config
        ]);
    }
}

