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

    public function obtenerPermisosCalculados(int $usuario_id, int $rol_id): array
    {
        return $this->permisoRepo->obtenerPermisosCalculados($usuario_id, $rol_id);
    }

    public function guardarExcepcionesUsuario(int $usuario_id, array $conceder_ids, array $revocar_ids = []): bool
    {
        $resultado = $this->permisoRepo->guardarExcepcionesUsuario($usuario_id, $conceder_ids, $revocar_ids, $_SESSION['usuario_id'] ?? null);

        if ($resultado && isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $usuario_id) {
            $rolId = (int)($_SESSION['usuario_rol_id'] ?? 1);
            $_SESSION['usuario_permisos'] = $this->obtenerPermisosCalculados($usuario_id, $rolId);
        }

        return $resultado;
    }

    public function asignarPermisos(int $usuario_id, array $permiso_ids): bool
    {
        $resultado = $this->permisoRepo->asignarPermisos($usuario_id, $permiso_ids);

        if ($resultado) {
            // Sincronizar permisos en sesión activa si coincide el ID del usuario
            if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $usuario_id) {
                $rolId = (int)($_SESSION['usuario_rol_id'] ?? 1);
                $_SESSION['usuario_permisos'] = $this->obtenerPermisosCalculados($usuario_id, $rolId);
            }

            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'usuario_permisos',
                $usuario_id,
                'Permisos de usuario actualizados'
            );
        }

        return $resultado;
    }

    /**
     * Obtiene el catálogo de permisos estructurado por Vistas y Acciones
     */
    public function obtenerMatrizPermisosPorVista(): array
    {
        $todos = $this->obtenerTodos();
        $vistas = [];

        foreach ($todos as $p) {
            $nombre = $p['nombre'];
            $partes = explode('.', $nombre);
            
            // Determinar la vista / módulo
            if (count($partes) >= 2) {
                $moduloKey = $partes[0];
                $vistaKey = $partes[1];
                $accionKey = $partes[2] ?? 'ver';
                $vistaTitulo = ucfirst($moduloKey) . ' - ' . ucfirst($vistaKey);
            } else {
                $vistaKey = $nombre;
                $accionKey = 'ver';
                $vistaTitulo = ucfirst($nombre);
            }

            $esAccesoVista = ($accionKey === 'ver' || $accionKey === 'dashboard' || count($partes) == 2);

            if (!isset($vistas[$vistaTitulo])) {
                $vistas[$vistaTitulo] = [
                    'titulo' => $vistaTitulo,
                    'modulo' => $partes[0] ?? 'general',
                    'vista_key' => $vistaKey,
                    'acceso_vista' => null,
                    'acciones' => []
                ];
            }

            if ($esAccesoVista && $vistas[$vistaTitulo]['acceso_vista'] === null) {
                $vistas[$vistaTitulo]['acceso_vista'] = $p;
            } else {
                $vistas[$vistaTitulo]['acciones'][] = $p;
            }
        }

        return $vistas;
    }
}

