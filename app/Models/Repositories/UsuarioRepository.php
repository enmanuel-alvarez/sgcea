<?php

namespace Src\Models\Repositories;

use PDO;

use Src\Core\Database;

/**
 * Repositorio de Usuarios
 */
class UsuarioRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT u.*, 
                COUNT(DISTINCT up.permiso_id) as total_permisos
                FROM usuarios u
                LEFT JOIN usuario_permisos up ON u.id = up.usuario_id
                WHERE u.estado = 1
                GROUP BY u.id
                ORDER BY u.apellido, u.nombre";
        
        return $this->db->fetchAll($sql);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function obtenerPorEmail(?string $email): ?array
    {
        if (empty($email)) {
            return null;
        }
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    public function obtenerPorCedula(?string $cedula): ?array
    {
        if (empty($cedula)) {
            return null;
        }
        $sql = "SELECT * FROM usuarios WHERE cedula = :cedula";
        return $this->db->fetch($sql, ['cedula' => $cedula]);
    }

    public function crear(array $datos): int
    {
        $email = $datos['email'] ?? $datos['correo'] ?? '';
        $tipo = $datos['tipo'] ?? $datos['tipo_usuario'] ?? 'estudiante';
        $estado = $datos['estado'] ?? $datos['estado_cuenta'] ?? $datos['activo'] ?? 1;
        $password = $datos['password'] ?? $datos['contrasena_hash'] ?? '';

        return $this->db->insert('usuarios', [
            'cedula' => $datos['cedula'] ?? '',
            'nombre' => $datos['nombre'] ?? '',
            'apellido' => $datos['apellido'] ?? '',
            'email' => $email,
            'password' => $password,
            'tipo' => $tipo,
            'estado' => $estado
        ]);
    }

    public function actualizar(int $id, array $datos): bool
    {
        $data = [];
        
        if (isset($datos['nombre'])) $data['nombre'] = $datos['nombre'];
        if (isset($datos['apellido'])) $data['apellido'] = $datos['apellido'];
        if (isset($datos['email'])) $data['email'] = $datos['email'];
        elseif (isset($datos['correo'])) $data['email'] = $datos['correo'];

        if (isset($datos['password'])) $data['password'] = $datos['password'];
        elseif (isset($datos['contrasena_hash'])) $data['password'] = $datos['contrasena_hash'];

        if (isset($datos['tipo'])) $data['tipo'] = $datos['tipo'];
        elseif (isset($datos['tipo_usuario'])) $data['tipo'] = $datos['tipo_usuario'];

        if (isset($datos['estado'])) $data['estado'] = $datos['estado'];
        elseif (isset($datos['activo'])) $data['estado'] = $datos['activo'];
        elseif (isset($datos['estado_cuenta'])) $data['estado'] = $datos['estado_cuenta'];

        if (empty($data)) {
            return false;
        }

        return $this->db->update('usuarios', $id, $data);
    }

    public function eliminar(int $id): bool
    {
        // Soft delete - cambiar estado a 0
        return $this->db->update('usuarios', $id, ['estado' => 0]);
    }

    public function buscar(string $termino): array
    {
        $sql = "SELECT * FROM usuarios 
                WHERE (nombre LIKE :termino OR apellido LIKE :termino OR email LIKE :termino OR cedula LIKE :termino)
                AND estado = 1
                ORDER BY apellido, nombre";
        
        return $this->db->fetchAll($sql, ['termino' => "%{$termino}%"]);
    }

    public function contarPorTipo(string $tipo): int
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = :tipo AND estado = 1";
        $result = $this->db->fetch($sql, ['tipo' => $tipo]);
        return (int) ($result['total'] ?? 0);
    }

    public function contarTotal(): int
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE estado = 1";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }
}


