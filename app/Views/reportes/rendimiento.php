<?php
/**
 * Vista: Reporte de Rendimiento Académico (Tailwind CSS v3)
 */
$titulo = 'Reporte de Rendimiento';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= e($titulo ?? 'Reporte de Rendimiento') ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Indicadores globales de promedio, tasa de aprobación y reprobación.</p>
    </div>
    <div>
        <a href="<?= url('/reportes') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a Reportes</span>
        </a>
    </div>
</div>

<!-- Stat Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-lg shadow-blue-600/20 relative overflow-hidden">
        <p class="text-xs font-bold uppercase tracking-wider text-blue-200 mb-1">Promedio General</p>
        <h2 class="text-3xl font-black" id="stat-promedio">0.00 pts</h2>
        <p class="text-xs text-blue-100/80 mt-2 flex items-center">
            <i class="bi bi-calculator me-1"></i> Período evaluado
        </p>
    </div>

    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-6 shadow-lg shadow-emerald-600/20 relative overflow-hidden">
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-200 mb-1">% Aprobados</p>
        <h2 class="text-3xl font-black" id="stat-aprobados">0%</h2>
        <p class="text-xs text-emerald-100/80 mt-2 flex items-center">
            <i class="bi bi-check-circle-fill me-1"></i> Nota >= 10 pts
        </p>
    </div>

    <div class="bg-gradient-to-br from-rose-600 to-pink-700 text-white rounded-2xl p-6 shadow-lg shadow-rose-600/20 relative overflow-hidden">
        <p class="text-xs font-bold uppercase tracking-wider text-rose-200 mb-1">% Reprobados</p>
        <h2 class="text-3xl font-black" id="stat-reprobados">0%</h2>
        <p class="text-xs text-rose-100/80 mt-2 flex items-center">
            <i class="bi bi-x-circle-fill me-1"></i> Nota < 10 pts
        </p>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 mb-8">
    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700/50 pb-3 flex items-center space-x-2">
        <i class="bi bi-funnel text-blue-500"></i>
        <span>Filtros de Búsqueda</span>
    </h3>

    <form id="filtro-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div>
            <label for="grado_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Grado</label>
            <select id="grado_id" name="grado_id"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Todos los Grados</option>
            </select>
        </div>

        <div>
            <label for="seccion_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Sección</label>
            <select id="seccion_id" name="seccion_id"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Todas las Secciones</option>
            </select>
        </div>

        <div>
            <label for="materia_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Materia</label>
            <select id="materia_id" name="materia_id"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Todas las Materias</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="button" id="btn-buscar"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="bi bi-search"></i>
                <span>Generar Informe</span>
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Promedio</th>
                    <th>Estado Académico</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#dataTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
