<?php
/**
 * Inicializador de la Aplicación (MVC Architecture)
 * Inicialización centralizada de constantes, autoloader, sesión, configuración y helpers
 */

use Src\Core\Autoloader;
use Src\Core\Security;

// 1. Definir constante de directorio base dinámico adaptativo (soporta /sgcea, /sgcea/public y raíz /)
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/'); // ej: /sgcea/public
$projectDir = rtrim(str_replace('/public', '', $scriptDir), '/');     // ej: /sgcea

if (!empty($scriptDir) && strpos($requestUri, $scriptDir) === 0) {
    define('BASE_PATH', $scriptDir);
} elseif (!empty($projectDir) && strpos($requestUri, $projectDir) === 0) {
    define('BASE_PATH', $projectDir);
} else {
    define('BASE_PATH', '');
}

define('APP_ROOT', dirname(__DIR__));

// 2. Registrar autoloader de clases (namespace Src\)
require_once __DIR__ . '/../core/Autoloader.php';
Autoloader::register();

// 2.1 Cargar variables de entorno (.env) y helpers globales
\Src\Core\Env::cargar();
require_once __DIR__ . '/../core/helpers.php';

// 3. Cargar configuración global de la aplicación
$appConfig = require __DIR__ . '/app.php';

// 4. Establecer zona horaria
date_default_timezone_set($appConfig['zona_horaria'] ?? 'America/Caracas');

// 5. Establecer modo debug y manejo de errores
define('APP_DEBUG', $appConfig['debug'] ?? false);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 6. Configurar e iniciar sesión segura
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_secure', '0'); // Cambiar a 1 en producción con HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', '3600');
    
    $sessionPath = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }
    ini_set('session.save_path', $sessionPath);
    
    session_start();
}

// 7. Establecer cabeceras de seguridad HTTP
Security::establecerHeadersSeguridad();

// 8. Cargar funciones helper globales
require_once __DIR__ . '/../core/helpers.php';

