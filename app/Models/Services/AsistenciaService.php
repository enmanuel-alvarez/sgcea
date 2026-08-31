<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\AsistenciaRepository;
use Src\Models\Services\AuditoriaService;

class AsistenciaService
{
    private AsistenciaRepository $asistenciaRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        ?AsistenciaRepository $asistenciaRepo = null,
        ?AuditoriaService $auditoriaService = null
    ) {
        $this->asistenciaRepo = $asistenciaRepo ?? new AsistenciaRepository();
        $this->auditoriaService = $auditoriaService ?? new AuditoriaService();
    }

    public function obtenerPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->asistenciaRepo->obtenerPorEstudiante($estudiante_id, $desde, $hasta);
    }

    public function obtenerResumenPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->asistenciaRepo->obtenerResumenPorEstudiante($estudiante_id, $desde, $hasta);
    }

    public function registrarMasiva(array $asistencias, int $usuario_id_sesion = 0): bool
    {
        if (empty($asistencias)) {
            throw new \Exception('No hay asistencias para registrar');
        }

        $resultado = $this->asistenciaRepo->registrarMasiva($asistencias);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'registrar_masiva',
                'asistencias',
                0,
                "Se registraron " . count($asistencias) . " asistencias"
            );
        }

        return $resultado;
    }

    public function contarPorEstudianteYEstado(int $estudiante_id, string $estado, ?string $desde = null, ?string $hasta = null): int
    {
        return $this->asistenciaRepo->contarPorEstudianteYEstado($estudiante_id, $estado, $desde, $hasta);
    }

    public function obtenerPorFechaYAsignacion(string $fecha, int $asignacion_id): array
    {
        return $this->asistenciaRepo->obtenerPorAsignacionYFecha($asignacion_id, $fecha);
    }

    public function obtenerResumenGeneral(): array
    {
        return $this->asistenciaRepo->obtenerResumenGeneral();
    }

    public function obtenerResumenPorSeccion(int $seccion_id, string $fecha_inicio, string $fecha_fin): array
    {
        return $this->asistenciaRepo->obtenerResumenPorSeccion($seccion_id, $fecha_inicio, $fecha_fin);
    }

    public function obtenerPorcentajePorEstudiante(int $estudiante_id): float
    {
        return $this->asistenciaRepo->obtenerPorcentajePorEstudiante($estudiante_id);
    }

    public function obtenerDetallePorEstudiante(int $estudiante_id, $mes = null, $ano = null): array
    {
        return $this->asistenciaRepo->obtenerDetallePorEstudiante($estudiante_id, $mes, $ano);
    }
}


