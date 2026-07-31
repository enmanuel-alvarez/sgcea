<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\InscripcionRepository;

class InscripcionService
{
    private InscripcionRepository $inscripcionRepo;

    public function __construct(
        ?InscripcionRepository $inscripcionRepo = null
    ) {
        $this->inscripcionRepo = $inscripcionRepo ?? new InscripcionRepository();
    }

    public function obtenerActivaPorEstudiante(int $estudiante_id): ?array
    {
        return $this->inscripcionRepo->obtenerActivaPorEstudiante($estudiante_id);
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        return $this->inscripcionRepo->obtenerPorEstudiante($estudiante_id);
    }
}
