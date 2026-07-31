<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use PDO;

class AsignacionRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT a.*, u_doc.nombre as docente_nombre, u_doc.apellido as docente_apellido, 
                       m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN profesores d ON a.profesor_id = d.id INNER JOIN usuarios u_doc ON d.usuario_id = u_doc.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                LEFT JOIN grados g ON s.grado_id = g.id 
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT a.*, u_doc.nombre as docente_nombre, u_doc.apellido as docente_apellido, 
                       m.nombre as materia_nombre, m.id as materia_id,
                       s.nombre as seccion_nombre, s.id as seccion_id, 
                       g.id as grado_id, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN profesores d ON a.profesor_id = d.id INNER JOIN usuarios u_doc ON d.usuario_id = u_doc.id 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                LEFT JOIN grados g ON s.grado_id = g.id 
                WHERE a.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO asignaciones (docente_id, materia_id, seccion_id, ano_lectivo, estado) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['docente_id'],
            $datos['materia_id'],
            $datos['seccion_id'],
            $datos['ano_lectivo'],
            $datos['estado'] ?? 'activa'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE asignaciones SET docente_id = ?, materia_id = ?, seccion_id = ?, ano_lectivo = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['docente_id'],
            $datos['materia_id'],
            $datos['seccion_id'],
            $datos['ano_lectivo'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE asignaciones SET estado = 'inactiva' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerPorDocente(int $docente_id): array
    {
        $sql = "SELECT a.*, m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre
                FROM asignaciones a 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                LEFT JOIN grados g ON s.grado_id = g.id 
                WHERE a.docente_id = ? AND a.estado = 'activa'
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorSeccionYMateria(int $seccion_id, int $materia_id): ?array
    {
        $sql = "SELECT a.* FROM asignaciones a 
                WHERE a.seccion_id = ? AND a.materia_id = ? AND a.estado = 'activa'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id, $materia_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function verificarDuplicado(int $docente_id, int $materia_id, int $seccion_id, string $ano_lectivo): bool
    {
        $sql = "SELECT COUNT(*) FROM asignaciones 
                WHERE docente_id = ? AND materia_id = ? AND seccion_id = ? AND ano_lectivo = ? AND estado = 'activa'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docente_id, $materia_id, $seccion_id, $ano_lectivo]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public function obtenerConPlanEvaluacion(int $docente_id): array
    {
        $sql = "SELECT a.*, m.nombre as materia_nombre, s.nombre as seccion_nombre, g.nombre as grado_nombre,
                       CASE WHEN pe.id IS NOT NULL THEN 1 ELSE 0 END as tiene_plan
                FROM asignaciones a 
                INNER JOIN materias m ON a.materia_id = m.id 
                INNER JOIN secciones s ON a.seccion_id = s.id 
                LEFT JOIN grados g ON s.grado_id = g.id 
                LEFT JOIN planes_evaluacion pe ON a.id = pe.asignacion_id AND pe.estado = 'activo'
                WHERE a.docente_id = ? AND a.estado = 'activa'
                ORDER BY g.orden, s.nombre, m.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$docente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
