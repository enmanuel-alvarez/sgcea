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

    public function __construct()
    {
        // Instanciamos las dependencias directamente (sin contenedor DI)
        $this->estudianteRepo = new EstudianteRepository();
        $this->usuarioRepo = new UsuarioRepository();
        $this->inscripcionRepo = new InscripcionRepository();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Obtiene todos los estudiantes con detalles de sección/grado
     */
    public function obtenerTodosConDetalles(): array
    {
        return $this->estudianteRepo->obtenerTodosConDetalles();
    }

    /**
     * Obtiene un estudiante por su ID
     */
    public function obtenerPorId(int $id): ?array
    {
        return $this->estudianteRepo->obtenerPorId($id);
    }

    /**
     * Obtiene la inscripción actual (activa) de un estudiante
     */
    public function obtenerInscripcionActual(int $idEstudiante): ?array
    {
        return $this->inscripcionRepo->obtenerInscripcionActivaPorEstudiante($idEstudiante);
    }

    /**
     * Inscribe un nuevo estudiante: crea usuario, estudiante e inscripción
     */
    public function inscribir(array $datosUsuario, array $datosEstudiante, array $datosInscripcion): array
    {
        // 1. Crear usuario
        $passwordHash = password_hash($datosUsuario['password'], PASSWORD_DEFAULT);
        $usuarioData = [
            'correo' => $datosUsuario['correo'],
            'cedula' => $datosUsuario['cedula'],
            'contrasena_hash' => $passwordHash,
            'estado_cuenta' => $datosUsuario['activo'] ?? 1,
            'primer_login' => 1
        ];
        $idUsuario = $this->usuarioRepo->crear($usuarioData);

        // 2. Crear estudiante vinculado
        $estudianteData = [
            'id_usuario' => $idUsuario,
            'nombres' => $datosUsuario['nombre'],
            'apellidos' => $datosUsuario['apellido'],
            'cedula_identidad' => $datosUsuario['cedula'],
            'fecha_nacimiento' => $datosEstudiante['fecha_nacimiento'],
            'genero' => $datosEstudiante['genero'],
            'direccion' => $datosEstudiante['direccion'] ?? null,
            'telefono_padre' => $datosEstudiante['telefono'] ?? null,
            'nombre_representante' => $datosEstudiante['representante'] ?? null,
            'telefono_representante' => $datosEstudiante['telefono_representante'] ?? null
        ];
        $idEstudiante = $this->estudianteRepo->crear($estudianteData);

        // 3. Crear inscripción
        $inscripcionData = [
            'id_estudiante' => $idEstudiante,
            'id_seccion' => $datosInscripcion['id_seccion'],
            'ano_academico' => $datosInscripcion['ano_academico'],
            'fecha_inscripcion' => date('Y-m-d'),
            'estado' => 'activo'
        ];
        $idInscripcion = $this->inscripcionRepo->crear($inscripcionData);

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'CREATE',
            'estudiantes',
            $idEstudiante,
            "Estudiante inscrito: {$datosUsuario['cedula']}"
        );

        return [
            'id_estudiante' => $idEstudiante,
            'id_usuario' => $idUsuario,
            'id_inscripcion' => $idInscripcion
        ];
    }

    /**
     * Actualiza datos de un estudiante: usuario, perfil e inscripción
     */
    public function actualizar(int $id, array $datosUsuario, array $datosEstudiante, array $datosInscripcion): bool
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($id);
        if (!$estudiante) {
            throw new \Exception('Estudiante no encontrado');
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
        $this->usuarioRepo->actualizar($estudiante['id_usuario'], $usuarioData);

        // Actualizar perfil estudiante
        $estudianteData = [
            'nombres' => $datosUsuario['nombre'],
            'apellidos' => $datosUsuario['apellido'],
            'cedula_identidad' => $datosUsuario['cedula'],
            'fecha_nacimiento' => $datosEstudiante['fecha_nacimiento'],
            'genero' => $datosEstudiante['genero'],
            'direccion' => $datosEstudiante['direccion'] ?? null,
            'telefono_padre' => $datosEstudiante['telefono'] ?? null,
            'nombre_representante' => $datosEstudiante['representante'] ?? null,
            'telefono_representante' => $datosEstudiante['telefono_representante'] ?? null
        ];
        $this->estudianteRepo->actualizar($id, $estudianteData);

        // Actualizar inscripción (si se proporciona sección)
        if (!empty($datosInscripcion['id_seccion'])) {
            $inscripcion = $this->inscripcionRepo->obtenerInscripcionActivaPorEstudiante($id);
            if ($inscripcion) {
                $this->inscripcionRepo->actualizar($inscripcion['id_inscripcion'], [
                    'id_seccion' => $datosInscripcion['id_seccion'],
                    'ano_academico' => $datosInscripcion['ano_academico']
                ]);
            } else {
                // Si no tiene inscripción activa, crear una nueva
                $this->inscripcionRepo->crear([
                    'id_estudiante' => $id,
                    'id_seccion' => $datosInscripcion['id_seccion'],
                    'ano_academico' => $datosInscripcion['ano_academico'],
                    'fecha_inscripcion' => date('Y-m-d'),
                    'estado' => 'activo'
                ]);
            }
        }

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'UPDATE',
            'estudiantes',
            $id,
            "Estudiante actualizado: {$datosUsuario['cedula']}"
        );

        return true;
    }

    /**
     * Elimina un estudiante (soft delete o desactivación)
     */
    public function eliminar(int $id): bool
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($id);
        if (!$estudiante) {
            throw new \Exception('Estudiante no encontrado');
        }

        // Desactivar usuario
        $this->usuarioRepo->actualizar($estudiante['id_usuario'], ['estado_cuenta' => 0]);
        // También podrías hacer soft delete en estudiante, pero aquí solo desactivamos
        $this->estudianteRepo->eliminar($id); // Asume que el repo tiene método eliminar

        // Auditoría
        $this->auditoriaService->registrar(
            $_SESSION['usuario_id'] ?? 0,
            'DELETE',
            'estudiantes',
            $id,
            'Estudiante eliminado'
        );

        return true;
    }

    /**
     * Obtiene estudiantes por sección
     */
    public function obtenerPorSeccion(int $seccion_id): array
    {
        return $this->estudianteRepo->obtenerEstudiantesPorSeccion($seccion_id);
    }

    /**
     * Obtiene el usuario asociado a un estudiante
     */
    public function obtenerUsuarioPorEstudiante(int $estudiante_id): ?array
    {
        $estudiante = $this->estudianteRepo->obtenerPorId($estudiante_id);
        if ($estudiante && !empty($estudiante['id_usuario'])) {
            return (new UsuarioService())->obtenerPorId((int)$estudiante['id_usuario']);
        }
        return null;
    }
}
