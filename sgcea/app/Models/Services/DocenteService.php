<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\DocenteRepository;
use Src\Models\Repositories\UsuarioRepository;
use Src\Models\Services\AuditoriaService;

class DocenteService
{
    private DocenteRepository $docenteRepo;
    private UsuarioRepository $usuarioRepo;
    private AuditoriaService $auditoriaService;

    public function __construct(
        DocenteRepository $docenteRepo,
        UsuarioRepository $usuarioRepo,
        AuditoriaService $auditoriaService
    ) {
        $this->docenteRepo = $docenteRepo;
        $this->usuarioRepo = $usuarioRepo;
        $this->auditoriaService = $auditoriaService;
    }

    public function obtenerTodos(): array
    {
        return $this->docenteRepo->obtenerTodos();
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->docenteRepo->obtenerPorId($id);
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        return $this->docenteRepo->obtenerPorUsuario($usuario_id);
    }

    public function crear(array $datos, int $usuario_id_sesion): array
    {
        if (!$this->docenteRepo->verificarCodigoUnico($datos['codigo'])) {
            throw new \Exception('El código de docente ya está registrado');
        }

        $usuario_data = [
            'correo' => $datos['correo'] ?? $datos['codigo'] . '@docente.sgcea.com',
            'password' => password_hash($datos['codigo'], PASSWORD_DEFAULT),
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'tipo' => 'docente',
            'estado' => 'activo'
        ];

        $usuario_id = $this->usuarioRepo->crear($usuario_data);

        $docente_data = [
            'usuario_id' => $usuario_id,
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'especialidad' => $datos['especialidad'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'titulo' => $datos['titulo'] ?? null,
            'estado' => 'activo'
        ];

        $docente_id = $this->docenteRepo->crear($docente_data);

        $this->auditoriaService->registrar(
            $usuario_id_sesion,
            'crear',
            'docentes',
            $docente_id,
            "Docente creado: {$datos['nombre']} {$datos['apellido']}"
        );

        return ['id' => $docente_id, 'usuario_id' => $usuario_id];
    }

    public function actualizar(int $id, array $datos, int $usuario_id_sesion): bool
    {
        $docente = $this->docenteRepo->obtenerPorId($id);
        if (!$docente) {
            throw new \Exception('Docente no encontrado');
        }

        if (!$this->docenteRepo->verificarCodigoUnico($datos['codigo'], $id)) {
            throw new \Exception('El código de docente ya está registrado');
        }

        $resultado = $this->docenteRepo->actualizar($id, $datos);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'actualizar',
                'docentes',
                $id,
                "Docente actualizado: {$datos['nombre']} {$datos['apellido']}"
            );
        }

        return $resultado;
    }

    public function eliminar(int $id, int $usuario_id_sesion): bool
    {
        $docente = $this->docenteRepo->obtenerPorId($id);
        if (!$docente) {
            throw new \Exception('Docente no encontrado');
        }

        $resultado = $this->docenteRepo->eliminar($id);

        if ($resultado) {
            $this->auditoriaService->registrar(
                $usuario_id_sesion,
                'eliminar',
                'docentes',
                $id,
                "Docente eliminado: {$docente['nombre']} {$docente['apellido']}"
            );
        }

        return $resultado;
    }
}
