<?php

declare(strict_types=1);

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Services\DashboardService;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\DocenteService;
use Src\Models\Services\MateriaService;
use Src\Models\Services\CalificacionService;
use Src\Models\Services\AsistenciaService;
use Src\Models\Services\SeccionService;
use Src\Models\Services\ReporteService;
use Src\Models\Services\ConfiguracionService;

class ReportesController extends Controller
{
    private DashboardService $dashboardService;
    private EstudianteService $estudianteService;
    private DocenteService $docenteService;
    private MateriaService $materiaService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private SeccionService $seccionService;
    private ReporteService $reporteService;
    private ConfiguracionService $configuracionService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->estudianteService = new EstudianteService();
        $this->docenteService = new DocenteService();
        $this->materiaService = new MateriaService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->seccionService = new SeccionService();
        $this->reporteService = new ReporteService();
        $this->configuracionService = new ConfiguracionService();
    }

    /**
     * Centro de Reportes del Sistema SGCEA
     */
    public function index(): void
    {
        $tipoReporte = $_GET['tipo'] ?? 'general';
        $seccionId = isset($_GET['seccion_id']) ? (int)$_GET['seccion_id'] : null;
        $estudianteId = isset($_GET['estudiante_id']) ? (int)$_GET['estudiante_id'] : null;

        $secciones = $this->seccionService->obtenerTodosConDetalles();
        $estudiantes = $this->estudianteService->obtenerTodosConDetalles();
        $materias = $this->materiaService->obtenerTodos();
        
        $configuraciones = $this->configuracionService->obtenerTodas();
        $config = [];
        foreach ($configuraciones as $c) {
            $config[$c['clave']] = $c['valor'];
        }

        $datosReporte = null;

        switch ($tipoReporte) {
            case 'cuadro_honor':
                $datosReporte = $this->reporteService->obtenerCuadroDeHonor($seccionId, 25);
                break;

            case 'riesgo_academico':
                $datosReporte = $this->reporteService->obtenerEstudiantesEnRiesgo($seccionId, 10.0);
                break;

            case 'sabana_seccion':
                if ($seccionId) {
                    $datosReporte = $this->reporteService->obtenerSabanaNotasSeccion($seccionId);
                }
                break;

            case 'ausentismo_critico':
                $datosReporte = $this->reporteService->obtenerAlertaAusentismoCritico($seccionId, 20.0);
                break;

            case 'carga_docente':
                $datosReporte = $this->reporteService->obtenerResumenCargaHorariaDocentes();
                break;

            case 'ficha_360':
                if ($estudianteId) {
                    $this->redirigir('/reportes/ficha360/' . $estudianteId);
                    return;
                }
                break;

            case 'estudiantes':
                $datosReporte = $this->estudianteService->obtenerTodosConDetalles();
                break;

            case 'docentes':
                $datosReporte = $this->docenteService->obtenerTodosConDetalles();
                break;

            case 'materias':
                $datosReporte = $this->materiaService->obtenerTodasConEstadisticas();
                break;

            default:
                $datosReporte = $this->dashboardService->obtenerDatosAdmin();
                break;
        }

        $this->render('reportes/index', [
            'tipoReporte' => $tipoReporte,
            'datosReporte' => $datosReporte,
            'secciones' => $secciones,
            'estudiantes' => $estudiantes,
            'materias' => $materias,
            'seccionId' => $seccionId,
            'estudianteId' => $estudianteId,
            'config' => $config
        ]);
    }

    /**
     * Vista de Ficha 360° imprimible del estudiante
     */
    public function ficha360(int $id): void
    {
        $ficha = $this->reporteService->obtenerFicha360Estudiante($id);
        
        if (empty($ficha['estudiante'])) {
            $_SESSION['flash_error'] = 'Estudiante no encontrado para Ficha 360°';
            $this->redirigir('/reportes');
            return;
        }

        $configuraciones = $this->configuracionService->obtenerTodas();
        $config = [];
        foreach ($configuraciones as $c) {
            $config[$c['clave']] = $c['valor'];
        }

        $this->render('reportes/ficha360', [
            'titulo' => 'Ficha 360° del Estudiante',
            'ficha' => $ficha,
            'config' => $config
        ]);
    }

    /**
     * Exportación CSV de datos
     */
    public function exportarCSV(string $tipo): void
    {
        $datos = [];
        $nombreArchivo = "reporte_{$tipo}_" . date('Ymd') . ".csv";

        switch ($tipo) {
            case 'cuadro_honor':
                $datos = $this->reporteService->obtenerCuadroDeHonor(null, 100);
                $columnas = ['cedula', 'nombre', 'apellido', 'grado', 'seccion', 'promedio_general'];
                break;

            case 'riesgo_academico':
                $datos = $this->reporteService->obtenerEstudiantesEnRiesgo(null, 10.0);
                $columnas = ['cedula', 'nombre', 'apellido', 'grado', 'seccion', 'promedio_general', 'materias_reprobadas'];
                break;

            case 'estudiantes':
                $datos = $this->estudianteService->obtenerTodosConDetalles();
                $columnas = ['cedula', 'nombre', 'apellido', 'genero', 'telefono_representante'];
                break;

            case 'ausentismo_critico':
                $datos = $this->reporteService->obtenerAlertaAusentismoCritico(null, 20.0);
                $columnas = ['cedula', 'nombre', 'apellido', 'grado', 'seccion', 'total_clases', 'ausencias', 'pct_ausencia'];
                break;

            default:
                $_SESSION['flash_error'] = 'Tipo de reporte CSV no soportado';
                $this->redirigir('/reportes');
                return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, array_map('ucfirst', $columnas));

        foreach ($datos as $fila) {
            $valores = [];
            foreach ($columnas as $col) {
                $valores[] = $fila[$col] ?? '';
            }
            fputcsv($output, $valores);
        }

        fclose($output);
        exit;
    }

    public function rendimiento(): void
    {
        $secciones = $this->seccionService->obtenerTodosConDetalles();
        $materias = $this->materiaService->obtenerTodos();

        $this->render('reportes/rendimiento', [
            'titulo' => 'Reporte de Rendimiento Académico',
            'secciones' => $secciones ?? [],
            'materias' => $materias ?? []
        ]);
    }

    public function asistencia(): void
    {
        $secciones = $this->seccionService->obtenerTodosConDetalles();

        $this->render('reportes/asistencia', [
            'titulo' => 'Reporte de Asistencia',
            'secciones' => $secciones ?? []
        ]);
    }

    public function estudiantesPorSeccion(int $idSeccion): void
    {
        $estudiantes = $this->estudianteService->obtenerPorSeccion($idSeccion);
        $this->json(['success' => true, 'data' => $estudiantes]);
    }

    public function obtenerEstudiante(int $id): void
    {
        $estudiante = $this->estudianteService->obtenerPorId($id);

        if (!$estudiante) {
            $this->json(['success' => false, 'message' => 'Estudiante no encontrado'], 404);
            return;
        }

        $this->json(['success' => true, 'data' => $estudiante]);
    }
}