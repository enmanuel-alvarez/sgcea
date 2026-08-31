<?php
/**
 * Vista: Dashboard Administrativo Rediseñado (Tailwind CSS v3 + Chart.js)
 */
$titulo = 'Dashboard Administrativo';
$nombreAdmin = $_SESSION['usuario_nombre'] ?? 'Administrador';
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
                <i class="bi bi-calendar-event me-1"></i> Año Lectivo: <?= htmlspecialchars($config['ano_academico_actual'] ?? '2026-2027') ?>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight">¡Bienvenido de nuevo, <?= htmlspecialchars($nombreAdmin) ?>! 👋</h1>
            <p class="text-sm text-blue-100/90 mt-2 max-w-xl leading-relaxed">
                Plataforma de Control Escolar-Académico SGCEA. Monitoree las métricas generales, asistencias y rendimiento en tiempo real.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= url('/admin/estudiantes/crear') ?>" class="px-5 py-3 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-2xl text-xs shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                <i class="bi bi-person-plus-fill text-base"></i>
                <span>Nuevo Estudiante</span>
            </a>
            <a href="<?= url('/admin/backup') ?>" class="px-5 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold rounded-2xl text-xs transition-all border border-white/20 flex items-center space-x-2">
                <i class="bi bi-database-gear text-base"></i>
                <span>Respaldo JSON</span>
            </a>
        </div>
    </div>
</div>

