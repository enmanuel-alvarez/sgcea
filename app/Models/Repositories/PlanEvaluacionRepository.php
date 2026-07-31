<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class PlanEvaluacionRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
            return;
        }

        $database = \Src\Core\Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function obtenerPorAsignacion(int $asignacion_id): array
    {
        $sql = "SELECT * FROM planes_evaluacion 
                WHERE asignacion_id = ? AND estado = 'activo'
                ORDER BY orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$asignacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM planes_evaluacion WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO planes_evaluacion (asignacion_id, nombre, tipo_evaluacion, ponderacion, orden, descripcion, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['asignacion_id'],
            $datos['nombre'],
            $datos['tipo_evaluacion'],
            $datos['ponderacion'],
            $datos['orden'] ?? 0,
            $datos['descripcion'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE planes_evaluacion SET 
                nombre = ?, tipo_evaluacion = ?, ponderacion = ?, orden = ?, descripcion = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['tipo_evaluacion'],
            $datos['ponderacion'],
            $datos['orden'],
            $datos['descripcion'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE planes_evaluacion SET estado = 'inactivo' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function sumarPonderacionPorAsignacion(int $asignacion_id, ?int $excluir_id = null): float
    {
        $sql = "SELECT COALESCE(SUM(ponderacion), 0) FROM planes_evaluacion 
                WHERE asignacion_id = ? AND estado = 'activo'";
        if ($excluir_id) {
            $sql .= " AND id != ?";
        }
        $stmt = $this->db->prepare($sql);
        if ($excluir_id) {
            $stmt->execute([$asignacion_id, $excluir_id]);
        } else {
            $stmt->execute([$asignacion_id]);
        }
        return (float) $stmt->fetchColumn();
    }

    public function obtenerConPromedios(int $asignacion_id): array
    {
        $sql = "SELECT pe.id, pe.nombre, pe.ponderacion, pe.tipo_evaluacion,
                       COUNT(DISTINCT c.estudiante_id) as estudiantes_evaluados,
                       AVG(c.nota) as nota_promedio
                FROM planes_evaluacion pe 
                LEFT JOIN calificaciones c ON pe.id = c.plan_evaluacion_id 
                WHERE pe.asignacion_id = ? AND pe.estado = 'activo'
                GROUP BY pe.id, pe.nombre, pe.ponderacion, pe.tipo_evaluacion
                ORDER BY pe.orden";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$asignacion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactivarPorAsignacion(int $asignacion_id): bool
    {
        $sql = "UPDATE planes_evaluacion SET estado = 'inactivo' WHERE asignacion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$asignacion_id]);
    }
}
