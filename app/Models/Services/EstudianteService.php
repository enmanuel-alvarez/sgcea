<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\EstudianteRepository;
use Src\Models\Repositories\UsuarioRepository;
use Src\Models\Repositories\InscripcionRepository;
use Src\Models\Services\AuditoriaService;

class EstudianteService
{
    private EstudianteRepository $estudianteRepo;
    private UsuarioRepository $usuarioRepo;
    private InscripcionRepository $inscripcionRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        EstudianteRepository $estudianteRepo,
        UsuarioRepository $usuarioRepo,
        InscripcionRepository $inscripcionRepo,
        AuditoriaService $auditoriaService
    ) {
        $this->estudianteRepo = $estudianteRepo;
        $this->usuarioRepo = $usuarioRepo;
        $this->inscripcionRepo = $inscripcionRepo;
        $this->auditoriaService = $auditoriaService;
    }

    public function obtenerTodos(): array
    {
        return $this->estudianteRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->estudianteRepo->obtenerPorId($id);
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        return $this->estudianteRepo->obtenerPorUsuario($usuario_id);
    }

    public function crear(array $datos, int $usuario_id_sesion): array
    {
        if (!$this->estudianteRepo->verificarCodigoUnico($datos['codigo'])) {
            throw new \Exception('El código de estudiante ya está registrado');
        }

        $usuario_data = [
            'correo' => $datos['correo'] ?? $datos['codigo'] . '@estudiante.sgcea.com',
            'password' => password_hash($datos['codigo'], PASSWORD_DEFAULT),
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'tipo' => 'estudiante',
            'estado' => 'activo'
        ];

        $usuario_id = $this->usuarioRepo->crear($usuario_data);

        $estudiante_data = [
            'usuario_id' => $usuario_id,
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'fecha_nacimiento' => $datos['fecha_nacimiento'],
            'genero' => $datos['genero'],
            'direccion' => $datos['direccion'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'representante_nombre' => $datos['representante_nombre'] ?? null,
            'representante_telefono' => $datos['representante_telefono'] ?? null,
            'grado_id' => $datos['grado_id'],
            'seccion_id' => $datos['seccion_id'],
            'estado' => 'activo'
        ];

        $estudiante_id = $this->estudianteRepo->crear($estudiante_data);

        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'estudiantes',
            $estudiante_id,
            "Estudiante creado: {$datos['nombre']} {$datos['apellido']}"
        );

        return ['id' => $estudiante_id, 'usuario_id' => $usuario_id];
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($id);
        if (!$estudiante) {
            throw new \Exception('Estudiante no encontrado');
        }

        if (!$this->estudianteRepo->verificarCodigoUnico($datos['codigo'], $id)) {
            throw new \Exception('El código de estudiante ya está registrado');
        }

        $resultado = $this->estudianteRepo->actualizar($id, $datos);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'estudiantes',
                $id,
                "Estudiante actualizado: {$datos['nombre']} {$datos['apellido']}"
            );
        }

        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($id);
        if (!$estudiante) {
            throw new \Exception('Estudiante no encontrado');
        }

        $resultado = $this->estudianteRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'estudiantes',
                $id,
                "Estudiante eliminado: {$estudiante['nombre']} {$estudiante['apellido']}"
            );
        }

        return $resultado;
    }

    public function inscribir(int $estudiante_id, string $ano_lectivo, int $usuario_id_sesion): int
    {
        if (!$this->inscripcionRepo->verificarInscripcionActiva($estudiante_id, $ano_lectivo)) {
            throw new \Exception('El estudiante ya tiene una inscripción activa para este año lectivo');
        }

        $inscripcion_id = $this->inscripcionRepo->crear([
            'estudiante_id' => $estudiante_id,
            'ano_lectivo' => $ano_lectivo,
            'estado' => 'activa'
        ]);

        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'inscribir',
            'inscripciones',
            $inscripcion_id,
            "Estudiante inscrito para año lectivo $ano_lectivo"
        );

        return $inscripcion_id;
    }

    public function obtenerUsuarioPorEstudiante(int $estudiante_id): ?array
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($estudiante_id);
        if ($estudiante && !empty($estudiante['usuario_id'])) {
            return (new \Src\Models\Services\UsuarioService())->obtenerPorId((int)$estudiante['usuario_id']);
        }
        return null;
    }

    public function obtenerPorSeccion(int $seccion_id): array
    {
        return $this->estudianteRepo->obtenerEstudiantesPorSeccion($seccion_id);
    }

}
