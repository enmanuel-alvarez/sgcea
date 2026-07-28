<?php

namespace Src\Models\Repositories;

/**
 * Repositorio de Permisos
 */
class PermisoRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM permisos ORDER BY modulo, nombre";
        return $this->db->fetchAll($sql);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM permisos WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function obtenerPorNombre(string $nombre): ?array
    {
        $sql = "SELECT * FROM permisos WHERE nombre = :nombre";
        return $this->db->fetch($sql, ['nombre' => $nombre]);
    }

    public function obtenerPermisosPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT p.nombre, p.descripcion, p.modulo
                FROM permisos p
                INNER JOIN usuario_permisos up ON p.id = up.permiso_id
                WHERE up.usuario_id = :usuario_id
                ORDER BY p.modulo, p.nombre";
        
        return $this->db->fetchAll($sql, ['usuario_id' => $usuarioId]);
    }

    public function asignarPermiso(int $usuarioId, int $permisoId, ?int $asignadoPor = null): bool
    {
        try {
            $sql = "INSERT INTO usuario_permisos (usuario_id, permiso_id, asignado_por)
                    VALUES (:usuario_id, :permiso_id, :asignado_por)
                    ON DUPLICATE KEY UPDATE fecha_asignacion = CURRENT_TIMESTAMP";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
            $stmt->bindValue(':permiso_id', $permisoId, \PDO::PARAM_INT);
            $stmt->bindValue(':asignado_por', $asignadoPor, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error al asignar permiso: " . $e->getMessage());
            return false;
        }
    }

    public function quitarPermiso(int $usuarioId, int $permisoId): bool
    {
        $sql = "DELETE FROM usuario_permisos WHERE usuario_id = :usuario_id AND permiso_id = :permiso_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
        $stmt->bindValue(':permiso_id', $permisoId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function quitarTodosLosPermisos(int $usuarioId): bool
    {
        $sql = "DELETE FROM usuario_permisos WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function tienePermiso(int $usuarioId, string $permisoNombre): bool
    {
        $sql = "SELECT COUNT(*) as total 
                FROM usuario_permisos up
                INNER JOIN permisos p ON up.permiso_id = p.id
                WHERE up.usuario_id = :usuario_id AND p.nombre = :permiso_nombre";
        
        $result = $this->db->fetch($sql, [
            'usuario_id' => $usuarioId,
            'permiso_nombre' => $permisoNombre
        ]);
        
        return (int) ($result['total'] ?? 0) > 0;
    }

    public function contarTotal(): int
    {
        $sql = "SELECT COUNT(*) as total FROM permisos";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }

    public function obtenerPorModulo(string $modulo): array
    {
        $sql = "SELECT * FROM permisos WHERE modulo = :modulo ORDER BY nombre";
        return $this->db->fetchAll($sql, ['modulo' => $modulo]);
    }

    public function obtenerTodosAgrupadosPorModulo(): array
    {
        $sql = "SELECT modulo, GROUP_CONCAT(nombre ORDER BY nombre SEPARATOR '|') as permisos
                FROM permisos
                GROUP BY modulo
                ORDER BY modulo";
        
        $resultados = $this->db->fetchAll($sql);
        $agrupados = [];
        
        foreach ($resultados as $row) {
            $agrupados[$row['modulo']] = explode('|', $row['permisos']);
        }
        
        return $agrupados;
    }
}
