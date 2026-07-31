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

    public function __construct()
    {
        $this->docenteRepo = new DocenteRepository();
        $this->usuarioRepo = new UsuarioRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Obtiene todos los docentes con detalles (correo, etc.)
     */
    public function obtenerTodosConDetalles(): array
    {
        return $this->docenteRepo->obtenerTodos();
    }

    /**
     * Obtiene todos los docentes (alias simple)
     */
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

    /**
     * Crea un docente a partir de datos de usuario + datos de docente
     * Ajustado al llamado del AdminController: crear($datos_usuario, $datos_docente)
     */
    public function crear(array $datosUsuario, array $datosDocente): array
    {
        // Crear usuario
        $passwordHash = password_hash($datosUsuario['password'], PASSWORD_DEFAULT);
        $usuarioData = [
            'correo' => $datosUsuario['correo'],
            'cedula' => $datosUsuario['cedula'],
            'contrasena_hash' => $passwordHash,
            'estado_cuenta' => $datosUsuario['activo'] ?? 1,
            'primer_login' => 1
        ];
        $idUsuario = $this->usuarioRepo->crear($usuarioData);

        // Crear docente vinculado
        $docenteData = [
            'id_usuario' => $idUsuario,
            'nombres' => $datosUsuario['nombre'],
            'apellidos' => $datosUsuario['apellido'],
            'cedula_identidad' => $datosUsuario['cedula'],
            'especialidad' => $datosDocente['especialidad'] ?? null,
            'telefono' => $datosDocente['telefono'] ?? null,
            // 'titulo' => $datosDocente['titulo'] ?? null, // si la tabla docentes no tiene 'titulo', ajustar según esquema real
            'fecha_contratacion' => $datosDocente['fecha_ingreso'] ?? date('Y-m-d')
        ];
        $idDocente = $this->docenteRepo->crear($docenteData);

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'docentes',
            $idDocente,
            "Docente registrado: {$datosUsuario['cedula']}"
        );

        return ['id' => $idDocente, 'usuario_id' => $idUsuario];
    }

    /**
     * Actualiza datos de un docente (usuario + perfil)
     * Ajustado al llamado: actualizar($id, $datos_usuario, $datos_docente)
     */
    public function actualizar(int $id, array $datosUsuario, array $datosDocente): bool
    {
        $docente = $this->docenteRepo->obtenerPorId($id);
        if (!$docente) {
            throw new \Exception('Docente no encontrado');
        }

        // Actualizar usuario
        $usuarioData = [
            'correo' => $datosUsuario['correo'],
            'cedula' => $datosUsuario['cedula'],
            'estado_cuenta' => $datosUsuario['activo'] ?? 1
        ];
        if (!empty($datosUsuario['password'])) {
            $usuarioData['contrasena_hash'] = password_hash($datosUsuario['password'], PASSWORD_DEFAULT);
        }
        $this->usuarioRepo->actualizar($docente['usuario_id'], $usuarioData);

        // Actualizar perfil docente
        $docenteData = [
            'nombres' => $datosUsuario['nombre'],
            'apellidos' => $datosUsuario['apellido'],
            'cedula_identidad' => $datosUsuario['cedula'],
            'especialidad' => $datosDocente['especialidad'] ?? null,
            'telefono' => $datosDocente['telefono'] ?? null,
            // 'titulo' => $datosDocente['titulo'] ?? null,
            'fecha_contratacion' => $datosDocente['fecha_ingreso'] ?? date('Y-m-d')
        ];
        $this->docenteRepo->actualizar($id, $docenteData);

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'UPDATE',
            'docentes',
            $id,
            "Docente actualizado: {$datosUsuario['cedula']}"
        );

        return true;
    }

    public function eliminar(int $id): bool
    {
        $docente = $this->docenteRepo->obtenerPorId($id);
        if (!$docente) {
            throw new \Exception('Docente no encontrado');
        }

        // Desactivar usuario asociado
        $this->usuarioRepo->actualizar($docente['usuario_id'], ['estado_cuenta' => 0]);
        // Soft delete al docente
        $this->docenteRepo->eliminar($id);

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'DELETE',
            'docentes',
            $id,
            'Docente eliminado'
        );

        return true;
    }
}