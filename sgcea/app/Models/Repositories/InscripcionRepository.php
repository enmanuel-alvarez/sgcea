<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class InscripcionRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT i.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido, 
                       g.nombre as grado, s.nombre as seccion
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                ORDER BY i.ano_lectivo DESC, e.apellido, e.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT i.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido 
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorEstudiante(int $estudiante_id): array
    {
        $sql = "SELECT i.* FROM inscripciones i 
                WHERE i.estudiante_id = ? 
                ORDER BY i.ano_lectivo DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudianteYAno(int $estudiante_id, string $ano_lectivo): ?array
    {
        $sql = "SELECT i.* FROM inscripciones i 
                WHERE i.estudiante_id = ? AND i.ano_lectivo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_lectivo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO inscripciones (estudiante_id, ano_lectivo, fecha_inscripcion, estado) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['estudiante_id'],
            $datos['ano_lectivo'],
            $datos['fecha_inscripcion'] ?? date('Y-m-d'),
            $datos['estado'] ?? 'activa'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE inscripciones SET ano_lectivo = ?, fecha_inscripcion = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['ano_lectivo'],
            $datos['fecha_inscripcion'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE inscripciones SET estado = 'cancelada' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarInscripcionActiva(int $estudiante_id, string $ano_lectivo): bool
    {
        $sql = "SELECT COUNT(*) FROM inscripciones 
                WHERE estudiante_id = ? AND ano_lectivo = ? AND estado = 'activa'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$estudiante_id, $ano_lectivo]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public function obtenerPorAnoLectivo(string $ano_lectivo): array
    {
        $sql = "SELECT i.*, e.nombre as estudiante_nombre, e.apellido as estudiante_apellido, 
                       e.codigo as codigo_estudiante, g.nombre as grado, s.nombre as seccion
                FROM inscripciones i 
                INNER JOIN estudiantes e ON i.estudiante_id = e.id 
                LEFT JOIN grados g ON e.grado_id = g.id 
                LEFT JOIN secciones s ON e.seccion_id = s.id 
                WHERE i.ano_lectivo = ? AND i.estado = 'activa'
                ORDER BY e.apellido, e.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_lectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPorAnoLectivo(string $ano_lectivo): int
    {
        $sql = "SELECT COUNT(*) FROM inscripciones WHERE ano_lectivo = ? AND estado = 'activa'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_lectivo]);
        return (int) $stmt->fetchColumn();
    }
}
