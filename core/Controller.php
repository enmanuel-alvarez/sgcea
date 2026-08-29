<?php

namespace Src\Core;

use Exception;

/**
 * Controlador base con métodos comunes
 */

class Controller
{
    /**
     * Renderiza una vista con datos opcionales
     * 
     * @param string $vista Nombre de la vista (sin extensión .php)
     * @param array $datos Datos para pasar a la vista
     * @param bool $layout Si true, usa el layout completo; si false, solo la vista
     * @param bool $printLayout Si true, usa layout de impresión
     */
    protected function render(string $vista, array $datos = [], bool $layout = true, bool $printLayout = false): void
    {
        extract($datos);

        if ($printLayout) {
            // Layout de impresión
            require __DIR__ . '/../app/Views/layouts/print.php';
        } elseif ($layout) {
            // Layout completo con header, sidebar y footer
            require __DIR__ . '/../app/Views/layouts/header.php';
            require __DIR__ . '/../app/Views/layouts/sidebar.php';
            
            // Cargar vista
            $vistaPath = __DIR__ . '/../app/Views/' . $vista . '.php';
            if (file_exists($vistaPath)) {
                require $vistaPath;
            } else {
                throw new Exception("Vista no encontrada: {$vista}");
            }
            
            require __DIR__ . '/../app/Views/layouts/footer.php';
        } else {
            // Solo vista sin layout
            $vistaPath = __DIR__ . '/../app/Views/' . $vista . '.php';
            if (file_exists($vistaPath)) {
                require $vistaPath;
            } else {
                throw new Exception("Vista no encontrada: {$vista}");
            }
        }
    }

    /**
     * Redirige a una URL
     */
    protected function redirigir(string $url): void
    {
        // Si la URL es relativa, agregar basePath
        if (strpos($url, 'http') !== 0) {
            $basePath = defined('BASE_PATH') ? BASE_PATH : '/sgcea/public';
            $url = $basePath . $url;
        }
        
        header('Location: ' . $url);
        exit;
    }

    /**
     * Retorna una respuesta JSON
     */
    protected function json(array $datos, int $codigo = 200): void
    {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    /**
     * Establece un mensaje flash en sesión
     */
    protected function setFlash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje
        ];
    }

    /**
     * Obtiene y elimina un mensaje flash de sesión
     */
    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Verifica si el usuario actual tiene un permiso específico
     */
    protected function exigirPermiso(string $permiso): bool
    {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        // El administrador tiene todos los permisos
        if ($_SESSION['usuario_tipo'] === 'admin') {
            return true;
        }

        return in_array($permiso, $_SESSION['usuario_permisos'] ?? []);
    }

    /**
     * Obtiene el ID del usuario actual
     */
    protected function usuarioId(): ?int
    {
        return $_SESSION['usuario_id'] ?? null;
    }

    /**
     * Obtiene el tipo del usuario actual
     */
    protected function usuarioTipo(): ?string
    {
        return $_SESSION['usuario_tipo'] ?? null;
    }
}

