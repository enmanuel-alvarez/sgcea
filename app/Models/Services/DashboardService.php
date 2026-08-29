<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\DashboardCacheRepository;
use Src\Models\Repositories\EstudianteRepository;
use Src\Models\Repositories\DocenteRepository;
use Src\Models\Repositories\MateriaRepository;
use Src\Models\Repositories\SeccionRepository;
use Src\Models\Repositories\InscripcionRepository;
use Src\Models\Repositories\ConstanciaRepository;
use Src\Models\Repositories\AsignacionRepository;
use Src\Models\Repositories\CalificacionRepository;

class DashboardService
{
    private DashboardCacheRepository $cacheRepo;
    private EstudianteRepository $estudianteRepo;
    private DocenteRepository $docenteRepo;
    private MateriaRepository $materiaRepo;
    private SeccionRepository $seccionRepo;
    private InscripcionRepository $inscripcionRepo;
    private ConstanciaRepository $constanciaRepo;
    private AsignacionRepository $asignacionRepo;
    private CalificacionRepository $calificacionRepo;

    public function __construct()
    {
        $this->cacheRepo        = new DashboardCacheRepository();
        $this->estudianteRepo   = new EstudianteRepository();
        $this->docenteRepo      = new DocenteRepository();
        $this->materiaRepo      = new MateriaRepository();
        $this->seccionRepo      = new SeccionRepository();
        $this->inscripcionRepo  = new InscripcionRepository();
        $this->constanciaRepo   = new ConstanciaRepository();
        $this->asignacionRepo   = new AsignacionRepository();
        $this->calificacionRepo = new CalificacionRepository();
    }

    public function obtenerDatosAdmin(): array
    {
        $this->cacheRepo->inicializarAdmin();

        if ($this->cacheRepo->esDesactualizadoAdmin()) {
            $datos = [
                'total_estudiantes'      => count($this->estudianteRepo->obtenerTodos()),
                'total_docentes'         => count($this->docenteRepo->obtenerTodos()),
                'total_materias'         => count($this->materiaRepo->obtenerActivas()),
                'total_secciones'        => count($this->seccionRepo->obtenerActivas()),
                'inscripciones_ano_actual' => $this->inscripcionRepo->contarPorAnoLectivo(date('Y')),
                'constancias_pendientes'   => $this->constanciaRepo->contarPorEstado('pendiente')
            ];

            $this->cacheRepo->actualizarAdmin($datos);
        }

        return $this->cacheRepo->obtenerAdmin() ?? [];
    }

    public function obtenerDatosDocente(int $docente_id): array
    {
        if ($this->cacheRepo->esDesactualizadoDocente($docente_id)) {
            $asignaciones = $this->asignacionRepo->obtenerPorDocente($docente_id);
            $total_asignaciones = count($asignaciones);

            $total_estudiantes = 0;
            $suma_promedios = 0;
            $contador_promedios = 0;

            foreach ($asignaciones as $asignacion) {
                $estudiantes = $this->estudianteRepo->obtenerEstudiantesPorSeccion($asignacion['seccion_id']);
                $total_estudiantes += count($estudiantes);

                $promedio = $this->calificacionRepo->calcularPromedioPorMateria(
                    $estudiantes[0]['id'] ?? 0,
                    $asignacion['id']
                );

                if ($promedio > 0) {
                    $suma_promedios += $promedio;
                    $contador_promedios++;
                }
            }

            $promedio_general = $contador_promedios > 0 ? $suma_promedios / $contador_promedios : 0;

            $this->cacheRepo->actualizarDocente($docente_id, [
                'total_asignaciones'    => $total_asignaciones,
                'total_estudiantes'     => $total_estudiantes,
                'promedio_calificaciones' => round($promedio_general, 2)
            ]);
        }

        $cache = $this->cacheRepo->obtenerDocente($docente_id);
        return $cache ?: [
            'total_asignaciones'     => 0,
            'total_estudiantes'      => 0,
            'promedio_calificaciones'=> 0
        ];
    }

    public function actualizarCacheDocente(int $docente_id): void
    {
        $this->cacheRepo->esDesactualizadoDocente($docente_id, 0);
        $this->obtenerDatosDocente($docente_id);
    }
}
