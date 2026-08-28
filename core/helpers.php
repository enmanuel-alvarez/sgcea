<?php
/**
 * Funciones helper globales
 */

use Src\Core\Security;

if (!function_exists('e')) {
    /**
     * Escapa una cadena para HTML
     */
    function e(?string $string): string
    {
        return Security::e($string);
    }
}

if (!function_exists('asset')) {
    /**
     * Genera URL para assets (css, js, img)
     */
    function asset(string $path): string
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        return $basePath . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Genera URL relativa al basePath
     */
    function url(string $path = '/'): string
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $cleanPath = '/' . ltrim($path, '/');

        if ($cleanPath === '/') {
            return $basePath === '' ? '/' : $basePath;
        }

        return $basePath . $cleanPath;
    }
}

if (!function_exists('config')) {
    /**
     * Obtiene un valor de configuración
     */
    function config(string $key, $default = null)
    {
        static $cache = [];
        
        if (!isset($cache[$key])) {
            $parts = explode('.', $key);
            $file = array_shift($parts);
            
            $configFile = __DIR__ . '/../config/' . $file . '.php';
            if (!file_exists($configFile)) {
                return $default;
            }
            
            $config = require $configFile;
            
            $value = $config;
            foreach ($parts as $part) {
                if (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    $value = $default;
                    break;
                }
            }
            
            $cache[$key] = $value;
        }
        
        return $cache[$key];
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirecciona a una URL
     */
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('old')) {
    /**
     * Obtiene un valor antiguo del formulario (para repoblar después de error)
     */
    function old(string $key, $default = ''): string
    {
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Genera y retorna token CSRF
     */
    function csrf_token(): string
    {
        return Security::generarTokenCSRF();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Genera campo oculto CSRF para formularios
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('auth_check')) {
    /**
     * Verifica si hay un usuario autenticado
     */
    function auth_check(): bool
    {
        return isset($_SESSION['usuario_id']);
    }
}

if (!function_exists('auth_user')) {
    /**
     * Obtiene datos del usuario autenticado
     */
    function auth_user(): ?array
    {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'tipo' => $_SESSION['usuario_tipo'] ?? '',
        ];
    }
}

if (!function_exists('can')) {
    /**
     * Verifica si el usuario tiene un permiso
     */
    function can(string $permiso): bool
    {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }
        
        // Admin tiene todos los permisos
        if ($_SESSION['usuario_tipo'] === 'admin') {
            return true;
        }
        
        return in_array($permiso, $_SESSION['usuario_permisos'] ?? []);
    }
}

if (!function_exists('flash')) {
    /**
     * Obtiene y elimina mensaje flash
     */
    function flash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('now')) {
    /**
     * Retorna fecha/hora actual en formato Y-m-d H:i:s
     */
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('today')) {
    /**
     * Retorna fecha actual en formato Y-m-d
     */
    function today(): string
    {
        return date('Y-m-d');
    }
}

if (!function_exists('debug_log')) {
    /**
     * Escribe un mensaje en el log de errores
     */
    function debug_log(string $message, string $level = 'INFO'): void
    {
        $logFile = __DIR__ . '/../storage/logs/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        if (is_writable(dirname($logFile))) {
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
}
