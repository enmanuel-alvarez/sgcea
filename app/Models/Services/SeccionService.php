<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\SeccionRepository;
use Src\Models\Services\AuditoriaService;

class SeccionService
{
    private SeccionRepository $seccionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->seccionRepo = new SeccionRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Obtiene todas las secciones con detalles del grado
     */
    public function obtenerTodosConDetalles(): array
    {
        return $this->seccionRepo->obtenerTodos();
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

    public function crear(array $datos): int
    {
        // Adaptamos nombres si vienen de AdminController
        $datosRepo = [
            'nombre' => $datos['nombre'],
            'grado_id' => $datos['id_grado'] ?? $datos['grado_id'] ?? 0,
            'cupo_maximo' => $datos['cup_maximo'] ?? $datos['cupo_maximo'] ?? 30,
            'estado' => ($datos['activo'] ?? 1) ? 'activo' : 'inactivo'
        ];

        $id = $this->seccionRepo->crear($datosRepo);

        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'secciones',
            $id,
            "Sección creada: {$datos['nombre']}"
        );

        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datosRepo = [
            'nombre' => $datos['nombre'],
            'grado_id' => $datos['id_grado'] ?? $datos['grado_id'] ?? 0,
            'cupo_maximo' => $datos['cup_maximo'] ?? $datos['cupo_maximo'] ?? 30,
            'estado' => ($datos['activo'] ?? 1) ? 'activo' : 'inactivo'
        ];

        $resultado = $this->seccionRepo->actualizar($id, $datosRepo);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'secciones',
                $id,
                "Sección actualizada: {$datos['nombre']}"
            );
        }

        return $resultado;
    }

    public function eliminar(int $id): bool
    {
        $resultado = $this->seccionRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'DELETE',
                'secciones',
                $id,
                "Sección eliminada: ID $id"
            );
        }

        return $resultado;
    }
}
