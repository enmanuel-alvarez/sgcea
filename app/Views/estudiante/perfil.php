<?php
/**
 * Vista: Estudiante - Mi Perfil (Tailwind CSS v3)
 */
$titulo = 'Mi Perfil';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Mi Perfil</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Información de usuario y actualización de datos de contacto.</p>
    </div>
    <div>
        <a href="<?= url('/estudiante/dashboard') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- User Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 text-center">
        <div class="w-24 h-24 mx-auto rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-3xl mb-4 border-2 border-blue-500/20">
            <i class="bi bi-person"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white"><?= e($estudiante['nombres'] ?? $estudiante['nombre'] ?? '') ?> <?= e($estudiante['apellidos'] ?? $estudiante['apellido'] ?? '') ?></h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">Cédula: <?= e($estudiante['cedula'] ?? '') ?></p>
        <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-300 border border-blue-200/50">
            Estudiante Activo
        </span>
    </div>

    <!-- Edit Form Card -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-6">
            <i class="bi bi-sliders text-blue-500"></i>
            <span>Actualizar Datos de Contacto</span>
        </h3>

        <form action="<?= url('/estudiante/perfil/actualizar') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="telefono" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Teléfono Personal</label>
                    <input type="text" id="telefono" name="telefono" value="<?= e($estudiante['telefono'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="telefono_representante" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Teléfono del Representante</label>
                    <input type="text" id="telefono_representante" name="telefono_representante" value="<?= e($estudiante['telefono_representante'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div>
                <label for="direccion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Dirección de Residencia</label>
                <textarea id="direccion" name="direccion" rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"><?= e($estudiante['direccion'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="<?= url('/estudiante/dashboard') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
