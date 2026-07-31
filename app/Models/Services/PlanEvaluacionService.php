<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Core\Database;

use Src\Models\Repositories\PlanEvaluacionRepository;
use Src\Models\Services\AuditoriaService;

class PlanEvaluacionService
{
    private PlanEvaluacionRepository $planRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        ?PlanEvaluacionRepository $planRepo = null,
        ?AuditoriaService $auditoriaService = null
    ) {
        $this->planRepo = $planRepo ?? new PlanEvaluacionRepository();
        $this->auditoriaService = $auditoriaService ?? new AuditoriaService();
    }

    public function obtenerPorAsignacion(int $asignacion_id): array
    {
        return $this->planRepo->obtenerPorAsignacion($asignacion_id);
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->planRepo->obtenerPorId($id);
    }

    public function obtenerConPromedios(int $asignacion_id): array
    {
        return $this->planRepo->obtenerConPromedios($asignacion_id);
    }

    public function crear(array $datos, int $usuario_id_sesion): int
    {
        $ponderacion_actual = $this->planRepo->sumarPonderacionPorAsignacion(
            $datos['asignacion_id'],
            $datos['id'] ?? null
        );
        
        $nueva_ponderacion = $ponderacion_actual + (float) $datos['ponderacion'];
        
        if ($nueva_ponderacion > 100) {
            throw new \Exception("La suma de ponderaciones no puede superar el 100%. Actual: $ponderacion_actual%, Nueva: {$datos['ponderacion']}%");
        }

        $id = $this->planRepo->crear($datos);
        
        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'planes_evaluacion',
            $id,
            "Evaluación creada: {$datos['nombre']} para asignación ID {$datos['asignacion_id']}"
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $resultado = $this->planRepo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'planes_evaluacion',
                $id,
                "Evaluación actualizada: {$datos['nombre']}"
            );
        }
        
        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $resultado = $this->planRepo->eliminar($id);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'planes_evaluacion',
                $id,
                "Evaluación eliminada: ID $id"
            );
        }
        
        return $resultado;
    }

    public function contarPorProfesor(int $profesor_id): int
    {
        $db = \Src\Core\Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) 
                FROM planes_evaluacion pe 
                INNER JOIN asignaciones a ON pe.asignacion_id = a.id 
                WHERE a.profesor_id = ? AND pe.estado = 'activo' AND a.estado = 'activa'";
        $stmt = $db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return (int) $stmt->fetchColumn();
    }

}
