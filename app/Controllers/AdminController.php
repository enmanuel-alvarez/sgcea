<?php

namespace Src\Controllers;

use Src\Core\Controller;
use Src\Models\Services\UsuarioService;
use Src\Models\Services\EstudianteService;
use Src\Models\Services\DocenteService;
use Src\Models\Services\GradoService;
use Src\Models\Services\SeccionService;
use Src\Models\Services\MateriaService;
use Src\Models\Services\AsignacionService;
use Src\Models\Services\ConstanciaService;
use Src\Models\Services\PermisoService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;

class AdminController extends Controller
{
    private UsuarioService $usuarioService;
    private EstudianteService $estudianteService;
    private DocenteService $docenteService;
    private GradoService $gradoService;
    private SeccionService $seccionService;
    private MateriaService $materiaService;
    private AsignacionService $asignacionService;
    private ConstanciaService $constanciaService;
    private PermisoService $permisoService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->estudianteService = new EstudianteService();
        $this->docenteService = new DocenteService();
        $this->gradoService = new GradoService();
        $this->seccionService = new SeccionService();
        $this->materiaService = new MateriaService();
        $this->asignacionService = new AsignacionService();
        $this->constanciaService = new ConstanciaService();
        $this->permisoService = new PermisoService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Dashboard del administrador
     */
    public function index(): void
    {
        $dashboardService = new \Src\Models\Services\DashboardService();
        $datos = $dashboardService->obtenerDatosAdmin();
        
        $this->render('admin/dashboard', [
            'titulo' => 'Dashboard Administrativo',
            'datos' => $datos
        ]);
    }

    // ==================== USUARIOS ====================

    public function listarUsuarios(): void
    {
        $usuarios = $this->usuarioService->obtenerTodosConDetalles();
        $this->render('admin/usuarios/index', [
            'titulo' => 'Gestión de Usuarios',
            'usuarios' => $usuarios
        ]);
    }

    public function mostrarCrearUsuario(): void
    {
        $permisos = $this->permisoService->obtenerTodos();
        $this->render('admin/usuarios/crear', [
            'titulo' => 'Crear Usuario',
            'permisos' => $permisos
        ]);
    }

    public function guardarUsuario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/usuarios');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'tipo_usuario' => Security::sanitizar($_POST['tipo_usuario'] ?? 'estudiante'),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        $permisos_ids = $_POST['permisos'] ?? [];

        try {
            $id_usuario = $this->usuarioService->crear($datos, $permisos_ids);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'usuarios',
                $id_usuario,
                "Usuario creado: {$datos['correo']}"
            );

