<?php

namespace Src\Core;

/**
 * Autoloader PSR-4
 * Registra y carga clases automáticamente según el namespace
 */

class Autoloader
{
    private static array $namespaces = [
        'Src\\Controllers' => __DIR__ . '/../app/Controllers',
        'Src\\Models\\Repositories' => __DIR__ . '/../app/Models/Repositories',
        'Src\\Models\\Services' => __DIR__ . '/../app/Models/Services',
        'Src\\Core' => __DIR__,
    ];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        foreach (self::$namespaces as $namespace => $baseDir) {
            $prefixLength = strlen($namespace);
            
            if (strncmp($namespace, $class, $prefixLength) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $prefixLength);
            $file = $baseDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }

    public static function addNamespace(string $namespace, string $baseDir): void
    {
        self::$namespaces[$namespace] = $baseDir;
    }
}
