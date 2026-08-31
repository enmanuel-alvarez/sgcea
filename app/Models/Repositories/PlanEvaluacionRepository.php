<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;

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
                WHERE asignacion_id = ? AND (estado = 'activo' OR estado = '1' OR estado = 1)
                ORDER BY id ASC";
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
        $estado = 1;
        if (isset($datos['estado'])) {
            $estado = ($datos['estado'] === 'activo' || $datos['estado'] === '1' || $datos['estado'] === 1) ? 1 : 0;
        }

        $sql = "INSERT INTO planes_evaluacion (asignacion_id, nombre, tipo, ponderacion, fecha_programada, descripcion, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['asignacion_id'] ?? $datos['id_asignacion'] ?? 0,
            $datos['nombre'],
            $datos['tipo'] ?? $datos['tipo_evaluacion'] ?? 'examen',
            $datos['ponderacion'],
            $datos['fecha_programada'] ?? date('Y-m-d'),
            $datos['descripcion'] ?? null,
            $estado
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $estado = 1;
        if (isset($datos['estado'])) {
            $estado = ($datos['estado'] === 'activo' || $datos['estado'] === '1' || $datos['estado'] === 1) ? 1 : 0;
        }

        $sql = "UPDATE planes_evaluacion SET 
                nombre = ?, tipo = ?, ponderacion = ?, fecha_programada = ?, descripcion = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['tipo'] ?? $datos['tipo_evaluacion'] ?? 'examen',
            $datos['ponderacion'],
            $datos['fecha_programada'] ?? date('Y-m-d'),
            $datos['descripcion'] ?? null,
            $estado,
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
                WHERE asignacion_id = ? AND (estado = 'activo' OR estado = '1' OR estado = 1)";
        $params = [$asignacion_id];
        if ($excluir_id) {
            $sql .= " AND id != ?";
            $params[] = $excluir_id;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function obtenerConPromedios(int $asignacion_id): array
    {
        $sql = "SELECT pe.id, pe.nombre, pe.ponderacion, pe.tipo, pe.fecha_programada,
                       COUNT(DISTINCT c.estudiante_id) as estudiantes_evaluados,
                       AVG(c.nota) as nota_promedio
                FROM planes_evaluacion pe 
                LEFT JOIN calificaciones c ON pe.id = c.plan_evaluacion_id 
                WHERE pe.asignacion_id = ? AND (pe.estado = 'activo' OR pe.estado = '1' OR pe.estado = 1)
                GROUP BY pe.id, pe.nombre, pe.ponderacion, pe.tipo, pe.fecha_programada
                ORDER BY pe.id ASC";
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


