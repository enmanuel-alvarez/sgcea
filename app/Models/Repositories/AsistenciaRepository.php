<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;

use PDO;

class AsistenciaRepository
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

    public function obtenerPorEstudianteYFecha(int $estudiante_id, string $fecha): ?array
    {
        $sql = "SELECT a.* FROM asistencias a 
                WHERE a.estudiante_id = ? AND DATE(a.fecha) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorAsignacionYFecha(int $asignacion_id, string $fecha): array
    {
        $sql = "SELECT a.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido 
                FROM asistencias a 
                INNER JOIN estudiantes e ON a.estudiante_id = e.id 
                INNER JOIN asignaciones asc_asig ON a.seccion_id = asc_asig.seccion_id 
                WHERE asc_asig.id = ? AND DATE(a.fecha) = ?
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$asignacion_id, $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        $sql = "SELECT a.* FROM asistencias a WHERE a.estudiante_id = ?";
        $params = [$estudiante_id];
        
        if ($desde && $hasta) {
            $sql .= " AND DATE(a.fecha) BETWEEN ? AND ?";
            $params[] = $desde;
            $params[] = $hasta;
        }
        
        $sql .= " ORDER BY a.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarMasiva(array $asistencias): bool
    {
        if (empty($asistencias)) {
            return true;
        }

        $sql = "INSERT INTO asistencias (estudiante_id, asignacion_id, fecha, estado, observacion, periodo) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                estado = VALUES(estado), 
                observacion = VALUES(observacion)";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($asistencias as $asistencia) {
            $stmt->execute([
                $asistencia['estudiante_id'],
                $asistencia['seccion_id'],
                $asistencia['fecha'],
                $asistencia['estado'],
                $asistencia['observacion'] ?? null
            ]);
        }
        
        return true;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO asistencias (estudiante_id, seccion_id, fecha, estado, observacion) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['seccion_id'],
            $datos['fecha'],
            $datos['estado'],
            $datos['observacion'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE asistencias SET estado = ?, observacion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['estado'],
            $datos['observacion'] ?? null,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM asistencias WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function contarPorEstudianteYEstado(int $estudiante_id, string $estado, ?string $desde = null, ?string $hasta = null): int
    {
        $sql = "SELECT COUNT(*) FROM asistencias WHERE estudiante_id = ? AND estado = ?";
        $params = [$estudiante_id, $estado];
        
        if ($desde && $hasta) {
            $sql .= " AND DATE(fecha) BETWEEN ? AND ?";
            $params[] = $desde;
            $params[] = $hasta;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerResumenPorEstudiante(int $estudiante_id, ?string $desde = null, ?string $hasta = null): array
    {
        $sql = "SELECT estado, COUNT(*) as cantidad 
                FROM asistencias 
                WHERE estudiante_id = ?";
        $params = [$estudiante_id];
        
        if ($desde && $hasta) {
            $sql .= " AND DATE(fecha) BETWEEN ? AND ?";
            $params[] = $desde;
            $params[] = $hasta;
        }
        
        $sql .= " GROUP BY estado";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorSeccionYFecha(int $seccion_id, string $fecha): array
    {
        $sql = "SELECT a.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido 
                FROM asistencias a 
                INNER JOIN estudiantes e ON a.estudiante_id = e.id 
                WHERE a.seccion_id = ? AND DATE(a.fecha) = ?
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id, $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenGeneral(): array
    {
        $sql = "SELECT estado, COUNT(*) as cantidad FROM asistencias GROUP BY estado";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerResumenPorSeccion(int $seccion_id, string $fecha_inicio, string $fecha_fin): array
    {
        $sql = "SELECT estado, COUNT(*) as cantidad 
                FROM asistencias 
                WHERE seccion_id = ? AND DATE(fecha) BETWEEN ? AND ? 
                GROUP BY estado";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id, $fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
