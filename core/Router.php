<?php
/**
 * Router - Enrutador con verificación de permisos ACL
 */

class Router
{
    private array $routes;
    private string $basePath = '/sgcea/public';

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../config/routes.php';
    }

    public function resolver(string $uri, string $method): void
    {
        // Remover basePath de la URI si existe
        if (!empty($this->basePath) && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        // Normalizar URI
        $uri = parse_url($uri, PHP_URL_PATH);
        if ($uri === '/') {
            $uri = '/login';
        }

        // Buscar ruta coincidente
        $route = $this->buscarRuta($uri, $method);

        if (!$route) {
            http_response_code(404);
            $this->renderError('404');
            return;
        }

        [$controllerName, $action] = $route['handler'];
        $permisoRequerido = $route['permission'];
        $params = $route['params'];

        // Verificar autenticación (excepto para rutas públicas)
        if ($permisoRequerido !== null && !isset($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = $uri;
            header('Location: ' . $this->basePath . '/login');
            exit;
        }

        // Verificar permiso ACL
        if ($permisoRequerido !== null && !$this->tienePermiso($permisoRequerido)) {
            http_response_code(403);
            $this->renderError('403');
            return;
        }

        // Instanciar controlador y ejecutar acción
        $controllerClass = "Src\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controlador no encontrado: {$controllerClass}");
        }

        $controller = new $controllerClass();
        
        if (!method_exists($controller, $action)) {
            throw new Exception("Método no encontrado: {$action} en {$controllerClass}");
        }

        // Llamar al método con parámetros
        call_user_func_array([$controller, $action], $params);
    }

    private function buscarRuta(string $uri, string $method): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            $patternRegex = $this->convertirAPatron($pattern);
            
            if (preg_match($patternRegex, $uri, $matches)) {
                array_shift($matches); // Remover match completo
                
                return [
                    'handler' => explode('@', $handler[0]),
                    'permission' => $handler[1] ?? null,
                    'params' => $this->extraerParametros($matches, $pattern)
                ];
            }
        }

        return null;
    }

    private function convertirAPatron(string $pattern): string
    {
        // Escapar caracteres especiales excepto {}
        $pattern = preg_replace('/([^\w\/{}])/', '\\\$1', $pattern);
        
        // Convertir {param} a grupos de captura
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
        
        return '#^' . $pattern . '$#';
    }

    private function extraerParametros(array $matches, string $pattern): array
    {
        preg_match_all('/\{(\w+)\}/', $pattern, $paramNames);
        $paramNames = $paramNames[1];
        
        $params = [];
        foreach ($paramNames as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }
        
        return $params;
    }

    private function tienePermiso(string $permiso): bool
    {
        if (!isset($_SESSION['usuario_permisos'])) {
            return false;
        }

        // El administrador tiene todos los permisos
        if ($_SESSION['usuario_tipo'] === 'admin') {
            return true;
        }

        return in_array($permiso, $_SESSION['usuario_permisos']);
    }

    private function renderError(string $codigo): void
    {
        $vista = __DIR__ . '/../app/Views/errors/' . $codigo . '.php';
        if (file_exists($vista)) {
            require $vista;
        } else {
            echo "<h1>Error {$codigo}</h1>";
        }
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }
}
