<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class PermisoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM permisos ORDER BY modulo, nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM permisos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorNombre(string $nombre): ?array
    {
        $sql = "SELECT * FROM permisos WHERE nombre = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPermisosPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.modulo
                FROM permisos p
                INNER JOIN usuario_permisos up ON p.id = up.permiso_id
                WHERE up.usuario_id = ?
                ORDER BY p.modulo, p.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tienePermiso(int $usuarioId, string $permisoNombre): bool
    {
        $sql = "SELECT COUNT(*) as total 
                FROM usuario_permisos up
                INNER JOIN permisos p ON up.permiso_id = p.id
                WHERE up.usuario_id = ? AND p.nombre = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuarioId, $permisoNombre]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0) > 0;
    }

    public function asignarPermisos(int $usuarioId, array $permisoIds): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar permisos actuales
            $sqlDelete = "DELETE FROM usuario_permisos WHERE usuario_id = ?";
            $stmtDelete = $this->db->prepare($sqlDelete);
            $stmtDelete->execute([$usuarioId]);

            // Insertar nuevos permisos
            if (!empty($permisoIds)) {
                $sqlInsert = "INSERT INTO usuario_permisos (usuario_id, permiso_id) VALUES (?, ?)";
                $stmtInsert = $this->db->prepare($sqlInsert);
                foreach ($permisoIds as $permisoId) {
                    $stmtInsert->execute([$usuarioId, (int)$permisoId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error al asignar permisos: " . $e->getMessage());
            return false;
        }
    }

    public function asignarPermiso(int $usuarioId, int $permisoId, ?int $asignadoPor = null): bool
    {
        try {
            $sql = "INSERT IGNORE INTO usuario_permisos (usuario_id, permiso_id, asignado_por) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$usuarioId, $permisoId, $asignadoPor]);
        } catch (\PDOException $e) {
            error_log("Error al asignar permiso individual: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorModulo(string $modulo): array
    {
        $sql = "SELECT * FROM permisos WHERE modulo = ? ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$modulo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosAgrupadosPorModulo(): array
    {
        $sql = "SELECT modulo, GROUP_CONCAT(nombre ORDER BY nombre SEPARATOR '|') as permisos
                FROM permisos
                GROUP BY modulo
                ORDER BY modulo";
        $stmt = $this->db->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $agrupados = [];
        foreach ($resultados as $row) {
            $agrupados[$row['modulo']] = explode('|', $row['permisos']);
        }
        return $agrupados;
    }
}

