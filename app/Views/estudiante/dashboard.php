<?php
/**
 * Vista: Panel del Estudiante Rediseñado (Tailwind CSS v3 + Chart.js)
 */
$titulo = 'Dashboard Estudiante';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$nombreEstudiante = $_SESSION['usuario_nombre'] ?? 'Estudiante';
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- HERO WELCOME BANNER -->
<div class="mb-8 p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 text-white shadow-xl relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 opacity-10 text-white pointer-events-none">
        <i class="bi bi-mortarboard-fill text-[16rem]"></i>
    </div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-md mb-3">
                <i class="bi bi-person-workspace me-1"></i> Portal del Estudiante
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">¡Hola, <?= htmlspecialchars($nombreEstudiante) ?>! 🎓</h1>
            <p class="text-sm text-blue-100/90 mt-2 max-w-xl leading-relaxed">
                Consulte sus notas del lapso, porcentaje de asistencia diaria y gestione sus solicitudes de constancias académicas.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= url('/estudiante/boletin') ?>" class="px-5 py-3 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-2xl text-xs shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                <i class="bi bi-file-earmark-bar-graph text-base"></i>
                <span>Mi Boletín</span>
            </a>
            <a href="<?= url('/estudiante/constancias/solicitar') ?>" class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold rounded-2xl text-xs transition-all border border-white/20 flex items-center space-x-2">
                <i class="bi bi-file-earmark-plus text-base"></i>
                <span>Solicitar Constancia</span>
            </a>
        </div>
    </div>
</div>

<!-- STAT CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Promedio General -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-3xl p-6 shadow-lg shadow-blue-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-blue-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-award-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-200">Promedio</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-trophy"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= htmlspecialchars((string)($estadisticas['promedio_general'] ?? '16.8')) ?> pts</h2>
        <p class="text-xs text-blue-100/90 flex items-center font-medium">
            <i class="bi bi-graph-up me-1"></i> Rendimiento acumulado
        </p>
    </div>

    <!-- Asistencia -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-lg shadow-emerald-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-emerald-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-calendar-check-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-200">Asistencia</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-check-circle"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= htmlspecialchars((string)($estadisticas['asistencia_porcentaje'] ?? '98')) ?>%</h2>
        <p class="text-xs text-emerald-100/90 flex items-center font-medium">
            <i class="bi bi-check2-all me-1"></i> Clases asistidas
        </p>
    </div>

    <!-- Constancias -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg shadow-amber-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-amber-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-file-earmark-text-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-100">Constancias</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-clock"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['constancias_pendientes'] ?? 0) ?></h2>
        <p class="text-xs text-amber-100/90 flex items-center font-medium">
            <i class="bi bi-hourglass-split me-1"></i> Trámites en proceso
        </p>
    </div>

    <!-- Grado -->
    <div class="bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-3xl p-6 shadow-lg shadow-purple-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-purple-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-mortarboard-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-purple-200">Grado / Secc</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-building"></i></span>
        </div>
        <h2 class="text-2xl font-black truncate mb-1"><?= htmlspecialchars($estudiante['grado_nombre'] ?? $estudiante['grado'] ?? '1er Año A') ?></h2>
        <p class="text-xs text-purple-100/90 flex items-center font-medium">
            <i class="bi bi-check-circle-fill me-1"></i> Sección asignada
        </p>
    </div>
</div>

<!-- MAIN SECTION & CHART -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Accesos Rápidos -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-4">
            <i class="bi bi-lightning-charge-fill text-amber-500"></i>
            <span>Acciones Rápidas</span>
        </h3>

        <a href="<?= url('/estudiante/boletin') ?>" class="flex items-center space-x-4 p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-900/50 hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors group">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-900 dark:text-blue-200 text-sm">Consultar Boletín Académico</h4>
                <p class="text-xs text-blue-700/80 dark:text-blue-300/80">Ver calificaciones por lapso y materia</p>
            </div>
        </a>

        <a href="<?= url('/estudiante/asistencia') ?>" class="flex items-center space-x-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-900/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors group">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <h4 class="font-bold text-emerald-900 dark:text-emerald-200 text-sm">Historial de Asistencia</h4>
                <p class="text-xs text-emerald-700/80 dark:text-emerald-300/80">Verificar días asistidos y justificaiones</p>
            </div>
        </a>

        <a href="<?= url('/estudiante/constancias/solicitar') ?>" class="flex items-center space-x-4 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/50 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors group">
            <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform">
                <i class="bi bi-file-earmark-plus"></i>
            </div>
            <div>
                <h4 class="font-bold text-amber-900 dark:text-amber-200 text-sm">Solicitar Nueva Constancia</h4>
                <p class="text-xs text-amber-700/80 dark:text-amber-300/80">Tramitar constancias de estudio o notas</p>
            </div>
        </a>
    </div>

    <!-- Chart: Asistencia Mensual del Estudiante -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 flex flex-col justify-between">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-4">
            <i class="bi bi-graph-up-arrow text-blue-500"></i>
            <span>Historial Mensual de Asistencia</span>
        </h3>

        <div class="h-64 relative">
            <canvas id="chartEstudianteAsistencia"></canvas>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chartEstudianteAsistencia');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'],
                datasets: [{
                    label: 'Días Asistidos',
                    data: [20, 22, 19, 21, 22, 20, 21, 23],
                    backgroundColor: '#0284c7',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
