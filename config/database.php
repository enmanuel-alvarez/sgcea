<?php
/**
 * Configuración dinámica de la base de datos basada en el archivo de entorno (.env)
 * SGCEA - Sistema de Gestión y Control Escolar-Académico
 */

use Src\Core\Env;

// Garantizar la lectura e inyección dinámica del archivo .env
if (class_exists('Src\Core\Env')) {
    Env::cargar();
}

return [
    'host'      => function_exists('env') ? env('DB_HOST', $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost') : ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost'),
    'port'      => (int) (function_exists('env') ? env('DB_PORT', $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?: 3306) : ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?: 3306)),
    'database'  => function_exists('env') ? env('DB_NAME', $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?: 'sgcea') : ($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?: 'sgcea'),
    'username'  => function_exists('env') ? env('DB_USER', $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: 'root') : ($_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? getenv('DB_USER') ?: 'root'),
    'password'  => function_exists('env') ? env('DB_PASS', $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: 'root') : ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? getenv('DB_PASS') ?: 'root'),
    'charset'   => function_exists('env') ? env('DB_CHARSET', $_ENV['DB_CHARSET'] ?? $_SERVER['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4') : ($_ENV['DB_CHARSET'] ?? $_SERVER['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4'),
    'collation' => function_exists('env') ? env('DB_COLLATION', $_ENV['DB_COLLATION'] ?? $_SERVER['DB_COLLATION'] ?? getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci') : ($_ENV['DB_COLLATION'] ?? $_SERVER['DB_COLLATION'] ?? getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci'),
    'prefix'    => function_exists('env') ? env('DB_PREFIX', $_ENV['DB_PREFIX'] ?? $_SERVER['DB_PREFIX'] ?? getenv('DB_PREFIX') ?: '') : ($_ENV['DB_PREFIX'] ?? $_SERVER['DB_PREFIX'] ?? getenv('DB_PREFIX') ?: ''),
];
// SGCEA v1.0 Release - Listo para Producción

