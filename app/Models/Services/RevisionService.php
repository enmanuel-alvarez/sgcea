<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\RevisionRepository;
use Src\Models\Services\AuditoriaService;

class RevisionService
{
    private RevisionRepository $revisionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        ?RevisionRepository $revisionRepo = null,
        ?AuditoriaService $auditoriaService = null
    ) {
        $this->revisionRepo = $revisionRepo ?? new RevisionRepository();
        $this->auditoriaService = $auditoriaService ?? new AuditoriaService();
    }

    public function solicitar(array $datos, int $usuario_id = 0): int
    {
        if (empty($datos['estudiante_id']) || empty($datos['asignacion_id']) || empty(trim($datos['motivo'] ?? ''))) {
            throw new \Exception('Debe especificar la asignatura y el motivo de la revisión.');
        }

        // Limitar a máximo 1 revisión activa (pendiente o en revisión) por materia
        $activas = $this->revisionRepo->contarActivasPorEstudianteYAsignacion(
            (int)$datos['estudiante_id'],
            (int)$datos['asignacion_id']
        );
        if ($activas > 0) {
            throw new \Exception('Ya posee una solicitud de revisión activa (pendiente) para esta asignatura. Debe esperar a que el profesor la responda antes de solicitar otra.');
        }

        $id = $this->revisionRepo->crear($datos);

        $this->auditoriaService->registrar(
            $usuario_id,
            'SOLICITAR_REVISION_NOTA',
            'solicitudes_revision',
            $id,
            "El estudiante solicito revision para la asignacion {$datos['asignacion_id']}"
        );

        return $id;
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        return $this->revisionRepo->obtenerPorEstudiante($estudiante_id);
    }

    public function obtenerPorProfesor(int $profesor_id): array
    {
        return $this->revisionRepo->obtenerPorProfesor($profesor_id);
    }

    public function responder(int $id, string $estado, string $respuesta, int $usuario_id = 0): bool
    {
        if (!in_array($estado, ['aprobada', 'rechazada', 'en_revision'])) {
            throw new \Exception('Estado de revisión no válido.');
        }

        $res = $this->revisionRepo->responder($id, $estado, trim($respuesta));

        if ($res) {
            $this->auditoriaService->registrar(
                $usuario_id,
                'RESPONDER_REVISION_NOTA',
                'solicitudes_revision',
                $id,
                "Se marco la revision {$id} como {$estado}"
            );
        }

        return $res;
    }

    public function contarPendientesPorProfesor(int $profesor_id): int
    {
        return $this->revisionRepo->contarPendientesPorProfesor($profesor_id);
    }

    public function contarActivasPorEstudianteYAsignacion(int $estudiante_id, int $asignacion_id): int
    {
        return $this->revisionRepo->contarActivasPorEstudianteYAsignacion($estudiante_id, $asignacion_id);
    }
}
