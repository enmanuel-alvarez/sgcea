<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\InstitucionRepository;
use Src\Models\Services\AuditoriaService;

class InstitucionService
{
    private InstitucionRepository $institucionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->institucionRepo = new InstitucionRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->institucionRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->institucionRepo->obtenerPorId($id);
    }

    public function crear(array $datos): int
    {
        // Validar nombre obligatorio
        if (empty($datos['nombre'])) {
            throw new \InvalidArgumentException('El nombre de la institución es obligatorio');
        }

        $id = $this->institucionRepo->crear($datos);

        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'instituciones',
            $id,
            "Institución creada: {$datos['nombre']}"
        );

        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        if (empty($datos['nombre'])) {
            throw new \InvalidArgumentException('El nombre de la institución es obligatorio');
        }

        $resultado = $this->institucionRepo->actualizar($id, $datos);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'instituciones',
                $id,
                "Institución actualizada: {$datos['nombre']}"
            );
        }

        return $resultado;
    }

    public function eliminar(int $id): bool
    {
        $resultado = $this->institucionRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'DELETE',
                'instituciones',
                $id,
                "Institución eliminada: ID $id"
            );
        }

        return $resultado;
    }

    public function contarTotal(): int
    {
        return $this->institucionRepo->contarTotal();
    }
}


