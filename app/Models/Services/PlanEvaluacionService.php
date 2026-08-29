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

    public function crear(array $datos, int $usuario_id_sesion = 0): int
    {
        $ponderacion_actual = $this->planRepo->sumarPonderacionPorAsignacion(
            $datos['asignacion_id'] ?? $datos['id_asignacion'] ?? 0,
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
            "Evaluación creada: {$datos['nombre']} para asignación ID " . ($datos['asignacion_id'] ?? $datos['id_asignacion'] ?? 0)
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion = 0): bool
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

    public function eliminar(int $id, int $usuario_id_sesion = 0): bool
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
                WHERE a.profesor_id = ? AND (pe.estado = 'activo' OR pe.estado = '1' OR pe.estado = 1)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return (int) $stmt->fetchColumn();
    }

    public function crearLote(int $asignacion_id, array $actividades, int $usuario_id_sesion = 0): array
    {
        if (empty($actividades)) {
            throw new \Exception("Debe agregar al menos una actividad evaluativa.");
        }

        $ponderacion_actual = $this->planRepo->sumarPonderacionPorAsignacion($asignacion_id);
        $sumaNuevas = 0.0;
        foreach ($actividades as $act) {
            $pond = (float)($act['ponderacion'] ?? 0);
            if (empty(trim($act['nombre'] ?? '')) || $pond <= 0) {
                throw new \Exception("Todas las actividades deben incluir un nombre y ponderación mayor a 0%.");
            }
            $sumaNuevas += $pond;
        }

        if (($ponderacion_actual + $sumaNuevas) > 100.001) {
            $disponible = round(100 - $ponderacion_actual, 2);
            throw new \Exception("La suma de ponderaciones supera el 100%. Ponderación existente: {$ponderacion_actual}%, Nuevas: {$sumaNuevas}%. Máximo disponible: {$disponible}%");
        }

        $creados = [];
        foreach ($actividades as $act) {
            $actData = [
                'asignacion_id' => $asignacion_id,
                'id_asignacion' => $asignacion_id,
                'nombre' => trim($act['nombre']),
                'tipo' => $act['tipo'] ?? 'examen',
                'tipo_evaluacion' => $act['tipo'] ?? 'examen',
                'ponderacion' => (float)$act['ponderacion'],
                'fecha_programada' => !empty($act['fecha_programada']) ? $act['fecha_programada'] : date('Y-m-d'),
                'descripcion' => trim($act['descripcion'] ?? '')
            ];
            $id = $this->planRepo->crear($actData);
            $creados[] = $id;

            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'crear',
                'planes_evaluacion',
                $id,
                "Evaluación creada: {$actData['nombre']} ({$actData['ponderacion']}%) para asignación {$asignacion_id}"
            );
        }

        return $creados;
    }
}

