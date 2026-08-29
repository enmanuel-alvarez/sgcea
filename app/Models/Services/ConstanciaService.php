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

    public function obtenerTodas(?string $estado = null): array
    {
        if ($estado && $estado !== 'todas') {
            $todas = $this->constanciaRepo->obtenerTodos();
            return array_values(array_filter($todas, fn($item) => ($item['estado'] ?? '') === $estado));
        }
        return $this->constanciaRepo->obtenerTodos();
    }

    public function obtenerTodos(): array
    {
        return $this->constanciaRepo->obtenerTodos();
    }

    public function obtenerInscripcionPorEstudiante(int $estudianteId): ?array
    {
        $inscripcionRepo = new \Src\Models\Repositories\InscripcionRepository();
        $inscripcion = $inscripcionRepo->obtenerInscripcionActivaPorEstudiante($estudianteId);
        if (!$inscripcion) {
            return null;
        }

        $seccionRepo = new \Src\Models\Repositories\SeccionRepository();
        $seccion = $seccionRepo->obtenerPorId((int)$inscripcion['seccion_id']);
        if ($seccion) {
            $inscripcion['seccion_nombre'] = $seccion['nombre'] ?? '';
            $gradoRepo = new \Src\Models\Repositories\GradoRepository();
            $grado = $gradoRepo->obtenerPorId((int)$seccion['grado_id']);
            $inscripcion['grado_nombre'] = $grado['nombre'] ?? '';
        }

        return $inscripcion;
    }

    public function obtenerConfiguraciones(): array
    {
        $configRepo = new \Src\Models\Repositories\ConfiguracionRepository();
        $configs = $configRepo->obtenerTodos();
        $resultado = [];
        foreach ($configs as $c) {
            if (isset($c['clave'])) {
                $resultado[$c['clave']] = $c['valor'] ?? '';
            }
        }
        return $resultado;
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

    public function contarPendientesPorEstudiante(int $estudiante_id): int
    {
        return $this->constanciaRepo->contarPendientesPorEstudiante($estudiante_id);
    }

    public function solicitar($datos, $tipoOrSesion = 0, ?string $motivo = null): int
    {
        if (is_array($datos)) {
            $estudiante_id = (int)($datos['estudiante_id'] ?? 0);
            $tipo = (string)($datos['tipo_constancia'] ?? $datos['tipo'] ?? '');
            $motivoTxt = (string)($datos['motivo'] ?? '');
            $usuario_id_sesion = is_numeric($tipoOrSesion) ? (int)$tipoOrSesion : ($_SESSION['usuario_id'] ?? 0);
        } else {
            $estudiante_id = (int)$datos;
            $tipo = (string)$tipoOrSesion;
            $motivoTxt = (string)($motivo ?? '');
            $usuario_id_sesion = (int)($_SESSION['usuario_id'] ?? 0);
        }

        $pendientes = $this->constanciaRepo->contarPendientesPorEstudiante($estudiante_id);
        if ($pendientes >= 3) {
            throw new \Exception('El estudiante ya tiene 3 solicitudes pendientes. Máximo permitido alcanzado.');
        }

        $id = $this->constanciaRepo->crear([
            'estudiante_id'   => $estudiante_id,
            'usuario_id'      => $usuario_id_sesion,
            'tipo_constancia' => $tipo,
            'tipo'            => $tipo,
            'motivo'          => $motivoTxt,
            'estado'          => 'pendiente'
        ]);

        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'SOLICITAR_CONSTANCIA',
            'solicitudes_constancia',
            $id,
            "Solicitud de constancia tipo {$tipo}"
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