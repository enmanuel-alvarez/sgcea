<?php
/**
 * Vista: Estudiante - Historial de Asistencia (Tailwind CSS v3)
 */
$titulo = 'Historial de Asistencia';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Historial de Asistencia</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Registro de concurrencia y faltas en clases por asignatura.</p>
    </div>
</div>

<?php if (empty($asistencias)): ?>
    <div class="p-6 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 text-blue-700 dark:text-blue-300 text-sm text-center">
        <i class="bi bi-info-circle text-xl block mb-2"></i>
        <span>No se han encontrado registros de asistencia en su perfil.</span>
    </div>
<?php else: ?>
    <?php 
        $total = count($asistencias);
        $presentes = 0; $ausentes = 0; $tardanzas = 0;
        foreach ($asistencias as $a) {
            $est = strtolower($a['estado']);
            if ($est === 'presente') $presentes++;
            elseif ($est === 'ausente') $ausentes++;
            elseif ($est === 'tarde' || $est === 'tardanza') $tardanzas++;
        }
        $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 0;
    ?>
    
    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Total Clases</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white block mt-1"><?= $total ?></span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Presentes</span>
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 block mt-1"><?= $presentes ?></span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400 block">Ausentes</span>
            <span class="text-2xl font-black text-rose-600 dark:text-rose-400 block mt-1"><?= $ausentes ?></span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Tardanzas</span>
            <span class="text-2xl font-black text-amber-600 dark:text-amber-400 block mt-1"><?= $tardanzas ?></span>
        </div>
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm col-span-2 sm:col-span-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">% Asistencia</span>
            <span class="text-2xl font-black text-blue-600 dark:text-blue-400 block mt-1"><?= $porcentaje ?>%</span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="dataTable" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($asistencias as $a): 
                        $est = strtolower($a['estado']);
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= e($a['fecha']) ?></td>
                        <td class="font-semibold text-slate-900 dark:text-white"><?= e($a['materia'] ?? $a['materia_nombre'] ?? '') ?></td>
                        <td>
                            <?php if ($est === 'presente'): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Presente</span>
                            <?php elseif ($est === 'ausente'): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">Ausente</span>
                            <?php elseif ($est === 'tarde' || $est === 'tardanza'): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Tardanza</span>
                            <?php else: ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400"><?= e(ucfirst($a['estado'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 text-xs"><?= e($a['observaciones'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']]
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>


