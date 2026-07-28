<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\CalificacionRepository;
use Src\Models\Repositories\PlanEvaluacionRepository;
use Src\Models\Services\AuditoriaService;
use Src\Models\Services\DashboardService;

class CalificacionService
{
    private CalificacionRepository $calificacionRepo;
    private PlanEvaluacionRepository $planRepo;
    private AuditoriaService $auditoriaService;
    private DashboardService $dashboardService;

    public function __construct(
        CalificacionRepository $calificacionRepo,
        PlanEvaluacionRepository $planRepo,
        AuditoriaService $auditoriaService,
        DashboardService $dashboardService
    ) {
        $this->calificacionRepo = $calificacionRepo;
        $this->planRepo = $planRepo;
        $this->auditoriaService = $auditoriaService;
        $this->dashboardService = $dashboardService;
    }

    public function obtenerPorEstudianteYAsignacion(int $estudiante_id, int $asignacion_id): array
    {
        return $this->calificacionRepo->obtenerPorEstudianteYAsignacion($estudiante_id, $asignacion_id);
    }

    public function obtenerPorEstudiante(int $estudiante_id, string $ano_lectivo): array
    {
        return $this->calificacionRepo->obtenerPorEstudiante($estudiante_id, $ano_lectivo);
    }

    public function registrarNota(int $estudiante_id, int $plan_evaluacion_id, float $nota, int $profesor_id): int
    {
        $plan = $this->planRepo->obtenerPorId($plan_evaluacion_id);
        if (!$plan) {
            throw new \Exception('Plan de evaluación no encontrado');
        }

        if ($nota < 0 || $nota > 100) {
            throw new \Exception('La nota debe estar entre 0 y 100');
        }

        $existente = $this->calificacionRepo->obtenerPorEstudianteYPlan($estudiante_id, $plan_evaluacion_id);
        
        if ($existente) {
            $this->calificacionRepo->actualizar($existente['id'], [
                'nota' => $nota,
                'fecha_registro' => date('Y-m-d H:i:s')
            ]);
            
            $id = $existente['id'];
            $accion = 'actualizar';
        } else {
            $id = $this->calificacionRepo->crear([
                'estudiante_id' => $estudiante_id,
                'plan_evaluacion_id' => $plan_evaluacion_id,
                'nota' => $nota,
                'profesor_id' => $profesor_id
            ]);
            $accion = 'crear';
        }

        $this->dashboardService->actualizarCacheDocente($profesor_id);

        $this->auditoriaService->registrar(
            $profesor_id,
            $accion,
            'calificaciones',
            $id,
            "Nota $accion para estudiante ID $estudiante_id: $nota"
        );

        return $id;
    }

    public function calcularPromedioPorMateria(int $estudiante_id, int $asignacion_id): float
    {
        return $this->calificacionRepo->calcularPromedioPorMateria($estudiante_id, $asignacion_id);
    }
}
