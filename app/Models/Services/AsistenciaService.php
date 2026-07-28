<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\AsistenciaRepository;
use Src\Models\Services\AuditoriaService;

class AsistenciaService
{
    private AsistenciaRepository $asistenciaRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(AsistenciaRepository $asistenciaRepo, AuditoriaService $auditoriaService)
    {
        $this->asistenciaRepo = $asistenciaRepo;
        $this->auditoriaService = $auditoriaService;
    }

    public function obtenerPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->asistenciaRepo->obtenerPorEstudiante($estudiante_id, $desde, $hasta);
    }

    public function obtenerResumenPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        return $this->asistenciaRepo->obtenerResumenPorEstudiante($estudiante_id, $desde, $hasta);
    }

    public function registrarMasiva(array $asistencias, int $usuario_id_sesion): bool
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
}
