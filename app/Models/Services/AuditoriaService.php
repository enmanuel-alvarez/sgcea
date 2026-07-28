<?php

namespace Src\Models\Services;

/**
 * Servicio de Auditoría
 * Registra todas las acciones críticas del sistema
 */
class AuditoriaService
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Registrar acción en auditoría
     */
    public function registrar(
        ?int $usuarioId,
        string $accion,
        ?string $tabla = null,
        ?int $registroId = null,
        array $detalles = []
    ): int {
        $sql = "INSERT INTO auditoria (usuario_id, accion, tabla, registro_id, detalles, ip, user_agent, fecha)
                VALUES (:usuario_id, :accion, :tabla, :registro_id, :detalles, :ip, :user_agent, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
        $stmt->bindValue(':accion', $accion, \PDO::PARAM_STR);
        $stmt->bindValue(':tabla', $tabla, \PDO::PARAM_STR);
        $stmt->bindValue(':registro_id', $registroId, \PDO::PARAM_INT);
        $stmt->bindValue(':detalles', !empty($detalles) ? json_encode($detalles) : null, \PDO::PARAM_STR);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? null, \PDO::PARAM_STR);
        $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? null, \PDO::PARAM_STR);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Obtener registros de auditoría con paginación
     */
    public function obtenerRegistros(int $limite = 50, int $offset = 0): array
    {
        $sql = "SELECT a.*, u.nombre, u.apellido, u.email
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.fecha DESC
                LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Obtener registros por usuario
     */
    public function obtenerPorUsuario(int $usuarioId, int $limite = 50): array
    {
        $sql = "SELECT * FROM auditoria
                WHERE usuario_id = :usuario_id
                ORDER BY fecha DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, \PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Obtener registros por tabla
     */
    public function obtenerPorTabla(string $tabla, int $limite = 50): array
    {
        $sql = "SELECT * FROM auditoria
                WHERE tabla = :tabla
                ORDER BY fecha DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tabla', $tabla, \PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Buscar en auditoría por término
     */
    public function buscar(string $termino, int $limite = 50): array
    {
        $sql = "SELECT a.*, u.nombre, u.apellido
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.accion LIKE :termino OR a.tabla LIKE :termino OR a.detalles LIKE :termino
                ORDER BY a.fecha DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':termino', "%{$termino}%", \PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Contar total de registros
     */
    public function contarTotal(): int
    {
        $sql = "SELECT COUNT(*) as total FROM auditoria";
        $result = $this->db->query($sql)->fetch();
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Limpiar registros antiguos (más de 90 días)
     */
    public function limpiarAntiguos(int $dias = 90): int
    {
        $sql = "DELETE FROM auditoria
                WHERE fecha < DATE_SUB(NOW(), INTERVAL :dias DAY)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':dias', $dias, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Registrar login exitoso
     */
    public function registrarLogin(int $usuarioId): void
    {
        $this->registrar(
            $usuarioId,
            'login_exitoso',
            'usuarios',
            $usuarioId,
            ['ip' => $_SERVER['REMOTE_ADDR'] ?? '']
        );
    }

    /**
     * Registrar logout
     */
    public function registrarLogout(int $usuarioId): void
    {
        $this->registrar(
            $usuarioId,
            'logout',
            'usuarios',
            $usuarioId
        );
    }
}
