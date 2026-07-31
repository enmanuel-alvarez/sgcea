<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\MateriaRepository;
use Src\Models\Services\AuditoriaService;

class MateriaService
{
    private MateriaRepository $materiaRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(MateriaRepository $materiaRepo, AuditoriaService $auditoriaService)
    {
        $this->materiaRepo = $materiaRepo;
        $this->auditoriaService = $auditoriaService;
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

    public function crear(array $datos, int $usuario_id_sesion): int
    {
        $id = $this->materiaRepo->crear($datos);
        
        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'materias',
            $id,
            "Materia creada: {$datos['nombre']}"
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $resultado = $this->materiaRepo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'materias',
                $id,
                "Materia actualizada: {$datos['nombre']}"
            );
        }
        
        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $resultado = $this->materiaRepo->eliminar($id);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'materias',
                $id,
                "Materia eliminada: ID $id"
            );
        }
        
        return $resultado;
    }

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

}
