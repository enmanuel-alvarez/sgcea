<?php
/**
 * Vista: Listado de Usuarios (Admin)
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Usuarios</h1>
    <?php if (in_array('admin_crear_usuario', $_SESSION['usuario_permisos'] ?? [])): ?>
        <a href="/admin/usuarios/crear" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </a>
    <?php endif; ?>
</div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td><?= htmlspecialchars($usuario['cedula']) ?></td>
                                <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $usuario['rol'] === 'admin' ? 'danger' : ($usuario['rol'] === 'docente' ? 'info' : 'success') ?>">
                                        <?= ucfirst($usuario['rol']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $usuario['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($usuario['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if (in_array('admin_editar_usuario', $_SESSION['usuario_permisos'] ?? [])): ?>
                                            <a href="/admin/usuarios/editar/<?= $usuario['id'] ?>" class="btn btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array('admin_asignar_permisos', $_SESSION['usuario_permisos'] ?? [])): ?>
                                            <a href="/admin/permisos/asignar/<?= $usuario['id'] ?>" class="btn btn-warning">
                                                <i class="bi bi-key"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (in_array('admin_eliminar_usuario', $_SESSION['usuario_permisos'] ?? []) && $usuario['id'] !== 1): ?>
                                            <button type="button" class="btn btn-danger" onclick="confirmarEliminar(<?= $usuario['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<form id="formEliminar" method="POST" action="/admin/usuarios/eliminar">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id_usuario" id="idUsuarioEliminar">
</form>

<script>
function confirmarEliminar(id) {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        document.getElementById('idUsuarioEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}

$(document).ready(function() {
    $('#tablaUsuarios').DataTable(getDatatablesConfig());
});
</script>
