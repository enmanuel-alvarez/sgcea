<?php

declare(strict_types=1);

namespace Src\Models\Repositories;

use Src\Core\Database;
use PDO;

class EstudianteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los estudiantes con datos básicos (usuario)
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula, u.email 
                FROM estudiantes e 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                WHERE e.estado = 1 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los estudiantes con detalles completos para la vista:
     * - 'nombres' y 'apellidos' (alias de u.nombre, u.apellido)
     * - 'grado_nombre' y 'seccion_nombre' desde inscripción activa
     * - 'activo' (estado)
     */
    public function obtenerTodosConDetalles(): array
    {
        $sql = "SELECT 
                    e.id,
                    u.cedula,
                    u.nombre,
                    u.nombre AS nombres,
                    u.apellido,
                    u.apellido AS apellidos,
                    e.fecha_nacimiento,
                    e.genero,
                    e.foto,
                    g.nombre AS grado_nombre,
                    s.nombre AS seccion_nombre,
                    e.estado AS activo
                FROM estudiantes e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                LEFT JOIN inscripciones i ON e.id = i.estudiante_id AND i.estado = 'activo'
                LEFT JOIN secciones s ON i.seccion_id = s.id
                LEFT JOIN grados g ON s.grado_id = g.id
                WHERE e.estado = 1
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula, u.email 
                FROM estudiantes e 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function obtenerPorUsuario(int $usuario_id): ?array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula, u.email 
                FROM estudiantes e 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                WHERE e.usuario_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function crear(array $datos): int
    {
        $sql = "INSERT INTO estudiantes (usuario_id, fecha_nacimiento, genero, direccion, telefono, nombre_representante, telefono_representante, foto, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos['usuario_id'],
            $datos['fecha_nacimiento'] ?? null,
            $datos['genero'] ?? null,
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['nombre_representante'] ?? null,
            $datos['telefono_representante'] ?? null,
            $datos['foto'] ?? null,
            $datos['estado'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        if (array_key_exists('foto', $datos) && $datos['foto'] !== null) {
            $sql = "UPDATE estudiantes SET 
                    fecha_nacimiento = ?, genero = ?, direccion = ?, telefono = ?, 
                    nombre_representante = ?, telefono_representante = ?, foto = ?, estado = ? 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['fecha_nacimiento'] ?? null,
                $datos['genero'] ?? null,
                $datos['direccion'] ?? null,
                $datos['telefono'] ?? null,
                $datos['nombre_representante'] ?? null,
                $datos['telefono_representante'] ?? null,
                $datos['foto'],
                $datos['estado'] ?? 1,
                $id
            ]);
        }

        $sql = "UPDATE estudiantes SET 
                fecha_nacimiento = ?, genero = ?, direccion = ?, telefono = ?, 
                nombre_representante = ?, telefono_representante = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['fecha_nacimiento'] ?? null,
            $datos['genero'] ?? null,
            $datos['direccion'] ?? null,
            $datos['telefono'] ?? null,
            $datos['nombre_representante'] ?? null,
            $datos['telefono_representante'] ?? null,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "UPDATE estudiantes SET estado = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function verificarCodigoUnico(string $codigo, ?int $excluir_id = null): bool
    {
        // Código no existe en la tabla estudiantes; la unicidad está en usuarios.cedula
        return true;
    }

    public function obtenerEstudiantesPorSeccion(int $seccion_id): array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula 
                FROM estudiantes e 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN inscripciones i ON e.id = i.estudiante_id 
                WHERE i.seccion_id = ? AND i.estado = 'activo' AND e.estado = 1 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$seccion_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerConInscripcionActual(int $ano_lectivo): array
    {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.cedula, g.nombre as grado, s.nombre as seccion 
                FROM estudiantes e 
                INNER JOIN usuarios u ON e.usuario_id = u.id 
                INNER JOIN inscripciones i ON e.id = i.estudiante_id AND i.ano_academico = ? AND i.estado = 'activo'
                INNER JOIN secciones s ON i.seccion_id = s.id 
                INNER JOIN grados g ON s.grado_id = g.id 
                WHERE e.estado = 1 
                ORDER BY u.apellido, u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ano_lectivo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método adicional usado por DashboardService
    public function contarActivos(): int
    {
        $sql = "SELECT COUNT(*) FROM estudiantes WHERE estado = 1";
        return (int) $this->db->query($sql)->fetchColumn();
    }
}

