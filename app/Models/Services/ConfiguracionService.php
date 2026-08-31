<?php

declare(strict_types=1);

namespace Src\Models\Services;

use Src\Models\Repositories\ConfiguracionRepository;

class ConfiguracionService
{
    private ConfiguracionRepository $configuracionRepository;

    public function __construct()
    {
        $this->configuracionRepository = new ConfiguracionRepository();
    }

    /**
     * Obtener todas las configuraciones
     */
    public function obtenerTodas(): array
    {
        return $this->configuracionRepository->obtenerTodos();
    }

    /**
     * Obtener todas las configuraciones organizadas como mapa asociativo (clave => valor)
     */
    public function obtenerMapa(): array
    {
        $rows = $this->configuracionRepository->obtenerTodos();
        $mapa = [];
        foreach ($rows as $r) {
            $mapa[$r['clave']] = $r['valor'];
        }
        return $mapa;
    }

    /**
     * Obtener una configuración por clave
     */
    public function obtenerPorClave(string $clave): ?string
    {
        $config = $this->configuracionRepository->obtenerPorClave($clave);
        return $config ? $config['valor'] : null;
    }

    /**
     * Actualizar una configuración
     */
    public function actualizar(string $clave, string $valor): bool
    {
        return $this->configuracionRepository->actualizar($clave, $valor);
    }

    /**
     * Obtener valor numérico de una configuración
     */
    public function obtenerValorNumerico(string $clave, float $default = 0): float
    {
        $valor = $this->obtenerPorClave($clave);
        return $valor !== null ? (float)$valor : $default;
    }

    /**
     * Obtener valor entero de una configuración
     */
    public function obtenerValorEntero(string $clave, int $default = 0): int
    {
        $valor = $this->obtenerPorClave($clave);
        return $valor !== null ? (int)$valor : $default;
    }
}


