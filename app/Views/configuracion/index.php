<?php
/**
 * Vista: Configuración General del Sistema (Tailwind CSS v3)
 */
$titulo = 'Configuración del Sistema';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Configuración General del Sistema</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ajustes institucionales, escala de calificaciones y parámetros globales.</p>
    </div>
</div>

<form method="POST" action="<?= url('/admin/configuracion/guardar') ?>" class="space-y-8 max-w-5xl mx-auto">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

    <!-- Datos Institucionales -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <i class="bi bi-building text-blue-500"></i>
            <span>Identidad Institucional</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="nombre_sistema" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nombre del Sistema *</label>
                <input type="text" id="nombre_sistema" name="nombre_sistema" value="<?= htmlspecialchars($config['nombre_sistema'] ?? 'SGCEA') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="nombre_institucion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nombre de la Institución *</label>
                <input type="text" id="nombre_institucion" name="nombre_institucion" value="<?= htmlspecialchars($config['nombre_institucion'] ?? 'Institución Educativa Demo') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
        </div>
    </div>

    <!-- Escala y Parámetros Académicos -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <i class="bi bi-sliders text-indigo-500"></i>
            <span>Parámetros Académicos</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label for="ano_academico_actual" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Año Académico Vigente *</label>
                <input type="text" id="ano_academico_actual" name="ano_academico_actual" value="<?= htmlspecialchars($config['ano_academico_actual'] ?? '2024-2025') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="nota_minima_aprobacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nota Mínima Aprobatoria *</label>
                <input type="number" id="nota_minima_aprobacion" name="nota_minima_aprobacion" value="<?= htmlspecialchars($config['nota_minima_aprobacion'] ?? 10) ?>" min="0" max="20" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="max_solicitudes_constancia" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Máx Solicitudes Constancias *</label>
                <input type="number" id="max_solicitudes_constancia" name="max_solicitudes_constancia" value="<?= htmlspecialchars($config['max_solicitudes_constancia'] ?? 5) ?>" min="1" max="10" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 pt-2">
        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Guardar Configuración</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
