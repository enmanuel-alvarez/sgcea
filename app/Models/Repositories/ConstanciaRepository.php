<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class ConstanciaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT s.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido, 
                       u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM solicitudes_constancia s 
                INNER JOIN estudiantes e ON s.estudiante_id = e.id 
                LEFT JOIN usuarios u ON s.usuario_id = u.id 
                ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT s.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido, 
                       e.codigo as codigo_estudiante, g.nombre as grado, s.nombre as seccion,
                       u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM solicitudes_constancia s 
                INNER JOIN estudiantes e ON s.estudiante_id = e.id 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones sec ON e.seccion_id = sec.id 
                LEFT JOIN usuarios u ON s.usuario_id = u.id 
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT s.* FROM solicitudes_constancia s 
                WHERE s.estudiante_id = ? 
                ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPendientes(): array
    {
        $sql = "SELECT s.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido 
                FROM solicitudes_constancia s 
                INNER JOIN estudiantes e ON s.estudiante_id = e.id 
                WHERE s.estado = 'pendiente' 
                ORDER BY s.fecha_solicitud ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO solicitudes_constancia (estudiante_id, usuario_id, tipo_constancia, motivo, estado) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['usuario_id'] ?? null,
            $datos['tipo_constancia'],
            $datos['motivo'] ?? null,
            $datos['estado'] ?? 'pendiente'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE solicitudes_constancia SET 
                tipo_constancia = ?, motivo = ?, estado = ?, 
                fecha_aprobacion = ?, aprobado_por = ?, motivo_rechazo = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['tipo_constancia'] ?? null,
            $datos['motivo'] ?? null,
            $datos['estado'],
            $datos['fecha_aprobacion'] ?? null,
            $datos['aprobado_por'] ?? null,
            $datos['motivo_rechazo'] ?? null,
            $id
        ]);
    }

    public function aprobar(int $id, int $aprobado_por): bool
    {
        $sql = "UPDATE solicitudes_constancia SET 
                estado = 'aprobada', fecha_aprobacion = NOW(), aprobado_por = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$aprobado_por, $id]);
    }

    public function rechazar(int $id, string $motivo_rechazo): bool
    {
        $sql = "UPDATE solicitudes_constancia SET 
                estado = 'rechazada', motivo_rechazo = ? 
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
        $sql = "SELECT s.* FROM solicitudes_constancia s 
                WHERE s.estudiante_id = ? AND s.estado = 'aprobada' 
                ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
