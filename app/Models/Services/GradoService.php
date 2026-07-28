<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\GradoRepository;
use Src\Models\Services\AuditoriaService;

class GradoService
{
    private GradoRepository $gradoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(GradoRepository $gradoRepo, AuditoriaService $auditoriaService)
    {
        $this->gradoRepo = $gradoRepo;
        $this->auditoriaService = $auditoriaService;
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

    public function crear(array $datos, int $usuario_id_sesion): int
    {
        $id = $this->gradoRepo->crear($datos);
        
        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'grados',
            $id,
            "Grado creado: {$datos['nombre']}"
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $resultado = $this->gradoRepo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'grados',
                $id,
                "Grado actualizado: {$datos['nombre']}"
            );
        }
        
        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $resultado = $this->gradoRepo->eliminar($id);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'grados',
                $id,
                "Grado eliminado: ID $id"
            );
        }
        
        return $resultado;
    }
}
