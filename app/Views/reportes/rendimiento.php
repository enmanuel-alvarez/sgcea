<?php
/**
 * Vista: Reporte de Rendimiento Académico (Tailwind CSS v3)
 */
$titulo = 'Reporte de Rendimiento';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= e($titulo ?? 'Reporte de Rendimiento') ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Indicadores globales de promedio, tasa de aprobación y reprobación.</p>
    </div>
    <div class="flex items-center space-x-2">
        <a href="<?= url('/reportes') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a Reportes</span>
        </a>
        <button onclick="window.print()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-1.5">
            <i class="bi bi-printer-fill"></i>
            <span>Imprimir PDF</span>
        </button>
    </div>
</div>

<!-- Stat Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8 no-print">
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
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 mb-8 no-print">
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
<div id="reporteContainer" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 overflow-hidden printable-report">

    <!-- ENCABEZADO / MEMBRETE OFICIAL DE LA INSTITUCIÓN -->
    <div class="membrete-oficial border-b-2 border-slate-900 dark:border-slate-100 pb-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 flex items-center justify-center text-3xl font-extrabold shadow-md shrink-0">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</p>
                <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mt-0.5">
                    <?= htmlspecialchars($config['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA') ?>
                </h2>
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                    Reporte de Rendimiento Académico Oficial • Año Lectivo <?= date('Y') ?>
                </p>
            </div>
        </div>
        <div class="text-left sm:text-right text-[11px] font-mono text-slate-600 dark:text-slate-300 shrink-0">
            <p><i class="bi bi-clock me-1"></i>Emisión: <?= date('d/m/Y h:i A') ?></p>
            <p class="text-blue-600 dark:text-blue-400 font-bold">CÓDIGO: SGCEA-REND-<?= date('Ymd') ?></p>
        </div>
    </div>
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

    <!-- SECCIÓN DE FIRMAS Y SELLOS INSTITUCIONALES PARA IMPRESIÓN -->
    <div class="firmas-impresion pt-8 border-t border-slate-200 dark:border-slate-700 hidden print:flex justify-between items-center text-center text-xs">
        <div class="firma-box w-2/5">
            <div class="h-12"></div>
            <div class="firma-linea border-t border-slate-900 dark:border-slate-100 pt-1">
                <p class="font-bold text-slate-900 dark:text-white uppercase">Director / Subdirector Académico</p>
                <p class="text-[10px] text-slate-500">Firma y Sello Oficial del Plantel</p>
            </div>
        </div>
        <div class="firma-box w-2/5">
            <div class="h-12"></div>
            <div class="firma-linea border-t border-slate-900 dark:border-slate-100 pt-1">
                <p class="font-bold text-slate-900 dark:text-white uppercase">Coordinación de Control de Estudios</p>
                <p class="text-[10px] text-slate-500">Verificación y Validación Institucional</p>
            </div>
        </div>
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


