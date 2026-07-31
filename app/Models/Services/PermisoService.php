<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\PermisoRepository;
use Src\Models\Services\AuditoriaService;

class PermisoService
{
    private PermisoRepository $permisoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        ?PermisoRepository $permisoRepo = null,
        ?AuditoriaService $auditoriaService = null
    ) {
        $this->permisoRepo = $permisoRepo ?? new PermisoRepository();
        $this->auditoriaService = $auditoriaService ?? new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->permisoRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->permisoRepo->obtenerPorId($id);
    }

    public function obtenerPorRol(int $rol_id): array
    {
        return $this->permisoRepo->obtenerPorRol($rol_id);
    }

    public function tienePermiso(int $rol_id, string $modulo, string $accion): bool
    {
        return $this->permisoRepo->tienePermiso($rol_id, $modulo, $accion);
    }

    public function asignarPermisosRol(int $rol_id, array $permiso_ids, int $usuario_id_sesion): bool
    {
        $resultado = $this->permisoRepo->asignarPermisosRol($rol_id, $permiso_ids);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'permisos_rol',
                $rol_id,
                "Permisos actualizados para el rol ID $rol_id"
            );
        }

        return $resultado;
    }

    public function obtenerPermisosPorUsuario(int $usuario_id): array
    {
        return $this->permisoRepo->obtenerPermisosPorUsuario($usuario_id);
    }

    public function asignarPermisos(int $usuario_id, array $permiso_ids, ?int $asignado_por = null): bool
    {
        return $this->permisoRepo->asignarPermisos($usuario_id, $permiso_ids, $asignado_por);
    }

    public function asignarPermisosUsuario(int $usuario_id, array $permiso_ids, ?int $asignado_por = null): bool
    {
        return $this->permisoRepo->asignarPermisosUsuario($usuario_id, $permiso_ids, $asignado_por);
    }


    public function obtenerPermisosPorUsuario(int $usuario_id): array
    {
        return $this->permisoRepo->obtenerPermisosPorUsuario($usuario_id);
    }

}
