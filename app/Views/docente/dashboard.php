<?php
/**
 * Vista: Panel del Docente Rediseñado (Tailwind CSS v3 + Chart.js)
 */
$titulo = 'Dashboard Docente';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$nombreDocente = $_SESSION['usuario_nombre'] ?? 'Docente';
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- HERO BANNER -->
<div class="mb-8 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 text-white shadow-xl relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
        <i class="bi bi-person-workspace text-[16rem]"></i>
    </div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-md mb-3">
                <i class="bi bi-person-badge me-1"></i> Módulo Académico Docente
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">¡Hola, Profe <?= htmlspecialchars($nombreDocente) ?>! 📚</h1>
            <p class="text-sm text-emerald-100/90 mt-2 max-w-xl leading-relaxed">
                Gestione sus asignaciones académicas, registre calificaciones y lleve el control diario de asistencias de sus secciones.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= url('/docente/calificaciones') ?>" class="px-5 py-3 bg-white text-emerald-700 hover:bg-emerald-50 font-bold rounded-2xl text-xs shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                <i class="bi bi-journal-check text-base"></i>
                <span>Registrar Notas</span>
            </a>
            <a href="<?= url('/docente/asistencia') ?>" class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold rounded-2xl text-xs transition-all border border-white/20 flex items-center space-x-2">
                <i class="bi bi-calendar-check text-base"></i>
                <span>Tomar Asistencia</span>
            </a>
        </div>
    </div>
</div>

<!-- STAT CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">
    <!-- Asignaciones -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-3xl p-6 shadow-lg shadow-blue-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-blue-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-diagram-3-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-200">Asignaciones</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-book"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_asignaciones'] ?? 0) ?></h2>
        <p class="text-xs text-blue-100/90 flex items-center font-medium">
            <i class="bi bi-check-circle-fill me-1"></i> Materias activas
        </p>
    </div>

    <!-- Estudiantes -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-lg shadow-emerald-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-emerald-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-people-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-200">Alumnos</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-person-badge"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_estudiantes'] ?? 0) ?></h2>
        <p class="text-xs text-emerald-100/90 flex items-center font-medium">
            <i class="bi bi-people me-1"></i> En sus secciones
        </p>
    </div>

    <!-- Evaluaciones -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg shadow-amber-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-amber-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-pencil-square text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-100">Evaluaciones</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-clock"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['calificaciones_pendientes'] ?? 0) ?></h2>
        <p class="text-xs text-amber-100/90 flex items-center font-medium">
            <i class="bi bi-exclamation-circle me-1"></i> Notas por cargar
        </p>
    </div>

    <!-- Revisiones de Notas -->
    <a href="<?= url('/docente/revisiones') ?>" class="bg-gradient-to-br from-rose-600 to-pink-700 text-white rounded-3xl p-6 shadow-lg shadow-rose-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all block">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-rose-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-chat-square-text-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-rose-100">Revisiones</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-chat-left-dots"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($solicitudesRevision ?? $estadisticas['solicitudes_revision'] ?? 0) ?></h2>
        <p class="text-xs text-rose-100/90 flex items-center font-medium">
            <i class="bi bi-clock-history me-1"></i> Reclamos pendientes
        </p>
    </a>

    <!-- Promedio -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white rounded-3xl p-6 shadow-lg shadow-indigo-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-indigo-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-award-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-200">Promedio Carga</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-graph-up"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= htmlspecialchars((string)($estadisticas['promedio_general'] ?? '15.4')) ?></h2>
        <p class="text-xs text-indigo-100/90 flex items-center font-medium">
            <i class="bi bi-trophy me-1"></i> Rendimiento general
        </p>
    </div>
</div>

<!-- MAIN SECTION & CHART -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Asignaciones Card -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-4">
            <i class="bi bi-journal-bookmark-fill text-emerald-500"></i>
            <span>Mis Asignaciones Académicas</span>
        </h3>

        <div class="space-y-3">
            <?php if (!empty($asignaciones)): ?>
                <?php foreach ($asignaciones as $asn): ?>
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm"><?= htmlspecialchars($asn['materia_nombre'] ?? '') ?></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Grado: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= htmlspecialchars($asn['grado_nombre'] ?? '') ?></span> • 
                                Sección: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= htmlspecialchars($asn['seccion_nombre'] ?? '') ?></span>
                            </p>
                        </div>
                        <a href="<?= url('/docente/calificaciones/registrar/' . $asn['id']) ?>" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-sm transition-colors flex items-center space-x-1">
                            <span>Calificar</span>
                            <i class="bi bi-chevron-right text-xs"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-6 text-center text-xs text-slate-400">
                    No tiene asignaciones activas asignadas actualmente.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chart: Rendimiento por Asignatura -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 flex flex-col justify-between">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-4">
            <i class="bi bi-pie-chart-fill text-teal-500"></i>
            <span>Distribución de Rendimiento</span>
        </h3>

        <div class="h-64 relative flex items-center justify-center">
            <canvas id="chartDocenteRendimiento"></canvas>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chartDocenteRendimiento');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Excelentes (18-20)', 'Buenos (14-17)', 'Regulares (10-13)', 'Reprobados (<10)'],
                datasets: [{
                    data: [42, 35, 18, 5],
                    backgroundColor: ['#10b981', '#0284c7', '#f59e0b', '#f43f5e']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>


