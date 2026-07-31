<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\PermisoRepository;
use Src\Models\Services\AuditoriaService;

class PermisoService
{
    private PermisoRepository $permisoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->permisoRepo = new PermisoRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->permisoRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->permisoRepo->obtenerPorId($id);
    }

    public function obtenerPorModulo(string $modulo): array
    {
        return $this->permisoRepo->obtenerPorModulo($modulo);
    }

    public function obtenerPermisosPorUsuario(int $usuario_id): array
    {
        return $this->permisoRepo->obtenerPermisosPorUsuario($usuario_id);
    }

    public function tienePermiso(int $usuario_id, string $permiso_nombre): bool
    {
        return $this->permisoRepo->tienePermiso($usuario_id, $permiso_nombre);
    }

    public function asignarPermisos(int $usuario_id, array $permiso_ids): bool
    {
        $resultado = $this->permisoRepo->asignarPermisos($usuario_id, $permiso_ids);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'usuario_permisos',
                $usuario_id,
                'Permisos actualizados'
            );
        }

        return $resultado;
    }
}