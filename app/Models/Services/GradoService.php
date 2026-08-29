<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\GradoRepository;
use Src\Models\Services\AuditoriaService;

class GradoService
{
    private GradoRepository $gradoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->gradoRepo = new GradoRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->gradoRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->gradoRepo->obtenerPorId($id);
    }

    public function obtenerActivos(): array
    {
        return $this->gradoRepo->obtenerActivos();
    }

    public function crear(array $datos): int
    {
        $id = $this->gradoRepo->crear($datos);

        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'grados',
            $id,
            "Grado creado: {$datos['nombre']}"
        );

        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $resultado = $this->gradoRepo->actualizar($id, $datos);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'grados',
                $id,
                "Grado actualizado: {$datos['nombre']}"
            );
        }

        return $resultado;
    }

    public function eliminar(int $id): bool
    {
        $resultado = $this->gradoRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'DELETE',
                'grados',
                $id,
                "Grado eliminado: ID $id"
            );
        }

        return $resultado;
    }
}
