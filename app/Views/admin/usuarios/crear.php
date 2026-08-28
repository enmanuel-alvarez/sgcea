<?php
/**
 * Vista: Crear/Editar Usuario (Admin - Tailwind CSS v3)
 */
$editar = isset($usuario);
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= $editar ? 'Editar' : 'Nuevo' ?> Usuario</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Complete los datos de la cuenta de usuario del sistema.</p>
    </div>
    <div>
        <a href="<?= url('/admin/usuarios') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Card -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
        <form method="POST" action="<?= $editar ? url('/admin/usuarios/actualizar/' . $usuario['id']) : url('/admin/usuarios/guardar') ?>" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="cedula" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Cédula *</label>
                    <input type="text" id="cedula" name="cedula" value="<?= htmlspecialchars($usuario['cedula'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="apellido" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="telefono" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="tipo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Rol / Tipo *</label>
                    <select id="tipo" name="tipo" required
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="estudiante" <?= ($usuario['tipo'] ?? $usuario['rol'] ?? '') === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
                        <option value="docente" <?= ($usuario['tipo'] ?? $usuario['rol'] ?? '') === 'docente' ? 'selected' : '' ?>>Docente</option>
                        <option value="admin" <?= ($usuario['tipo'] ?? $usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
            </div>

            <?php if (!$editar): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Contraseña *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <p class="text-xs text-slate-500 mt-1">Mínimo 6 caracteres</p>
                </div>

                <div>
                    <label for="password_confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Confirmar Contraseña *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>
            <?php else: ?>
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nueva Contraseña (dejar en blanco para conservar la actual)</label>
                <input type="password" id="password" name="password" minlength="6"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
            <?php endif; ?>

            <div>
                <label for="estado" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Estado de la cuenta</label>
                <select id="estado" name="estado"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="1" <?= ($usuario['estado'] ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= ($usuario['estado'] ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="<?= url('/admin/usuarios') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-check-lg"></i>
                    <span><?= $editar ? 'Actualizar Usuario' : 'Crear Usuario' ?></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Info Panel -->
    <div class="space-y-6">
        <div class="bg-blue-50/50 dark:bg-slate-800/60 border border-blue-200/60 dark:border-slate-700/60 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-blue-900 dark:text-blue-300 flex items-center space-x-2 mb-3">
                <i class="bi bi-info-circle-fill"></i>
                <span>Indicaciones Importantes</span>
            </h3>
            <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
                <li class="flex items-start space-x-2">
                    <i class="bi bi-dot text-lg leading-none"></i>
                    <span>El correo institucional y la cédula deben ser únicos en la base de datos.</span>
                </li>
                <li class="flex items-start space-x-2">
                    <i class="bi bi-dot text-lg leading-none"></i>
                    <span>Al crear un usuario se cargan los permisos predeterminados del rol asignado.</span>
                </li>
                <li class="flex items-start space-x-2">
                    <i class="bi bi-dot text-lg leading-none"></i>
                    <span>Los administradores cuentan con acceso completo a todos los módulos por defecto.</span>
                </li>
            </ul>
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