            $_SESSION['flash']['success'] = 'Usuario creado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al crear usuario: ' . $e->getMessage();
        }

        $this->redirigir('/admin/usuarios');
    }

    public function mostrarEditarUsuario(int $id): void
    {
        $usuario = $this->usuarioService->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['flash']['error'] = 'Usuario no encontrado';
            $this->redirigir('/admin/usuarios');
        }

        $permisos = $this->permisoService->obtenerTodos();
        $permisos_asignados = $this->permisoService->obtenerPermisosPorUsuario($id);
        $ids_asignados = array_column($permisos_asignados, 'id');

        $this->render('admin/usuarios/editar', [
            'titulo' => 'Editar Usuario',
            'usuario' => $usuario,
            'permisos' => $permisos,
            'permisos_asignados' => $ids_asignados
        ]);
    }

    public function actualizarUsuario(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/usuarios/editar/$id");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'tipo_usuario' => Security::sanitizar($_POST['tipo_usuario'] ?? 'estudiante'),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        if (!empty($_POST['password'])) {
            $datos['password'] = $_POST['password'];
        }

        $permisos_ids = $_POST['permisos'] ?? [];

        try {
            $this->usuarioService->actualizar($id, $datos, $permisos_ids);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'usuarios',
                $id,
                "Usuario actualizado: {$datos['correo']}"
            );

            $_SESSION['flash']['success'] = 'Usuario actualizado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar usuario: ' . $e->getMessage();
        }

        $this->redirigir('/admin/usuarios');
    }

    public function eliminarUsuario(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/usuarios');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->usuarioService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'usuarios',
                $id,
                'Usuario eliminado'
            );

            $_SESSION['flash']['success'] = 'Usuario eliminado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar usuario: ' . $e->getMessage();
        }

        $this->redirigir('/admin/usuarios');
    }

    // ==================== ESTUDIANTES ====================

    public function listarEstudiantes(): void
    {
        $estudiantes = $this->estudianteService->obtenerTodosConDetalles();
        $grados = $this->gradoService->obtenerTodos();
        
        $this->render('admin/estudiantes/index', [
            'titulo' => 'Gestión de Estudiantes',
            'estudiantes' => $estudiantes,
            'grados' => $grados
        ]);
    }

    public function mostrarCrearEstudiante(): void
    {
        $grados = $this->gradoService->obtenerTodos();
        $secciones = $this->seccionService->obtenerTodos();
        
        $this->render('admin/estudiantes/crear', [
            'titulo' => 'Inscribir Estudiante',
            'grados' => $grados,
            'secciones' => $secciones
        ]);
    }

    public function guardarEstudiante(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/estudiantes');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos_usuario = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'tipo_usuario' => 'estudiante',
            'activo' => 1
        ];

        $datos_estudiante = [
            'fecha_nacimiento' => Security::sanitizar($_POST['fecha_nacimiento'] ?? ''),
            'genero' => Security::sanitizar($_POST['genero'] ?? ''),
            'direccion' => Security::sanitizar($_POST['direccion'] ?? ''),
            'telefono' => Security::sanitizar($_POST['telefono'] ?? ''),
            'representante' => Security::sanitizar($_POST['representante'] ?? ''),
            'telefono_representante' => Security::sanitizar($_POST['telefono_representante'] ?? '')
        ];

        $datos_inscripcion = [
            'id_grado' => (int)($_POST['id_grado'] ?? 0),
            'id_seccion' => (int)($_POST['id_seccion'] ?? 0),
            'ano_academico' => Security::sanitizar($_POST['ano_academico'] ?? date('Y'))
        ];

        try {
            $this->estudianteService->inscribir($datos_usuario, $datos_estudiante, $datos_inscripcion);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'estudiantes',
                null,
                "Estudiante inscrito: {$datos_usuario['cedula']}"
            );

            $_SESSION['flash']['success'] = 'Estudiante inscrito exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al inscribir estudiante: ' . $e->getMessage();
        }

        $this->redirigir('/admin/estudiantes');
    }

    public function mostrarEditarEstudiante(int $id): void
    {
        $estudiante = $this->estudianteService->obtenerPorId($id);
        if (!$estudiante) {
            $_SESSION['flash']['error'] = 'Estudiante no encontrado';
            $this->redirigir('/admin/estudiantes');
        }

        $grados = $this->gradoService->obtenerTodos();
        $secciones = $this->seccionService->obtenerTodos();
        $inscripcion = $this->estudianteService->obtenerInscripcionActual($id);

        $this->render('admin/estudiantes/editar', [
            'titulo' => 'Editar Estudiante',
            'estudiante' => $estudiante,
            'grados' => $grados,
            'secciones' => $secciones,
            'inscripcion' => $inscripcion
        ]);
    }

    public function actualizarEstudiante(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/estudiantes/editar/$id");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos_usuario = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        $datos_estudiante = [
            'fecha_nacimiento' => Security::sanitizar($_POST['fecha_nacimiento'] ?? ''),
            'genero' => Security::sanitizar($_POST['genero'] ?? ''),
            'direccion' => Security::sanitizar($_POST['direccion'] ?? ''),
            'telefono' => Security::sanitizar($_POST['telefono'] ?? ''),
            'representante' => Security::sanitizar($_POST['representante'] ?? ''),
            'telefono_representante' => Security::sanitizar($_POST['telefono_representante'] ?? '')
        ];

        $datos_inscripcion = [
            'id_grado' => (int)($_POST['id_grado'] ?? 0),
            'id_seccion' => (int)($_POST['id_seccion'] ?? 0),
            'ano_academico' => Security::sanitizar($_POST['ano_academico'] ?? date('Y'))
        ];

        try {
            $this->estudianteService->actualizar($id, $datos_usuario, $datos_estudiante, $datos_inscripcion);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'estudiantes',
                $id,
                "Estudiante actualizado: {$datos_usuario['cedula']}"
            );

            $_SESSION['flash']['success'] = 'Estudiante actualizado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar estudiante: ' . $e->getMessage();
        }

        $this->redirigir('/admin/estudiantes');
    }

    public function eliminarEstudiante(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/estudiantes');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->estudianteService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'estudiantes',
                $id,
                'Estudiante eliminado'
            );

            $_SESSION['flash']['success'] = 'Estudiante eliminado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar estudiante: ' . $e->getMessage();
        }

        $this->redirigir('/admin/estudiantes');
    }

    // ==================== DOCENTES ====================

    public function listarDocentes(): void
    {
        $docentes = $this->docenteService->obtenerTodosConDetalles();
        
        $this->render('admin/docentes/index', [
            'titulo' => 'Gestión de Docentes',
            'docentes' => $docentes
        ]);
    }

    public function mostrarCrearDocente(): void
    {
        $this->render('admin/docentes/crear', [
            'titulo' => 'Registrar Docente'
        ]);
    }

    public function guardarDocente(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/docentes');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos_usuario = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'tipo_usuario' => 'docente',
            'activo' => 1
        ];

        $datos_docente = [
            'especialidad' => Security::sanitizar($_POST['especialidad'] ?? ''),
            'telefono' => Security::sanitizar($_POST['telefono'] ?? ''),
            'direccion' => Security::sanitizar($_POST['direccion'] ?? ''),
            'titulo' => Security::sanitizar($_POST['titulo'] ?? ''),
            'fecha_ingreso' => Security::sanitizar($_POST['fecha_ingreso'] ?? date('Y-m-d'))
        ];

        try {
            $this->docenteService->crear($datos_usuario, $datos_docente);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'docentes',
                null,
                "Docente registrado: {$datos_usuario['cedula']}"
            );

            $_SESSION['flash']['success'] = 'Docente registrado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al registrar docente: ' . $e->getMessage();
        }

        $this->redirigir('/admin/docentes');
    }

    public function mostrarEditarDocente(int $id): void
    {
        $docente = $this->docenteService->obtenerPorId($id);
        if (!$docente) {
            $_SESSION['flash']['error'] = 'Docente no encontrado';
            $this->redirigir('/admin/docentes');
        }

        $this->render('admin/docentes/editar', [
            'titulo' => 'Editar Docente',
            'docente' => $docente
        ]);
    }

    public function actualizarDocente(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/docentes/editar/$id");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos_usuario = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'apellido' => Security::sanitizar($_POST['apellido'] ?? ''),
            'cedula' => Security::sanitizar($_POST['cedula'] ?? ''),
            'correo' => Security::sanitizar($_POST['correo'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        $datos_docente = [
            'especialidad' => Security::sanitizar($_POST['especialidad'] ?? ''),
            'telefono' => Security::sanitizar($_POST['telefono'] ?? ''),
            'direccion' => Security::sanitizar($_POST['direccion'] ?? ''),
            'titulo' => Security::sanitizar($_POST['titulo'] ?? ''),
            'fecha_ingreso' => Security::sanitizar($_POST['fecha_ingreso'] ?? date('Y-m-d'))
        ];

        if (!empty($_POST['password'])) {
            $datos_usuario['password'] = $_POST['password'];
        }

        try {
            $this->docenteService->actualizar($id, $datos_usuario, $datos_docente);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'docentes',
                $id,
                "Docente actualizado: {$datos_usuario['cedula']}"
            );

            $_SESSION['flash']['success'] = 'Docente actualizado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar docente: ' . $e->getMessage();
        }

        $this->redirigir('/admin/docentes');
    }

    public function eliminarDocente(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/docentes');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->docenteService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'docentes',
                $id,
                'Docente eliminado'
            );

            $_SESSION['flash']['success'] = 'Docente eliminado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar docente: ' . $e->getMessage();
        }

        $this->redirigir('/admin/docentes');
    }

    // ==================== MATERIAS ====================

    public function listarMaterias(): void
    {
        $materias = $this->materiaService->obtenerTodos();
        
        $this->render('admin/materias/index', [
            'titulo' => 'Gestión de Materias',
            'materias' => $materias
        ]);
    }

    public function mostrarCrearMateria(): void
    {
        $this->render('admin/materias/crear', [
            'titulo' => 'Crear Materia'
        ]);
    }

    public function guardarMateria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/materias');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'codigo' => Security::sanitizar($_POST['codigo'] ?? ''),
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'descripcion' => Security::sanitizar($_POST['descripcion'] ?? ''),
            'creditos' => (int)($_POST['creditos'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            $this->materiaService->crear($datos);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'materias',
                null,
                "Materia creada: {$datos['nombre']}"
            );

            $_SESSION['flash']['success'] = 'Materia creada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al crear materia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/materias');
    }

    public function mostrarEditarMateria(int $id): void
    {
        $materia = $this->materiaService->obtenerPorId($id);
        if (!$materia) {
            $_SESSION['flash']['error'] = 'Materia no encontrada';
            $this->redirigir('/admin/materias');
        }

        $this->render('admin/materias/editar', [
            'titulo' => 'Editar Materia',
            'materia' => $materia
        ]);
    }

    public function actualizarMateria(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/materias/editar/$id");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'codigo' => Security::sanitizar($_POST['codigo'] ?? ''),
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'descripcion' => Security::sanitizar($_POST['descripcion'] ?? ''),
            'creditos' => (int)($_POST['creditos'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            $this->materiaService->actualizar($id, $datos);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'materias',
                $id,
                "Materia actualizada: {$datos['nombre']}"
            );

            $_SESSION['flash']['success'] = 'Materia actualizada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar materia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/materias');
    }

    public function eliminarMateria(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/materias');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->materiaService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'materias',
                $id,
                'Materia eliminada'
            );

            $_SESSION['flash']['success'] = 'Materia eliminada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar materia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/materias');
    }

    // ==================== SECCIONES ====================

    public function listarSecciones(): void
    {
        $secciones = $this->seccionService->obtenerTodosConDetalles();
        $grados = $this->gradoService->obtenerTodos();
        
        $this->render('admin/secciones/index', [
            'titulo' => 'Gestión de Secciones',
            'secciones' => $secciones,
            'grados' => $grados
        ]);
    }

    public function mostrarCrearSeccion(): void
    {
        $grados = $this->gradoService->obtenerTodos();
        
        $this->render('admin/secciones/crear', [
            'titulo' => 'Crear Sección',
            'grados' => $grados
        ]);
    }

    public function guardarSeccion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/secciones');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'id_grado' => (int)($_POST['id_grado'] ?? 0),
            'cup_maximo' => (int)($_POST['cup_maximo'] ?? 30),
            'ano_academico' => Security::sanitizar($_POST['ano_academico'] ?? date('Y')),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            $this->seccionService->crear($datos);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'secciones',
                null,
                "Sección creada: {$datos['nombre']}"
            );

            $_SESSION['flash']['success'] = 'Sección creada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al crear sección: ' . $e->getMessage();
        }

        $this->redirigir('/admin/secciones');
    }

    public function mostrarEditarSeccion(int $id): void
    {
        $seccion = $this->seccionService->obtenerPorId($id);
        if (!$seccion) {
            $_SESSION['flash']['error'] = 'Sección no encontrada';
            $this->redirigir('/admin/secciones');
        }

        $grados = $this->gradoService->obtenerTodos();
        
        $this->render('admin/secciones/editar', [
            'titulo' => 'Editar Sección',
            'seccion' => $seccion,
            'grados' => $grados
        ]);
    }

    public function actualizarSeccion(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/secciones/editar/$id");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'nombre' => Security::sanitizar($_POST['nombre'] ?? ''),
            'id_grado' => (int)($_POST['id_grado'] ?? 0),
            'cup_maximo' => (int)($_POST['cup_maximo'] ?? 30),
            'ano_academico' => Security::sanitizar($_POST['ano_academico'] ?? date('Y')),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            $this->seccionService->actualizar($id, $datos);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'secciones',
                $id,
                "Sección actualizada: {$datos['nombre']}"
            );

            $_SESSION['flash']['success'] = 'Sección actualizada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al actualizar sección: ' . $e->getMessage();
        }

        $this->redirigir('/admin/secciones');
    }

    public function eliminarSeccion(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/secciones');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->seccionService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'secciones',
                $id,
                'Sección eliminada'
            );

            $_SESSION['flash']['success'] = 'Sección eliminada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar sección: ' . $e->getMessage();
        }

        $this->redirigir('/admin/secciones');
    }

    // ==================== ASIGNACIONES ====================

    public function listarAsignaciones(): void
    {
        $asignaciones = $this->asignacionService->obtenerTodosConDetalles();
        $docentes = $this->docenteService->obtenerTodos();
        $materias = $this->materiaService->obtenerTodos();
        $secciones = $this->seccionService->obtenerTodos();
        
        $this->render('admin/asignaciones/index', [
            'titulo' => 'Gestión de Asignaciones',
            'asignaciones' => $asignaciones,
            'docentes' => $docentes,
            'materias' => $materias,
            'secciones' => $secciones
        ]);
    }

    public function mostrarCrearAsignacion(): void
    {
        $docentes = $this->docenteService->obtenerTodos();
        $materias = $this->materiaService->obtenerTodos();
        $secciones = $this->seccionService->obtenerTodos();
        
        $this->render('admin/asignaciones/crear', [
            'titulo' => 'Crear Asignación',
            'docentes' => $docentes,
            'materias' => $materias,
            'secciones' => $secciones
        ]);
    }

    public function guardarAsignacion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/asignaciones');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $datos = [
            'id_profesor' => (int)($_POST['id_profesor'] ?? 0),
            'id_materia' => (int)($_POST['id_materia'] ?? 0),
            'id_seccion' => (int)($_POST['id_seccion'] ?? 0),
            'ano_academico' => Security::sanitizar($_POST['ano_academico'] ?? date('Y')),
            'periodo' => Security::sanitizar($_POST['periodo'] ?? '1'),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        try {
            $this->asignacionService->crear($datos);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'CREATE',
                'asignaciones',
                null,
                "Asignación creada: Profesor ID {$datos['id_profesor']}"
            );

            $_SESSION['flash']['success'] = 'Asignación creada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al crear asignación: ' . $e->getMessage();
        }

        $this->redirigir('/admin/asignaciones');
    }

    public function eliminarAsignacion(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/asignaciones');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->asignacionService->eliminar($id);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'DELETE',
                'asignaciones',
                $id,
                'Asignación eliminada'
            );

            $_SESSION['flash']['success'] = 'Asignación eliminada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al eliminar asignación: ' . $e->getMessage();
        }

        $this->redirigir('/admin/asignaciones');
    }

    // ==================== CONSTANCIAS ====================

    public function gestionarConstancias(): void
    {
        $solicitudes = $this->constanciaService->obtenerTodasConDetalles();
        
        $this->render('admin/constancias/index', [
            'titulo' => 'Gestión de Constancias',
            'solicitudes' => $solicitudes
        ]);
    }

    public function aprobarConstancia(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/constancias');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        try {
            $this->constanciaService->aprobar($id, $_SESSION['usuario_id']);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'solicitudes_constancia',
                $id,
                'Constancia aprobada'
            );

            $_SESSION['flash']['success'] = 'Constancia aprobada exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al aprobar constancia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/constancias');
    }

    public function rechazarConstancia(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('/admin/constancias');
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $motivo = Security::sanitizar($_POST['motivo_rechazo'] ?? 'Sin motivo especificado');

        try {
            $this->constanciaService->rechazar($id, $motivo, $_SESSION['usuario_id']);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'solicitudes_constancia',
                $id,
                "Constancia rechazada: $motivo"
            );

            $_SESSION['flash']['success'] = 'Constancia rechazada';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al rechazar constancia: ' . $e->getMessage();
        }

        $this->redirigir('/admin/constancias');
    }

    // ==================== PERMISOS ====================

    public function mostrarAsignarPermisos(int $id_usuario): void
    {
        $usuario = $this->usuarioService->obtenerPorId($id_usuario);
        if (!$usuario) {
            $_SESSION['flash']['error'] = 'Usuario no encontrado';
            $this->redirigir('/admin/usuarios');
        }

        $permisos = $this->permisoService->obtenerTodos();
        $permisos_asignados = $this->permisoService->obtenerPermisosPorUsuario($id_usuario);
        $ids_asignados = array_column($permisos_asignados, 'id');

        $this->render('admin/permisos/asignar', [
            'titulo' => 'Asignar Permisos',
            'usuario' => $usuario,
            'permisos' => $permisos,
            'permisos_asignados' => $ids_asignados
        ]);
    }

    public function guardarPermisos(int $id_usuario): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir("/admin/permisos/asignar/$id_usuario");
        }

        Security::validarTokenCSRF($_POST['csrf_token'] ?? '');

        $permisos_ids = $_POST['permisos'] ?? [];

        try {
            $this->permisoService->asignarPermisos($id_usuario, $permisos_ids);
            
            $this->auditoriaService->registrar(
                $_SESSION['usuario_id'],
                'UPDATE',
                'usuario_permisos',
                $id_usuario,
                'Permisos actualizados'
            );

            $_SESSION['flash']['success'] = 'Permisos asignados exitosamente';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Error al asignar permisos: ' . $e->getMessage();
        }

        $this->redirigir('/admin/usuarios');
    }
}
