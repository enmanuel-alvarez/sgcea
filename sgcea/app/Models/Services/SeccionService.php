<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\SeccionRepository;
use Src\Models\Services\AuditoriaService;

class SeccionService
{
    private SeccionRepository $seccionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(SeccionRepository $seccionRepo, AuditoriaService $auditoriaService)
    {
        $this->seccionRepo = $seccionRepo;
        $this->auditoriaService = $auditoriaService;
    }

    public function obtenerTodos(): array
    {
        return $this->seccionRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->seccionRepo->obtenerPorId($id);
    }

    public function obtenerActivas(): array
    {
        return $this->seccionRepo->obtenerActivas();
    }

    public function obtenerPorGrado(int $grado_id): array
    {
        return $this->seccionRepo->obtenerPorGrado($grado_id);
    }

    public function verificarCupo(int $seccion_id, int $cupo_maximo): bool
    {
        $cupo_actual = $this->seccionRepo->obtenerCupoActual($seccion_id);
        return $cupo_actual < $cupo_maximo;
    }

    public function crear(array $datos, int $usuario_id_sesion): int
    {
        $id = $this->seccionRepo->crear($datos);
        
        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'secciones',
            $id,
            "Sección creada: {$datos['nombre']}"
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $resultado = $this->seccionRepo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'secciones',
                $id,
                "Sección actualizada: {$datos['nombre']}"
            );
        }
        
        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $resultado = $this->seccionRepo->eliminar($id);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'secciones',
                $id,
                "Sección eliminada: ID $id"
            );
        }
        
        return $resultado;
    }
}
