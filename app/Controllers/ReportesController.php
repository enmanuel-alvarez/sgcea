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

class ReportesController extends Controller
{
    private DashboardService $dashboardService;
    private EstudianteService $estudianteService;
    private DocenteService $docenteService;
    private MateriaService $materiaService;
    private CalificacionService $calificacionService;
    private AsistenciaService $asistenciaService;
    private SeccionService $seccionService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->estudianteService = new EstudianteService();
        $this->docenteService = new DocenteService();
        $this->materiaService = new MateriaService();
        $this->calificacionService = new CalificacionService();
        $this->asistenciaService = new AsistenciaService();
        $this->seccionService = new SeccionService();
    }

    public function index(): void
    {
        $tipoReporte = $_GET['tipo'] ?? 'general';

        $datos = [
            'tipoReporte' => $tipoReporte,
            'estadisticas' => null,
            'tablaDatos' => null
        ];

        switch ($tipoReporte) {
            case 'estudiantes':
                $datos['tablaDatos'] = $this->estudianteService->obtenerTodosConDetalles();
                break;

            case 'docentes':
                $datos['tablaDatos'] = $this->docenteService->obtenerTodosConDetalles();
                break;

            case 'materias':
                $datos['tablaDatos'] = $this->materiaService->obtenerTodasConEstadisticas();
                break;

            case 'rendimiento_materia':
                $idMateria = (int)($_GET['materia'] ?? 0);
                $datos['tablaDatos'] = $this->calificacionService->obtenerRendimientoPorMateria($idMateria);
                break;

            case 'asistencia_general':
                $mes = $_GET['mes'] ?? date('m');
                $ano = $_GET['ano'] ?? date('Y');
                $datos['tablaDatos'] = $this->asistenciaService->obtenerResumenGeneral($mes, $ano);
                break;

            case 'promedios_por_seccion':
                $datos['tablaDatos'] = $this->calificacionService->obtenerPromediosPorSeccion(null);
                break;

            default:
                $datos['estadisticas'] = $this->dashboardService->obtenerDatosAdmin();
                break;
        }

        $this->render('reportes/index', $datos);
    }

    public function estudiantesPorSeccion(int $idSeccion): void
    {
        $estudiantes = $this->estudianteService->obtenerPorSeccion($idSeccion);
        $this->json(['success' => true, 'data' => $estudiantes]);
    }

    public function calificacionesPorPeriodo(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $periodo = $_POST['periodo'] ?? '';
        $idSeccion = (int)($_POST['id_seccion'] ?? 0);

        if (empty($periodo)) {
            $this->json(['success' => false, 'message' => 'Período requerido'], 400);
            return;
        }

        $calificaciones = $this->calificacionService->obtenerPorPeriodoYSeccion($periodo, $idSeccion);
        $this->json(['success' => true, 'data' => $calificaciones]);
    }

    public function exportarCSV(string $tipo): void
    {
        $datos = [];

        switch ($tipo) {
            case 'estudiantes':
                $datos = $this->estudianteService->obtenerTodosConDetalles();
                $nombreArchivo = 'estudiantes.csv';
                $columnas = ['Cédula', 'Nombre', 'Apellido', 'Fecha Nacimiento', 'Género', 'Teléfono', 'Dirección'];
                break;

            case 'docentes':
                $datos = $this->docenteService->obtenerTodosConDetalles();
                $nombreArchivo = 'docentes.csv';
                $columnas = ['Cédula', 'Nombre', 'Apellido', 'Especialidad', 'Teléfono', 'Email'];
                break;

            default:
                $_SESSION['flash_error'] = 'Tipo de reporte no válido';
                $this->redirigir('/reportes');
                return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, $columnas);

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