<!-- FILTER BAR FOR DASHBOARD DATA -->
<div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-sm mb-8 no-print">
    <form method="GET" action="<?= url('/admin') ?>" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3 text-slate-800 dark:text-slate-100 font-extrabold text-sm">
            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <i class="bi bi-funnel-fill text-lg"></i>
            </div>
            <div>
                <h3>Filtros de Métricas del Dashboard</h3>
                <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400">Filtre las estadísticas e indicadores en tiempo real por período y nivel académico</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div>
                <select name="periodo" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500">
                    <option value="">Año Lectivo: Todos</option>
                    <?php 
                    $anoConfig = $config['ano_academico_actual'] ?? '2026-2027';
                    $anoBase = (int)substr($anoConfig, 0, 4);
                    if ($anoBase < 2020) $anoBase = (int)date('Y');
                    for ($y = $anoBase + 1; $y >= $anoBase - 4; $y--): 
                        $labelOp = $y . '-' . ($y + 1);
                    ?>
                        <option value="<?= $y ?>" <?= (isset($periodoFiltro) && ($periodoFiltro == $y || $periodoFiltro == $labelOp)) ? 'selected' : '' ?>>Año Lectivo <?= $labelOp ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <select name="grado_id" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-blue-500">
                    <option value="">Grado / Nivel: Todos</option>
                    <?php if (!empty($grados)): ?>
                        <?php foreach ($grados as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= (isset($gradoFiltro) && $gradoFiltro == $g['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <?php if (!empty($periodoFiltro) || !empty($gradoFiltro)): ?>
                <a href="<?= url('/admin') ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all flex items-center space-x-1.5" title="Limpiar Filtros">
                    <i class="bi bi-x-circle-fill text-rose-500"></i>
                    <span>Limpiar</span>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- VIBRANT STAT CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Estudiantes -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-3xl p-6 shadow-lg shadow-blue-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-blue-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-people-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-200">Estudiantes</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-person-badge"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_estudiantes'] ?? 0) ?></h2>
        <p class="text-xs text-blue-100/90 flex items-center font-medium">
            <i class="bi bi-arrow-up-right me-1 font-bold"></i> Matrícula activa registrada
        </p>
    </div>

    <!-- Total Docentes -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-lg shadow-emerald-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-emerald-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-person-workspace text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-200">Docentes</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-award"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_docentes'] ?? 0) ?></h2>
        <p class="text-xs text-emerald-100/90 flex items-center font-medium">
            <i class="bi bi-check-circle-fill me-1"></i> Personal académico activo
        </p>
    </div>

    <!-- Total Materias -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white rounded-3xl p-6 shadow-lg shadow-indigo-600/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-indigo-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-book-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-200">Materias</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-journal-text"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_materias'] ?? 0) ?></h2>
        <p class="text-xs text-indigo-100/90 flex items-center font-medium">
            <i class="bi bi-layers-fill me-1"></i> Malla curricular vigente
        </p>
    </div>

    <!-- Secciones Activas -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg shadow-amber-500/20 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="absolute -right-4 -bottom-4 opacity-20 text-amber-200 group-hover:scale-110 transition-transform">
            <i class="bi bi-door-open-fill text-8xl"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-100">Secciones</span>
            <span class="p-2 rounded-xl bg-white/20 text-white text-sm"><i class="bi bi-building"></i></span>
        </div>
        <h2 class="text-4xl font-black mb-1"><?= number_format($estadisticas['total_secciones'] ?? 0) ?></h2>
        <p class="text-xs text-amber-100/90 flex items-center font-medium">
            <i class="bi bi-door-open me-1"></i> Aulas y ambientes activos
        </p>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    <!-- Chart 1: Asistencias y Rendimiento -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-700/50">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-graph-up-arrow text-blue-500"></i>
                <span>Balance Mensual de Asistencias</span>
            </h3>
            <span class="text-xs font-mono text-slate-400">Tendencia General</span>
        </div>
        <div class="h-64 relative">
            <canvas id="chartAsistenciaAdmin"></canvas>
        </div>
    </div>

    <!-- Chart 2: Estado de Solicitudes de Constancias -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-700/50">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-pie-chart-fill text-indigo-500"></i>
                <span>Distribución de Solicitudes de Constancias</span>
            </h3>
            <a href="<?= url('/admin/constancias') ?>" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">Gestionar &rarr;</a>
        </div>
        <div class="h-64 relative flex items-center justify-center">
            <canvas id="chartConstanciasAdmin"></canvas>
        </div>
    </div>

</div>

<!-- DATA & TIMELINE ROW -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Últimos Estudiantes Registrados -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-700/50">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-clock-history text-blue-500"></i>
                <span>Últimos Estudiantes Registrados</span>
            </h3>
            <a href="<?= url('/admin/estudiantes') ?>" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver Todos &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase font-bold text-slate-400 dark:text-slate-500">
                        <th class="py-2.5 px-3">Estudiante</th>
                        <th class="py-2.5 px-3">Grado</th>
                        <th class="py-2.5 px-3">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php if (!empty($estadisticas['ultimos_estudiantes'])): ?>
                        <?php foreach ($estadisticas['ultimos_estudiantes'] as $est): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 px-3 font-semibold text-slate-800 dark:text-slate-100 flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                        <?= strtoupper(substr($est['nombre'], 0, 1)) ?>
                                    </div>
                                    <span><?= htmlspecialchars($est['nombre'] . ' ' . $est['apellido']) ?></span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        <?= htmlspecialchars($est['grado_nombre'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-400 font-mono">
                                    <?= date('d/m/Y', strtotime($est['fecha_creacion'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="py-6 text-center text-slate-400">No hay registros recientes</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accesos Rápidos y Configuración -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-grid-fill text-indigo-500"></i>
                <span>Accesos Rápidos del Sistema</span>
            </h3>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <a href="<?= url('/reportes?tipo=cuadro_honor') ?>" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 hover:bg-blue-50 dark:hover:bg-slate-700/50 transition-all flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Cuadro de Honor</h4>
                    <span class="text-[10px] text-slate-400">Ranking notas</span>
                </div>
            </a>

            <a href="<?= url('/admin/backup') ?>" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 hover:bg-emerald-50 dark:hover:bg-slate-700/50 transition-all flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-database-gear"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Respaldo JSON</h4>
                    <span class="text-[10px] text-slate-400">Import/Export</span>
                </div>
            </a>

            <a href="<?= url('/admin/usuarios') ?>" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 hover:bg-indigo-50 dark:hover:bg-slate-700/50 transition-all flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Permisos ACL</h4>
                    <span class="text-[10px] text-slate-400">Usuarios & Roles</span>
                </div>
            </a>

            <a href="<?= url('/admin/auditoria') ?>" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 hover:bg-amber-50 dark:hover:bg-slate-700/50 transition-all flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-800 dark:text-slate-200">Bitácora Logs</h4>
                    <span class="text-[10px] text-slate-400">Eventos del sistema</span>
                </div>
            </a>
        </div>
    </div>

</div>

<!-- CHART INITIALIZERS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Chart 1: Asistencia Balance
    const ctx1 = document.getElementById('chartAsistenciaAdmin');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Presentes', 'Inasistencias'],
                datasets: [
                    {
                        data: [
                            <?= (int)($estadisticas['asistencias_presentes'] ?? 0) ?>,
                            <?= (int)($estadisticas['asistencias_ausentes'] ?? 0) ?>
                        ],
                        backgroundColor: ['#0284c7', '#f43f5e']
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Chart 2: Solicitudes de Constancias
    const ctx2 = document.getElementById('chartConstanciasAdmin');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Aprobadas / Emitidas', 'Rechazadas'],
                datasets: [{
                    data: [
                        <?= (int)($estadisticas['constancias_distribucion']['pendiente'] ?? 0) ?>,
                        <?= (int)($estadisticas['constancias_distribucion']['aprobada'] ?? 0) ?>,
                        <?= (int)($estadisticas['constancias_distribucion']['rechazada'] ?? 0) ?>
                    ],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444']
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


