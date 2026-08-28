<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Services\BackupService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;
use Exception;

class BackupController extends Controller
{
    private BackupService $backupService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->backupService = new BackupService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Mostrar vista de respaldos (Importar / Exportar JSON)
     */
    public function index(): void
    {
        $tablas = $this->backupService->obtenerTablasExportables();
        $this->render('admin/backup/index', ['tablas' => $tablas]);
    }

    /**
     * Exportar entidades seleccionadas en archivo JSON descargable
     */
    public function exportar(): void
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

        $tablasSeleccionadas = $_POST['tablas'] ?? [];
        if (empty($tablasSeleccionadas) || !is_array($tablasSeleccionadas)) {
            $_SESSION['flash_error'] = 'Debe seleccionar al menos una tabla para exportar.';
            $this->redirigir('/admin/backup');
            return;
        }

        try {
            $backupData = $this->backupService->exportarJSON($tablasSeleccionadas);
            $jsonString = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $filename = 'sgcea_backup_' . date('Ymd_His') . '.json';

            // Auditoría
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'EXPORTAR_BACKUP_JSON',
                'sistema',
                null,
                'Exportación JSON generada: ' . implode(', ', $tablasSeleccionadas)
            );

            // Descarga HTTP
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($jsonString));
            echo $jsonString;
            exit;

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al exportar los datos: ' . $e->getMessage();
            $this->redirigir('/admin/backup');
        }
    }

    /**
     * Importar data desde un archivo JSON subido
     */
    public function importar(): void
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

        if (empty($_FILES['archivo_json']['tmp_name']) || $_FILES['archivo_json']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Debe seleccionar un archivo JSON válido.';
            $this->redirigir('/admin/backup');
            return;
        }

        $tablasAImportar = $_POST['tablas_importar'] ?? [];
        if (empty($tablasAImportar) || !is_array($tablasAImportar)) {
            $_SESSION['flash_error'] = 'Debe seleccionar al menos una entidad para importar.';
            $this->redirigir('/admin/backup');
            return;
        }

        try {
            $contenido = file_get_contents($_FILES['archivo_json']['tmp_name']);
            $jsonStruct = json_decode($contenido, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonStruct)) {
                throw new Exception('El archivo subido no es un JSON válido.');
            }

            $this->backupService->importarJSON($jsonStruct, $tablasAImportar);

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'IMPORTAR_BACKUP_JSON',
                'sistema',
                null,
                'Importación JSON realizada con éxito'
            );

            $_SESSION['flash_success'] = '¡Datos importados satisfactoriamente desde el respaldo JSON!';
            $this->redirigir('/admin/backup');

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Falló la importación del respaldo: ' . $e->getMessage();
            $this->redirigir('/admin/backup');
        }
    }
}
