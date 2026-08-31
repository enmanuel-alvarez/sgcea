<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Core\Database;
use PDO;
use Exception;

class BackupService
{
    private PDO $db;

    /**
     * Lista de tablas exportables e importables del sistema
     */
    private const TABLAS_PERMITIDAS = [
        'usuarios' => 'Usuarios del Sistema',
        'usuario_permisos' => 'Permisos Asignados a Usuarios',
        'estudiantes' => 'Estudiantes Matriculados',
        'profesores' => 'Docentes y Profesores',
        'materias' => 'Asignaturas / Materias',
        'secciones' => 'Secciones y Aulas',
        'grados' => 'Grados Escolares',
        'asignaciones' => 'Asignaciones Académicas (Docente-Materia)',
        'planes_evaluacion' => 'Planes de Evaluación',
        'calificaciones' => 'Registro de Calificaciones',
        'asistencias' => 'Control de Asistencia',
        'solicitudes_constancia' => 'Solicitudes de Constancias',
        'configuraciones' => 'Parámetros de Configuración'
    ];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTablasExportables(): array
    {
        return self::TABLAS_PERMITIDAS;
    }

    /**
     * Genera un JSON descargable con la data de las tablas seleccionadas
     */
    public function exportarJSON(array $tablasSeleccionadas): array
    {
        $dataExport = [];
        $tablasIncluidas = [];

        foreach ($tablasSeleccionadas as $tabla) {
            if (!array_key_exists($tabla, self::TABLAS_PERMITIDAS)) {
                continue;
            }

            // Consultar todos los registros de la tabla
            $stmt = $this->db->query("SELECT * FROM `{$tabla}`");
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dataExport[$tabla] = $registros;
            $tablasIncluidas[] = $tabla;
        }

        return [
            'metadata' => [
                'sistema' => 'SGCEA',
                'version' => '1.0',
                'fecha_exportacion' => date('Y-m-d H:i:s'),
                'exportado_por' => $_SESSION['usuario_id'] ?? 0,
                'tablas_incluidas' => $tablasIncluidas,
                'total_tablas' => count($tablasIncluidas)
            ],
            'payload' => $dataExport
        ];
    }

    /**
     * Importa data desde un arreglo JSON previamente validado
     */
    public function importarJSON(array $jsonStruct, array $tablasAImportar): bool
    {
        if (empty($jsonStruct['payload']) || !is_array($jsonStruct['payload'])) {
            throw new Exception('Estructura JSON inválida o sin datos.');
        }

        $payload = $jsonStruct['payload'];

        // ORDEN DE DEPENDENCIA DE TABLAS PARA INSERCIÓN SEGURA
        $ordenProcesamiento = [
            'grados',
            'secciones',
            'materias',
            'profesores',
            'usuarios',
            'usuario_permisos',
            'estudiantes',
            'asignaciones',
            'planes_evaluacion',
            'calificaciones',
            'asistencias',
            'solicitudes_constancia',
            'configuraciones'
        ];

        try {
            $this->db->beginTransaction();
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 0;");

            foreach ($ordenProcesamiento as $tabla) {
                // Verificar si la tabla está en el archivo y fue seleccionada para importar
                if (!in_array($tabla, $tablasAImportar) || empty($payload[$tabla])) {
                    continue;
                }

                $registros = $payload[$tabla];
                if (!is_array($registros) || count($registros) === 0) {
                    continue;
                }

                // Obtener nombres de columnas
                $columnas = array_keys($registros[0]);
                $columnasEscapadas = array_map(fn($c) => "`{$c}`", $columnas);
                $placeholders = array_fill(0, count($columnas), '?');
                
                $updateClause = array_map(fn($c) => "`{$c}` = VALUES(`{$c}`)", $columnas);

                $sql = sprintf(
                    "INSERT INTO `%s` (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
                    $tabla,
                    implode(', ', $columnasEscapadas),
                    implode(', ', $placeholders),
                    implode(', ', $updateClause)
                );

                $stmt = $this->db->prepare($sql);

                foreach ($registros as $row) {
                    $stmt->execute(array_values($row));
                }
            }

            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $this->db->rollBack();
            error_log("Error en Importación JSON: " . $e->getMessage());
            throw new Exception("Falló la importación: " . $e->getMessage());
        }
    }
}


