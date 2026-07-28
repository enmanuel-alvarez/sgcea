<?php

namespace Src\Models\Services;

/**
 * Servicio de Usuarios
 * Encapsula la lógica de negocio relacionada con usuarios
 */
class UsuarioService
{
    private Repositories\UsuarioRepository $usuarioRepo;
    private Repositories\PermisoRepository $permisoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->usuarioRepo = new Repositories\UsuarioRepository();
        $this->permisoRepo = new Repositories\PermisoRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Obtener todos los usuarios activos
     */
    public function obtenerTodos(): array
    {
        return $this->usuarioRepo->obtenerTodos();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        return $this->usuarioRepo->obtenerPorId($id);
    }

    /**
     * Crear nuevo usuario con validaciones
     */
    public function crear(array $datos): array
    {
        // Validar unicidad de email
        if ($this->usuarioRepo->obtenerPorEmail($datos['email'])) {
            return ['success' => false, 'error' => 'El correo electrónico ya está registrado'];
        }

        // Validar unicidad de cédula
        if ($this->usuarioRepo->obtenerPorCedula($datos['cedula'])) {
            return ['success' => false, 'error' => 'La cédula ya está registrada'];
        }

        // Validar que el email sea válido
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'El correo electrónico no es válido'];
        }

        // Hashear contraseña
        $hashedPassword = password_hash($datos['password'], PASSWORD_DEFAULT);

        // Crear usuario
        $datos['password'] = $hashedPassword;
        $usuarioId = $this->usuarioRepo->crear($datos);

        if ($usuarioId) {
            // Asignar permisos si se proporcionan
            if (isset($datos['permisos']) && is_array($datos['permisos'])) {
                foreach ($datos['permisos'] as $permisoId) {
                    $this->permisoRepo->asignarPermiso($usuarioId, (int)$permisoId, $_SESSION['usuario_id'] ?? null);
                }
            }

            // Registrar en auditoría
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? null,
                'crear_usuario',
                'usuarios',
                $usuarioId,
                ['email' => $datos['email'], 'tipo' => $datos['tipo']]
            );

            return ['success' => true, 'usuario_id' => $usuarioId];
        }

        return ['success' => false, 'error' => 'Error al crear el usuario'];
    }

    /**
     * Actualizar usuario existente
     */
    public function actualizar(int $id, array $datos): array
    {
        $usuario = $this->usuarioRepo->obtenerPorId($id);
        
        if (!$usuario) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        // Validar unicidad de email (si se cambió)
        if (isset($datos['email']) && $datos['email'] !== $usuario['email']) {
            if ($this->usuarioRepo->obtenerPorEmail($datos['email'])) {
                return ['success' => false, 'error' => 'El correo electrónico ya está registrado'];
            }
        }

        // Validar unicidad de cédula (si se cambió)
        if (isset($datos['cedula']) && $datos['cedula'] !== $usuario['cedula']) {
            if ($this->usuarioRepo->obtenerPorCedula($datos['cedula'])) {
                return ['success' => false, 'error' => 'La cédula ya está registrada'];
            }
        }

        // Hashear nueva contraseña si se proporciona
        if (!empty($datos['password'])) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        } else {
            unset($datos['password']);
        }

        // Actualizar usuario
        $actualizado = $this->usuarioRepo->actualizar($id, $datos);

        if ($actualizado) {
            // Actualizar permisos si se proporcionan
            if (isset($datos['permisos'])) {
                $this->permisoRepo->quitarTodosLosPermisos($id);
                foreach ($datos['permisos'] as $permisoId) {
                    $this->permisoRepo->asignarPermiso($id, (int)$permisoId, $_SESSION['usuario_id'] ?? null);
                }
            }

            // Registrar en auditoría
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? null,
                'editar_usuario',
                'usuarios',
                $id,
                ['email' => $usuario['email']]
            );

            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Error al actualizar el usuario'];
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function eliminar(int $id): array
    {
        $usuario = $this->usuarioRepo->obtenerPorId($id);
        
        if (!$usuario) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        // No permitir eliminarse a sí mismo
        if ($id === ($_SESSION['usuario_id'] ?? 0)) {
            return ['success' => false, 'error' => 'No puede eliminar su propio usuario'];
        }

        $eliminado = $this->usuarioRepo->eliminar($id);

        if ($eliminado) {
            // Registrar en auditoría
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'] ?? null,
                'eliminar_usuario',
                'usuarios',
                $id,
                ['email' => $usuario['email']]
            );

            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Error al eliminar el usuario'];
    }

    /**
     * Buscar usuarios por término
     */
    public function buscar(string $termino): array
    {
        return $this->usuarioRepo->buscar($termino);
    }

    /**
     * Contar usuarios por tipo
     */
    public function contarPorTipo(string $tipo): int
    {
        return $this->usuarioRepo->contarPorTipo($tipo);
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'total' => $this->usuarioRepo->contarTotal(),
            'admin' => $this->usuarioRepo->contarPorTipo('admin'),
            'docente' => $this->usuarioRepo->contarPorTipo('docente'),
            'estudiante' => $this->usuarioRepo->contarPorTipo('estudiante')
        ];
    }
}
