<?php

namespace Src\Controllers;

use Src\Core\Controller;

use Src\Models\Services\UsuarioService;
use Src\Models\Services\AuditoriaService;
use Src\Core\Security;
use Src\Core\RateLimiter;

/**
 * Controlador de Autenticación
 */
class AuthController extends Controller
{
    private UsuarioService $usuarioService;
    private AuditoriaService $auditoriaService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->auditoriaService = new AuditoriaService();
    }

    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin(): void
    {
        // Si ya está autenticado, redirigir según tipo
        if (isset($_SESSION['usuario_id'])) {
            $this->redirigirSegunTipo($_SESSION['usuario_tipo']);
            return;
        }

        $this->render('auth/login', [
            'titulo' => 'Iniciar Sesión'
        ], false);
    }

    /**
     * Procesar autenticación
     */
    public function autenticar(): void
    {
        // Validar token CSRF
        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Token de seguridad inválido');
            $this->redirigir('/login');
            return;
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validaciones básicas
        if (empty($login) || empty($password)) {
            $this->setFlash('error', 'Por favor ingrese correo y contraseña');
            $this->redirigir('/login');
            return;
        }

        // Rate limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $rateLimiter = new RateLimiter();

        if (!$rateLimiter->puedeIntentar($ip)) {
            $tiempoRestante = $rateLimiter->obtenerTiempoRestante($ip);
            $minutos = floor($tiempoRestante / 60);
            $segundos = $tiempoRestante % 60;
            
            $this->setFlash('error', "Demasiados intentos. Intente en {$minutos}m {$segundos}s");
            $this->redirigir('/login');
            return;
        }

        // Buscar usuario por email o cédula
        $usuario = null;
        
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $usuario = $this->usuarioService->obtenerPorEmail($login);
        } else {
            $usuario = $this->usuarioService->obtenerPorCedula($login);
        }

        // Verificar credenciales
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            // Registrar intento fallido
            $rateLimiter->registrarIntento($ip, $login);
            
            $this->setFlash('error', 'Correo/cedula o contraseña incorrectos');
            $this->redirigir('/login');
            return;
        }

        // Verificar que el usuario esté activo
        if ($usuario['estado'] != 1) {
            $this->setFlash('error', 'Usuario desactivado. Contacte al administrador');
            $this->redirigir('/login');
            return;
        }

        // Limpiar intentos previos
        $rateLimiter->limpiarIntentos($ip);

        // Obtener permisos del usuario
        $permisoRepo = new \Src\Models\Repositories\PermisoRepository();
        $permisos = $permisoRepo->obtenerPermisosPorUsuario($usuario['id']);
        $nombresPermisos = array_column($permisos, 'nombre');

        // Iniciar sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        $_SESSION['usuario_permisos'] = $nombresPermisos;
        $_SESSION['ultima_actividad'] = time();

        if ($usuario['tipo'] === 'docente') {
            $docenteRepo = new \Src\Models\Repositories\DocenteRepository();
            $docente = $docenteRepo->obtenerPorUsuario((int)$usuario['id']);
            if ($docente) {
                $_SESSION['profesor_id'] = (int)$docente['id'];
            }
        } elseif ($usuario['tipo'] === 'estudiante') {
            $estudianteRepo = new \Src\Models\Repositories\EstudianteRepository();
            $estudiante = $estudianteRepo->obtenerPorUsuario((int)$usuario['id']);
            if ($estudiante) {
                $_SESSION['estudiante_id'] = (int)$estudiante['id'];
            }
        }

        // Registrar login en auditoría
        $this->auditoriaService->registrarLogin($usuario['id']);

        // Redirigir según tipo de usuario
        $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);

        if ($redirectUrl) {
            $this->redirigir($redirectUrl);
        } else {
            $this->redirigirSegunTipo($usuario['tipo']);
        }
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion(): void
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        
        if ($usuarioId) {
            $this->auditoriaService->registrarLogout($usuarioId);
        }

        // Destruir sesión
        session_destroy();
        
        // Regenerar ID de sesión para seguridad
        session_start();
        session_regenerate_id(true);

        $this->setFlash('success', 'Sesión cerrada correctamente');
        $this->redirigir('/login');
    }

    /**
     * Procesar cambio de contraseña del usuario autenticado
     */
    public function cambiarPassword(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirigir('/login');
            return;
        }

        if (!Security::validarTokenCSRF($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Token de seguridad inválido.');
            $this->redirigirReferer();
            return;
        }

        $usuarioId = (int)$_SESSION['usuario_id'];
        $claveActual = $_POST['clave_actual'] ?? '';
        $claveNueva = $_POST['clave_nueva'] ?? '';
        $claveConfirmar = $_POST['clave_confirmar'] ?? '';

        if (empty($claveActual) || empty($claveNueva) || empty($claveConfirmar)) {
            $this->setFlash('error', 'Debe completar todos los campos para cambiar su contraseña.');
            $this->redirigirReferer();
            return;
        }

        if ($claveNueva !== $claveConfirmar) {
            $this->setFlash('error', 'La nueva contraseña y su confirmación no coinciden.');
            $this->redirigirReferer();
            return;
        }

        if (strlen($claveNueva) < 6) {
            $this->setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            $this->redirigirReferer();
            return;
        }

        $usuario = $this->usuarioService->obtenerPorId($usuarioId);
        if (!$usuario || !password_verify($claveActual, $usuario['password'])) {
            $this->setFlash('error', 'La contraseña actual ingresada es incorrecta.');
            $this->redirigirReferer();
            return;
        }

        // Actualizar clave en base de datos
        $nuevoHash = password_hash($claveNueva, PASSWORD_DEFAULT);
        $db = \Src\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $res = $stmt->execute([$nuevoHash, $usuarioId]);

        if ($res) {
            $this->auditoriaService->registrar(
                $usuarioId,
                'CAMBIO_PASSWORD',
                'usuarios',
                $usuarioId,
                "El usuario cambio su clave de acceso"
            );
            $this->setFlash('success', '¡Su contraseña se ha actualizado correctamente!');
        } else {
            $this->setFlash('error', 'No se pudo actualizar la contraseña. Intente nuevamente.');
        }

        $this->redirigirReferer();
    }

    private function redirigirReferer(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer && str_contains($referer, $_SERVER['HTTP_HOST'] ?? '')) {
            header("Location: {$referer}");
            exit;
        }
        $this->redirigirSegunTipo($_SESSION['usuario_tipo'] ?? 'admin');
    }

    /**
     * Redirigir según tipo de usuario
     */
    private function redirigirSegunTipo(string $tipo): void
    {
        switch ($tipo) {
            case 'admin':
                $this->redirigir('/admin');
                break;
            case 'docente':
                $this->redirigir('/docente');
                break;
            case 'estudiante':
                $this->redirigir('/estudiante');
                break;
            default:
                $this->redirigir('/login');
        }
    }
}
