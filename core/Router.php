<?php

namespace Src\Core;

use Exception;

/**
 * Enrutador principal de la aplicación (PHP 8.3 Tailwind v3 ready - MVC Scheme)
 * Maneja el mapeo de URLs a controladores y acciones con soporte de permisos ACL y auto-detección universal de subdirectorios
 */
class Router
{
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function agregar(string $method, string $pattern, array $handler): void
    {
        $this->routes[strtoupper($method)][$pattern] = $handler;
    }

    public function resolver(string $uri, string $method): void
    {
        // Parsear la ruta de la URI
        $cleanUri = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        // Detección automática y remoción de prefijos de subdirectorio (/sgcea, /sgcea/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/'); // ej: /sgcea/public
        $projectDir = rtrim(str_replace('/public', '', $scriptDir), '/');     // ej: /sgcea

        if (!empty($scriptDir) && strpos($cleanUri, $scriptDir) === 0) {
            $cleanUri = substr($cleanUri, strlen($scriptDir));
        } elseif (!empty($projectDir) && strpos($cleanUri, $projectDir) === 0) {
            $cleanUri = substr($cleanUri, strlen($projectDir));
        } elseif (!empty($this->basePath) && strpos($cleanUri, $this->basePath) === 0) {
            $cleanUri = substr($cleanUri, strlen($this->basePath));
        }

        $cleanUri = '/' . trim($cleanUri, '/');

        // Redirección inteligente de raíz /
        if ($cleanUri === '/' || $cleanUri === '/index.php') {
            if (isset($_SESSION['usuario_id'])) {
                $tipo = $_SESSION['usuario_tipo'] ?? '';
                $target = '/admin';
                if ($tipo === 'docente') $target = '/docente';
                if ($tipo === 'estudiante') $target = '/estudiante';
                
                header('Location: ' . url($target));
                exit;
            }
            $cleanUri = '/login';
        }

        // Carga de respaldo de config/routes.php si aún no han sido registradas
        if (empty($this->routes)) {
            $routesFile = __DIR__ . '/../config/routes.php';
            if (file_exists($routesFile)) {
                $rutas = require $routesFile;
                foreach ($rutas as $m => $lista) {
                    foreach ($lista as $patron => $h) {
                        $this->agregar($m, $patron, $h);
                    }
                }
            }
        }

        // Buscar ruta coincidente
        $route = $this->buscarRuta($cleanUri, $method);

        if (!$route) {
            http_response_code(404);
            $this->renderError('404', [
                'requestedUri' => $_SERVER['REQUEST_URI'] ?? '',
                'evaluatedUri' => $cleanUri,
                'method' => $method,
                'basePath' => defined('BASE_PATH') ? BASE_PATH : '',
                'registeredRoutes' => array_keys($this->routes[strtoupper($method)] ?? [])
            ]);
            return;
        }

        [$controllerName, $action] = $route['handler'];
        $permisoRequerido = $route['permission'];
        $params = $route['params'];

        // Verificar autenticación (excepto para rutas públicas)
        if ($permisoRequerido !== null && !isset($_SESSION['usuario_id'])) {
            $_SESSION['redirect_after_login'] = $cleanUri;
            header('Location: ' . url('/login'));
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

        // Invocación segura mediante Reflection en PHP 8
        $refMethod = new \ReflectionMethod($controller, $action);
        $methodParams = $refMethod->getParameters();
        $args = [];
        $values = array_values($params);

        foreach ($methodParams as $i => $param) {
            if (isset($values[$i])) {
                $args[] = $values[$i];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }

        $refMethod->invokeArgs($controller, $args);
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
        return array_values($matches);
    }

    private function tienePermiso(string $permiso): bool
    {
        if (!isset($_SESSION['usuario_permisos'])) {
            return false;
        }

        $tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
        if ($tipoUsuario === 'admin') {
            return true; // Superadmin acceso total
        }

        return in_array($permiso, $_SESSION['usuario_permisos']);
    }

    private function renderError(string $codigo, array $debugData = []): void
    {
        $archivo = __DIR__ . "/../app/Views/errors/{$codigo}.php";
        
        if (file_exists($archivo)) {
            require $archivo;
        } else {
            echo "<h1>Error {$codigo}</h1>";
        }
    }
}


