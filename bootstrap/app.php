<?php
/**
 * Bootstrap de la aplicación
 * Inicializa configuración, sesión, autoloader y zona horaria
 */

use Src\Core\Autoloader;
use Src\Core\Security;

// Definir constante de directorio base
define('BASE_PATH', '/sgcea/public');
define('APP_ROOT', dirname(__DIR__));

// Registrar autoloader
require_once __DIR__ . '/../core/Autoloader.php';
Autoloader::register();

// Cargar configuración de la aplicación
$appConfig = require __DIR__ . '/../config/app.php';

// Establecer zona horaria
date_default_timezone_set($appConfig['zona_horaria'] ?? 'America/Caracas');

// Establecer modo debug
define('APP_DEBUG', $appConfig['debug'] ?? false);

// Configurar sesión segura
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.save_path', __DIR__ . '/../storage/sessions');
    
    // Crear directorio de sesiones si no existe
    $sessionPath = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }
    
    session_start();
}

// Establecer headers de seguridad
Security::establecerHeadersSeguridad();

// Cargar helpers
require_once __DIR__ . '/../core/helpers.php';

// Manejador de errores personalizado
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Manejador de excepciones no capturadas
set_exception_handler(function ($exception) {
    if (APP_DEBUG) {
        echo "<h1>Error de la aplicación</h1>";
        echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>Archivo:</strong> " . htmlspecialchars($exception->getFile()) . "</p>";
        echo "<p><strong>Línea:</strong> " . htmlspecialchars($exception->getLine()) . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    } else {
        error_log($exception->getMessage());
        http_response_code(500);
        echo "<h1>Error interno del servidor</h1>";
        echo "<p>Ha ocurrido un error inesperado. Por favor contacte al administrador.</p>";
    }
});

// Manejador de errores PHP
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Verificar instalación (si existe database.php configurado)
$dbConfigFile = __DIR__ . '/../config/database.php';
if (!file_exists($dbConfigFile)) {
    die("Error: Archivo de configuración de base de datos no encontrado. Copie database.template.php a database.php y configure las credenciales.");
}
