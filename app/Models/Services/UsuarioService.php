<?php

namespace Src\Models\Services;

use Src\Models\Repositories\UsuarioRepository;
use Src\Models\Repositories\PermisoRepository;

/**
 * Servicio de Usuarios
 * Encapsula la lógica de negocio relacionada con usuarios
 */
class UsuarioService
{
    private UsuarioRepository $usuarioRepo;
    private PermisoRepository $permisoRepo;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->usuarioRepo = new UsuarioRepository();
        $this->permisoRepo = new PermisoRepository();
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
     * Obtener todos los usuarios con detalles (alias de obtenerTodos)
     */
    public function obtenerTodosConDetalles(): array
    {
        return $this->obtenerTodos();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId(int $id): ?array
    {
        return $this->usuarioRepo->obtenerPorId($id);
    }

    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail(string $email): ?array
    {
        return $this->usuarioRepo->obtenerPorEmail($email);
    }

    /**
     * Obtener usuario por cédula
     */
    public function obtenerPorCedula(string $cedula): ?array
    {
        return $this->usuarioRepo->obtenerPorCedula($cedula);
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
            if (isset($datos['permisos']) && is_array($datos['permisos']) && !empty($datos['permisos'])) {
                foreach ($datos['permisos'] as $permisoId) {
                    $this->permisoRepo->asignarPermiso($usuarioId, (int)$permisoId, $_SESSION['usuario_id'] ?? null);
                }
            } else {
                // Asignar permisos predeterminados según el tipo de usuario (RN-001)
                $this->asignarPermisosPredeterminados($usuarioId, $datos['tipo'] ?? 'estudiante');
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

    /**
     * Asignar permisos predeterminados según el tipo de usuario (RN-001)
     * Garantiza que todo usuario tenga al menos los permisos básicos de su rol
     */
    private function asignarPermisosPredeterminados(int $usuarioId, string $tipo): void
    {
        $permisosPorTipo = [
            'admin' => [
                'admin.dashboard', 'admin.usuarios.ver', 'admin.usuarios.crear',
                'admin.usuarios.editar', 'admin.usuarios.eliminar',
                'admin.estudiantes.ver', 'admin.estudiantes.crear',
                'admin.estudiantes.editar', 'admin.estudiantes.eliminar',
                'admin.estudiantes.inscribir',
                'admin.docentes.ver', 'admin.docentes.crear',
                'admin.docentes.editar', 'admin.docentes.eliminar',
                'admin.materias.ver', 'admin.materias.crear',
                'admin.materias.editar', 'admin.materias.eliminar',
                'admin.secciones.ver', 'admin.secciones.crear',
                'admin.secciones.editar', 'admin.secciones.eliminar',
                'admin.asignaciones.ver', 'admin.asignaciones.crear',
                'admin.asignaciones.eliminar',
                'admin.constancias.ver', 'admin.constancias.aprobar',
                'admin.permisos.asignar',
                'admin.configuracion.ver', 'admin.configuracion.editar',
                'reportes.ver'
            ],
            'docente' => [
                'docente.dashboard',
                'docente.calificaciones.ver', 'docente.calificaciones.registrar',
                'docente.asistencia.ver', 'docente.asistencia.registrar',
                'docente.planevaluacion.gestionar'
            ],
            'estudiante' => [
                'estudiante.dashboard',
                'estudiante.boletin.ver',
                'estudiante.asistencia.ver',
                'estudiante.constancias.solicitar', 'estudiante.constancias.ver',
                'estudiante.perfil.ver', 'estudiante.perfil.editar'
            ]
        ];

        $permisosNombres = $permisosPorTipo[$tipo] ?? [];

        foreach ($permisosNombres as $nombrePermiso) {
            $permiso = $this->permisoRepo->obtenerPorNombre($nombrePermiso);
            if ($permiso) {
                $this->permisoRepo->asignarPermiso($usuarioId, (int)$permiso['id'], $_SESSION['usuario_id'] ?? null);
            }
        }
    }
}