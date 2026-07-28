<?php
/**
 * Vista: Crear/Editar Usuario (Admin)
 */
$editar = isset($usuario);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $editar ? 'Editar' : 'Nuevo' ?> Usuario</h1>
        <a href="/admin/usuarios" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="<?= $editar ? '/admin/usuarios/actualizar/' . $usuario['id'] : '/admin/usuarios/guardar' ?>">
                        <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cedula" class="form-label">Cédula *</label>
                                <input type="text" class="form-control" id="cedula" name="cedula" 
                                       value="<?= htmlspecialchars($usuario['cedula'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido *</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" 
                                       value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" 
                                       value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Rol *</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="estudiante" <?= ($usuario['rol'] ?? '') === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
                                    <option value="docente" <?= ($usuario['rol'] ?? '') === 'docente' ? 'selected' : '' ?>>Docente</option>
                                    <option value="admin" <?= ($usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </div>
                        </div>

                        <?php if (!$editar): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña (dejar vacío para no cambiar)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Solo llenar si desea cambiar la contraseña</small>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo" <?= ($usuario['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= ($usuario['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/usuarios" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?= $editar ? 'Actualizar' : 'Crear' ?> Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0"><i class="bi bi-info-circle"></i> Información</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Nota:</strong></p>
                    <ul class="small mb-0">
                        <li>El email debe ser único en el sistema.</li>
                        <li>La cédula también debe ser única.</li>
                        <li>Después de crear el usuario, puede asignarle permisos específicos desde la sección de permisos.</li>
                        <li>Los administradores tienen acceso completo por defecto.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    
    if (password && passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirm.setCustomValidity('');
            }
        });
    }
});
</script>
