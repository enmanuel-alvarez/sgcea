<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\ConstanciaRepository;
use Src\Models\Services\AuditoriaService;

class ConstanciaService
{
    private ConstanciaRepository $constanciaRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->constanciaRepo = new ConstanciaRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Obtiene todas las solicitudes de constancia con detalles
     */
    public function obtenerTodasConDetalles(): array
    {
        return $this->constanciaRepo->obtenerTodos();
    }

    public function obtenerTodos(): array
    {
        return $this->constanciaRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->constanciaRepo->obtenerPorId($id);
    }

    public function obtenerPendientes(): array
    {
        return $this->constanciaRepo->obtenerPendientes();
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        return $this->constanciaRepo->obtenerPorEstudiante($estudiante_id);
    }

    public function obtenerAprobadasPorEstudiante(int $estudiante_id): array
    {
        return $this->constanciaRepo->obtenerAprobadasPorEstudiante($estudiante_id);
    }

    public function solicitar(array $datos, int $usuario_id_sesion): int
    {
        $estudiante_id = $datos['estudiante_id'];

        $pendientes = $this->constanciaRepo->contarPendientesPorEstudiante($estudiante_id);
        if ($pendientes >= 3) {
            throw new \Exception('El estudiante ya tiene 3 solicitudes pendientes. Máximo permitido alcanzado.');
        }

        $id = $this->constanciaRepo->crear([
            'estudiante_id'   => $estudiante_id,
            'usuario_id'      => $usuario_id_sesion,
            'tipo_constancia' => $datos['tipo_constancia'],
            'motivo'          => $datos['motivo'] ?? null,
            'estado'          => 'pendiente'
        ]);

        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'solicitar',
            'solicitudes_constancia',
            $id,
            "Solicitud de constancia tipo {$datos['tipo_constancia']}"
        );

        return $id;
    }

    /**
     * Aprueba una constancia (adaptado a AdminController que llama con 2 argumentos: id, usuario_id)
     */
    public function aprobar(int $id, int $aprobado_por): bool
    {
        $resultado = $this->constanciaRepo->aprobar($id, $aprobado_por);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $aprobado_por,
                'aprobar',
                'solicitudes_constancia',
                $id,
                "Constancia aprobada"
            );
        }

        return $resultado;
    }

    /**
     * Rechaza una constancia (adaptado a AdminController que llama con 3 argumentos: id, motivo, usuario_id)
     */
    public function rechazar(int $id, string $motivo_rechazo, int $rechazado_por): bool
    {
        if (empty($motivo_rechazo)) {
            throw new \Exception('Debe especificar un motivo para el rechazo');
        }

        $resultado = $this->constanciaRepo->rechazar($id, $motivo_rechazo);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $rechazado_por,
                'rechazar',
                'solicitudes_constancia',
                $id,
                "Constancia rechazada: $motivo_rechazo"
            );
        }

        return $resultado;
    }
}