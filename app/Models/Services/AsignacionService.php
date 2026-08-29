<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\AsignacionRepository;
use Src\Models\Services\AuditoriaService;

class AsignacionService
{
    private AsignacionRepository $asignacionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->asignacionRepo = new AsignacionRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    public function obtenerTodos(): array
    {
        return $this->asignacionRepo->obtenerTodos();
    }

    /**
     * Obtiene todas las asignaciones con detalles (join de docente, materia, sección, grado)
     */
    public function obtenerTodosConDetalles(): array
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

    public function obtenerPorProfesor(int $profesor_id): array
    {
        return $this->obtenerPorDocente($profesor_id);
    }

    public function obtenerConPlanEvaluacion(int $docente_id): array
    {
        return $this->asignacionRepo->obtenerConPlanEvaluacion($docente_id);
    }

    /**
     * Crea una asignación a partir de los datos del AdminController
     */
    public function crear(array $datos): int
    {
        // Mapeo de nombres de campos del AdminController a los del repositorio
        $datosRepo = [
            'profesor_id'  => $datos['id_profesor'] ?? $datos['profesor_id'] ?? 0,
            'materia_id'   => $datos['id_materia'] ?? $datos['materia_id'] ?? 0,
            'seccion_id'   => $datos['id_seccion'] ?? $datos['seccion_id'] ?? 0,
            'ano_lectivo'  => $datos['ano_academico'] ?? $datos['ano_lectivo'] ?? date('Y'),
            'estado'       => ($datos['activo'] ?? 1) ? 'activa' : 'inactiva'
        ];

        // Verificar duplicado (invierte lógica: verificarDuplicado retorna true si NO existe duplicado)
        if (!$this->asignacionRepo->verificarDuplicado(
            $datosRepo['profesor_id'],
            $datosRepo['materia_id'],
            $datosRepo['seccion_id'],
            $datosRepo['ano_lectivo']
        )) {
            throw new \Exception('Ya existe una asignación similar para este docente, materia y sección en este año lectivo');
        }

        $id = $this->asignacionRepo->crear($datosRepo);

        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'asignaciones',
            $id,
            "Asignación creada: Docente ID {$datosRepo['profesor_id']}, Materia ID {$datosRepo['materia_id']}"
        );

        return $id;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $datosRepo = [
            'profesor_id'  => $datos['id_profesor'] ?? $datos['profesor_id'] ?? 0,
            'materia_id'   => $datos['id_materia'] ?? $datos['materia_id'] ?? 0,
            'seccion_id'   => $datos['id_seccion'] ?? $datos['seccion_id'] ?? 0,
            'ano_lectivo'  => $datos['ano_academico'] ?? $datos['ano_lectivo'] ?? date('Y'),
            'estado'       => ($datos['activo'] ?? 1) ? 'activa' : 'inactiva'
        ];

        $resultado = $this->asignacionRepo->actualizar($id, $datosRepo);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'UPDATE',
                'asignaciones',
                $id,
                "Asignación actualizada: ID $id"
            );
        }

        return $resultado;
    }

    /**
     * Elimina una asignación (soft delete)
     */
    public function eliminar(int $id): bool
    {
        $resultado = $this->asignacionRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? 0,
                'DELETE',
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
                WHERE a.profesor_id = ? AND a.estado = 'activa' AND i.estado = 'activo'";
        $stmt = $db->prepare($sql);
        $stmt->execute([$profesor_id]);
        return (int) $stmt->fetchColumn();
    }
}