<?php
/**
 * Configuración general de la aplicación basada en variables de entorno (.env)
 */

return [
    'nombre_sistema' => env('APP_NAME', 'SGCEA'),
    'version'        => env('APP_VERSION', '1.0.0'),
    'entorno'        => env('APP_ENV', 'development'), // development, production
    'zona_horaria'   => env('APP_TIMEZONE', 'America/Caracas'),
    'idioma'         => env('APP_LANG', 'es'),
    'moneda'         => env('APP_CURRENCY', 'VES'),
    'url_base'       => env('APP_URL', 'http://localhost/sgcea/public'),
    'debug'          => env('APP_DEBUG', true),
];
