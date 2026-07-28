<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class ConfiguracionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todas las configuraciones
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT id, clave, valor, descripcion, fecha_actualizacion 
                FROM configuraciones 
                ORDER BY clave ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener configuración por clave
     */
    public function obtenerPorClave(string $clave): ?array
    {
        $sql = "SELECT id, clave, valor, descripcion, fecha_actualizacion 
                FROM configuraciones 
                WHERE clave = :clave";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['clave' => $clave]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /**
     * Actualizar configuración
     */
    public function actualizar(string $clave, string $valor): bool
    {
        $sql = "UPDATE configuraciones 
                SET valor = :valor, 
                    fecha_actualizacion = NOW() 
                WHERE clave = :clave";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'clave' => $clave,
            'valor' => $valor
        ]);
    }

    /**
     * Insertar o actualizar (upsert)
     */
    public function insertarOActualizar(string $clave, string $valor, string $descripcion = ''): bool
    {
        $sql = "INSERT INTO configuraciones (clave, valor, descripcion, fecha_actualizacion)
                VALUES (:clave, :valor, :descripcion, NOW())
                ON DUPLICATE KEY UPDATE 
                    valor = :valor_update,
                    descripcion = :descripcion_update,
                    fecha_actualizacion = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'clave' => $clave,
            'valor' => $valor,
            'descripcion' => $descripcion,
            'valor_update' => $valor,
            'descripcion_update' => $descripcion
        ]);
    }
}
