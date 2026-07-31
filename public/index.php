<?php
/**
 * Front Controller - Punto de entrada único de la aplicación
 */

use Src\Core\Router;

// Cargar bootstrap
require_once __DIR__ . '/../bootstrap/app.php';

try {
    // Obtener URI y método HTTP
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Instanciar router y resolver ruta
    $router = new Router();
    $router->resolver($uri, $method);
    
} catch (Exception $e) {
    // Manejo de excepciones no capturadas
    if (APP_DEBUG) {
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
