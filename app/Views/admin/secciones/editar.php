<?php
/**
 * Vista: Admin - Editar Sección (Tailwind CSS v3)
 */
$titulo = 'Editar Sección';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Editar Sección</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Actualización de capacidad y parámetros de la sección.</p>
    </div>
    <div>
        <a href="<?= url('/admin/secciones') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<?php if (isset($errores)): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('/admin/secciones/actualizar/' . $seccion['id']) ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" value="<?= $seccion['id'] ?>">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <i class="bi bi-door-open text-blue-500"></i>
            <span>Datos de la Sección</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nombre / Letra *</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($seccion['nombre'] ?? '') ?>" required maxlength="50"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="cupo_maximo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Cupo Máximo</label>
                <input type="number" id="cupo_maximo" name="cupo_maximo" min="1" max="100" value="<?= htmlspecialchars($seccion['cupo_maximo'] ?? $seccion['capacidad_maxima'] ?? 35) ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="estado" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Estado</label>
                <select id="estado" name="estado"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="1" <?= ($seccion['estado'] ?? $seccion['activo'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= ($seccion['estado'] ?? $seccion['activo'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
        </div>

        <div>
            <label for="descripcion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" maxlength="200"
                      class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"><?= htmlspecialchars($seccion['descripcion'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 pt-4">
        <a href="<?= url('/admin/secciones') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Actualizar Sección</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
