<?php
/**
 * Front Controller - Punto de entrada único de la aplicación (MVC Pattern)
 */

use Src\Core\Router;

// Cargar el inicializador global de la aplicación
require_once __DIR__ . '/../config/inicializador.php';

try {
    // Obtener URI y método HTTP
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    // Instanciar router
    $router = new Router();

    // Cargar y registrar las rutas definidas en config/routes.php
    $rutasConfig = require __DIR__ . '/../config/routes.php';
    foreach ($rutasConfig as $metodo => $listaRutas) {
        foreach ($listaRutas as $patron => $handler) {
            $router->agregar($metodo, $patron, $handler);
        }
    }

    // Resolver la ruta solicitada
    $router->resolver($uri, $method);
    
} catch (Exception $e) {
    // Manejo de excepciones no capturadas
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<h1>Error de la aplicación</h1>";
        echo "<p><strong>Mensaje:</strong> " . e($e->getMessage()) . "</p>";
        echo "<p><strong>Archivo:</strong> " . e($e->getFile()) . "</p>";
        echo "<p><strong>Línea:</strong> " . e($e->getLine()) . "</p>";
        echo "<pre>" . e($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        require __DIR__ . '/../app/Views/errors/500.php';
    }
}


