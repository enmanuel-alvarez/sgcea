<?php

namespace Src\Core;

/**
 * Clase Env - Cargador nativo de variables de entorno desde archivo .env
 */
class Env
{
    private static bool $cargado = false;

    /**
     * Carga el archivo .env especificado y registra las variables en $_ENV, $_SERVER y putenv()
     */
    public static function cargar(?string $archivo = null): void
    {
        if (self::$cargado) {
            return;
        }

        if ($archivo === null) {
            $archivo = defined('APP_ROOT') ? APP_ROOT . '/.env' : dirname(__DIR__) . '/.env';
        }

        if (!file_exists($archivo) || !is_readable($archivo)) {
            return;
        }

        $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lineas === false) {
            return;
        }

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            // Ignorar comentarios que inician con # o //
            if (empty($linea) || str_starts_with($linea, '#') || str_starts_with($linea, '//')) {
                continue;
            }

            // Separar por el primer '='
            $partes = explode('=', $linea, 2);
            if (count($partes) !== 2) {
                continue;
            }

            $clave = trim($partes[0]);
            $valor = trim($partes[1]);

            // Remover comillas si la cadena las contiene
            if ((str_starts_with($valor, '"') && str_ends_with($valor, '"')) ||
                (str_starts_with($valor, "'") && str_ends_with($valor, "'"))
            ) {
                $valor = substr($valor, 1, -1);
            } else {
                // Eliminar comentarios en línea si existen
                if (str_contains($valor, '#')) {
                    $valor = trim(explode('#', $valor, 2)[0]);
                }
            }

            // Asignar en $_ENV, $_SERVER y entorno del proceso si no fue sobreescrito previamente
            if (!array_key_exists($clave, $_ENV)) {
                $_ENV[$clave] = $valor;
            }
            if (!array_key_exists($clave, $_SERVER)) {
                $_SERVER[$clave] = $valor;
            }
            putenv("{$clave}={$valor}");
        }

        self::$cargado = true;
    }

    /**
     * Obtiene el valor de una variable de entorno con fallback predeterminado
     */
    public static function get(string $clave, mixed $predeterminado = null): mixed
    {
        $valor = $_ENV[$clave] ?? $_SERVER[$clave] ?? getenv($clave);

        if ($valor === false || $valor === null || $valor === '') {
            return $predeterminado;
        }

        // Convertir tipos booleanos y nulos comunes
        return match (strtolower((string)$valor)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $valor,
        };
    }
}
