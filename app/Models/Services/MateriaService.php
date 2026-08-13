<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\MateriaRepository;
use Src\Models\Services\AuditoriaService;

class MateriaService
{
    private MateriaRepository $materiaRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->materiaRepo = new MateriaRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->materiaRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->materiaRepo->obtenerPorId($id);
    }

    public function obtenerActivas(): array
    {
        return $this->materiaRepo->obtenerActivas();
    }

    public function obtenerPorGrado(int $grado_id): array
    {
        return $this->materiaRepo->obtenerPorGrado($grado_id);
    }

    /**
     * Crea una materia a partir de los datos enviados por el controlador
     */
    public function crear(array $datos): int
    {
        // Mapeo de campos del AdminController a lo que espera el repositorio
        $datosRepo = [
            'nombre'       => $datos['nombre'],
            'descripcion'  => $datos['descripcion'] ?? null,
            'estado'       => ($datos['activo'] ?? 1) ? 'activo' : 'inactivo'
        ];
        $id = $this->materiaRepo->crear($datosRepo);

        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'materias',
            $id,
            "Materia creada: {$datos['nombre']}"
        );

        return $id;
    }

    /**
     * Actualiza una materia
     */
    public function actualizar(int $id, array $datos): bool
    {
        $datosRepo = [
            'nombre'       => $datos['nombre'],
            'descripcion'  => $datos['descripcion'] ?? null,
            'estado'       => ($datos['activo'] ?? 1) ? 'activo' : 'inactivo'
        ];
        $resultado = $this->materiaRepo->actualizar($id, $datosRepo);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'materias',
                $id,
                "Materia actualizada: {$datos['nombre']}"
            );
        }

        return $resultado;
    }

    /**
     * Elimina una materia (soft delete)
     */
    public function eliminar(int $id): bool
    {
        $resultado = $this->materiaRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'DELETE',
                'materias',
                $id,
                "Materia eliminada: ID $id"
            );
        }

        return $resultado;
    }

    /**
     * Obtiene materias con estadísticas adicionales (total de asignaciones activas)
     */
    public function obtenerTodasConEstadisticas(): array
    {
        $db = \Src\Core\Database::getInstance()->getConnection();
        $sql = "SELECT m.*, 
                       (SELECT COUNT(*) FROM asignaciones a WHERE a.materia_id = m.id AND a.estado = 'activa') as total_asignaciones
                FROM materias m 
                WHERE m.estado != 'eliminado'
                ORDER BY m.nombre";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

        public function obtenerTodas(): array
    {
        return $this->obtenerTodos();
    }
}