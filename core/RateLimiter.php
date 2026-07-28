<?php
/**
 * RateLimiter - Control de intentos de login usando base de datos
 * Limita a 5 intentos en 15 minutos por IP
 */

class RateLimiter
{
    private const MAX_INTENTOS = 5;
    private const VENTANA_MINUTOS = 15;
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verifica si la IP ha excedido el límite de intentos
     */
    public function puedeIntentar(string $ip): bool
    {
        $this->limpiarAntiguos($ip);

        $sql = "SELECT COUNT(*) as total FROM intentos_login 
                WHERE ip = :ip 
                AND fecha_intento >= DATE_SUB(NOW(), INTERVAL :minutos MINUTE)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':minutos', self::VENTANA_MINUTOS, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        
        return (int) $resultado['total'] < self::MAX_INTENTOS;
    }

    /**
     * Registra un intento fallido de login
     */
    public function registrarIntento(string $ip, ?string $email = null): void
    {
        $sql = "INSERT INTO intentos_login (ip, email, fecha_intento) 
                VALUES (:ip, :email, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Limpia los intentos antiguos para una IP
     */
    private function limpiarAntiguos(string $ip): void
    {
        $sql = "DELETE FROM intentos_login 
                WHERE fecha_intento < DATE_SUB(NOW(), INTERVAL :minutos MINUTE)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':minutos', self::VENTANA_MINUTOS, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Obtiene el tiempo restante antes de que se desbloquee el acceso
     */
    public function obtenerTiempoRestante(string $ip): int
    {
        $sql = "SELECT UNIX_TIMESTAMP(MAX(fecha_intento)) as ultimo_intento 
                FROM intentos_login 
                WHERE ip = :ip";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        
        if (!$resultado || !$resultado['ultimo_intento']) {
            return 0;
        }

        $tiempoTranscurrido = time() - (int) $resultado['ultimo_intento'];
        $ventanaSegundos = self::VENTANA_MINUTOS * 60;
        
        return max(0, $ventanaSegundos - $tiempoTranscurrido);
    }

    /**
     * Limpia todos los intentos para una IP (usar después de login exitoso)
     */
    public function limpiarIntentos(string $ip): void
    {
        $sql = "DELETE FROM intentos_login WHERE ip = :ip";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->execute();
    }
}
