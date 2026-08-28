<?php
/**
 * Configuración de la base de datos basada en variables de entorno (.env)
 */

return [
    'host'      => env('DB_HOST', 'localhost'),
    'port'      => env('DB_PORT', 3306),
    'database'  => env('DB_NAME', 'sgcea'),
    'username'  => env('DB_USER', 'root'),
    'password'  => env('DB_PASS', 'root'),
    'charset'   => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    'prefix'    => env('DB_PREFIX', ''),
];
