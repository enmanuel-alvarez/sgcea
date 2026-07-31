<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class ConstanciaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT s.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido, 
                       u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM solicitudes_constancia s 
                INNER JOIN estudiantes e ON s.estudiante_id = e.id 
                INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                LEFT JOIN usuarios u ON s.resuelto_por = u.id 
                ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT s.*, u_est.nombre as estudiante_nombre, u_est.apellido as estudiante_apellido, 
                       u_est.cedula as codigo_estudiante, g.nombre as grado, sec.nombre as seccion,
                       u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM solicitudes_constancia s 
                INNER JOIN estudiantes e ON s.estudiante_id = e.id 
                INNER JOIN usuarios u_est ON e.usuario_id = u_est.id 
                LEFT JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activo'
                LEFT JOIN secciones sec ON i.seccion_id = sec.id 
                LEFT JOIN grados g ON sec.grado_id = g.id 
                LEFT JOIN usuarios u ON s.resuelto_por = u.id 
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO solicitudes_constancia (estudiante_id, tipo, motivo, estado) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['tipo'],               // antes era 'tipo_constancia'
            $datos['motivo'] ?? '',
            $datos['estado'] ?? 'pendiente'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function aprobar(int $id, int $aprobado_por): bool
    {
        $sql = "UPDATE solicitudes_constancia SET 
                estado = 'aprobada', fecha_resolucion = NOW(), resuelto_por = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$aprobado_por, $id]);
    }

    public function rechazar(int $id, string $motivo_rechazo): bool
    {
        $sql = "UPDATE solicitudes_constancia SET 
                estado = 'rechazada', resolucion_motivo = ?, fecha_resolucion = NOW() 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$motivo_rechazo, $id]);
    }

    public function contarPendientesPorEstudiante(int $estudiante_id): int
    {
        $sql = "SELECT COUNT(*) FROM solicitudes_constancia 
                WHERE estudiante_id = ? AND estado = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return (int) $stmt->fetchColumn();
    }

    public function contarPorEstado(string $estado): int
    {
        $sql = "SELECT COUNT(*) FROM solicitudes_constancia WHERE estado = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estado]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerAprobadasPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT * FROM solicitudes_constancia 
                WHERE estudiante_id = ? AND estado = 'aprobada' 
                ORDER BY fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}