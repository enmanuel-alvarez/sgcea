<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class RolRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT r.*, COUNT(rp.permiso_id) as total_permisos
                FROM roles r
                LEFT JOIN rol_permiso rp ON r.id = rp.rol_id
                GROUP BY r.id
                ORDER BY r.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM roles WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function obtenerPorSlug(string $slug): ?array
    {
        $sql = "SELECT * FROM roles WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function obtenerPermisosPorRol(int $rolId): array
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.modulo
                FROM permisos p
                INNER JOIN rol_permiso rp ON p.id = rp.permiso_id
                WHERE rp.rol_id = ?
                ORDER BY p.modulo, p.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sincronizarPermisosRol(int $rolId, array $permisoIds): bool
    {
        try {
            $this->db->beginTransaction();

            $stmtDelete = $this->db->prepare("DELETE FROM rol_permiso WHERE rol_id = ?");
            $stmtDelete->execute([$rolId]);

            if (!empty($permisoIds)) {
                $stmtInsert = $this->db->prepare("INSERT INTO rol_permiso (rol_id, permiso_id) VALUES (?, ?)");
                foreach ($permisoIds as $pId) {
                    $stmtInsert->execute([$rolId, (int)$pId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error al sincronizar permisos de rol: " . $e->getMessage());
            return false;
        }
    }
}
