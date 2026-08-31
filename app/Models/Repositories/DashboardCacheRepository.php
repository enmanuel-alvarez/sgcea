<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class DashboardCacheRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerAdmin(): ?array
    {
        $sql = "SELECT * FROM cache_dashboard_admin WHERE id = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function actualizarAdmin(array $datos): bool
    {
        $sql = "REPLACE INTO cache_dashboard_admin (id, total_estudiantes, total_docentes, total_materias, 
                total_secciones, inscripciones_ano_actual, constancias_pendientes, fecha_actualizacion) 
                VALUES (1, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['total_estudiantes'],
            $datos['total_docentes'],
            $datos['total_materias'],
            $datos['total_secciones'],
            $datos['inscripciones_ano_actual'],
            $datos['constancias_pendientes']
        ]);
    }

    public function obtenerDocente(int $docente_id): ?array
    {
        $sql = "SELECT * FROM cache_dashboard_docente WHERE docente_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docente_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function actualizarDocente(int $docente_id, array $datos): bool
    {
        $sql = "REPLACE INTO cache_dashboard_docente (docente_id, total_asignaciones, total_estudiantes, 
                promedio_calificaciones, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $docente_id,
            $datos['total_asignaciones'],
            $datos['total_estudiantes'],
            $datos['promedio_calificaciones']
        ]);
    }

    public function esDesactualizadoAdmin(int $minutos = 30): bool
    {
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, fecha_actualizacion, NOW()) as minutos 
                FROM cache_dashboard_admin WHERE id = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) return true;
        return (int) $result['minutos'] > $minutos;
    }

    public function esDesactualizadoDocente(int $docente_id, int $minutos = 30): bool
    {
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, fecha_actualizacion, NOW()) as minutos 
                FROM cache_dashboard_docente WHERE docente_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docente_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) return true;
        return (int) $result['minutos'] > $minutos;
    }

    public function inicializarAdmin(): bool
    {
        $sql = "INSERT IGNORE INTO cache_dashboard_admin (id, total_estudiantes, total_docentes, 
                total_materias, total_secciones, inscripciones_ano_actual, constancias_pendientes) 
                VALUES (1, 0, 0, 0, 0, 0, 0)";
        return $this->db->exec($sql) !== false;
    }
}

