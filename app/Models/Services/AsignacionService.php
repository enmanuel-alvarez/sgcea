<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\AsignacionRepository;
use Src\Models\Services\AuditoriaService;

class AsignacionService
{
    private AsignacionRepository $asignacionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(AsignacionRepository $asignacionRepo, AuditoriaService $auditoriaService)
    {
        $this->asignacionRepo = $asignacionRepo;
        $this->auditoriaService = $auditoriaService;
    }

    public function obtenerTodos(): array
    {
        return $this->asignacionRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->asignacionRepo->obtenerPorId($id);
    }

    public function obtenerPorDocente(int $docente_id): array
    {
        return $this->asignacionRepo->obtenerPorDocente($docente_id);
    }

    public function obtenerConPlanEvaluacion(int $docente_id): array
    {
        return $this->asignacionRepo->obtenerConPlanEvaluacion($docente_id);
    }

    public function crear(array $datos, int $usuario_id_sesion): int
    {
        if (!$this->asignacionRepo->verificarDuplicado(
            $datos['docente_id'],
            $datos['materia_id'],
            $datos['seccion_id'],
            $datos['ano_lectivo']
        )) {
            throw new \Exception('Ya existe una asignación similar para este docente, materia y sección en este año lectivo');
        }

        $id = $this->asignacionRepo->crear($datos);
        
        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'asignaciones',
            $id,
            "Asignación creada: Docente ID {$datos['docente_id']}, Materia ID {$datos['materia_id']}"
        );
        
        return $id;
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $resultado = $this->asignacionRepo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'asignaciones',
                $id,
                "Asignación actualizada: ID $id"
            );
        }
        
        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $resultado = $this->asignacionRepo->eliminar($id);
        
        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'asignaciones',
                $id,
                "Asignación eliminada: ID $id"
            );
        }
        
        return $resultado;
    }

    public function contarEstudiantesPorProfesor(int $profesor_id): int
    {
        $db = \Src\Core\Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(DISTINCT i.estudiante_id) 
                FROM asignaciones a 
                INNER JOIN inscripciones i ON a.seccion_id = i.seccion_id 
                WHERE a.docente_id = ? AND a.estado = 'activa' AND i.estado = 'activo'";
        $stmt = $db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return (int) $stmt->fetchColumn();
    }

}
