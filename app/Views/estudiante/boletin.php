<?php
/**
 * Vista: Estudiante - Boletín de Calificaciones (Tailwind CSS v3)
 */
$titulo = 'Boletín de Calificaciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Boletín de Calificaciones</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Informe académico consolidado de asignaturas y evaluaciones.</p>
    </div>
    <?php if (!empty($calificaciones)): ?>
        <button onclick="window.print()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-printer"></i>
            <span>Imprimir Boletín</span>
        </button>
    <?php endif; ?>
</div>

<!-- Student Data Banner -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Estudiante</span>
        <span class="font-bold text-slate-900 dark:text-white text-base block mt-0.5"><?= e($estudiante['nombres'] ?? $estudiante['nombre'] ?? '') ?> <?= e($estudiante['apellidos'] ?? $estudiante['apellido'] ?? '') ?></span>
    </div>
    <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Cédula de Identidad</span>
        <span class="font-mono text-slate-700 dark:text-slate-300 text-sm block mt-0.5"><?= e($estudiante['cedula'] ?? '') ?></span>
    </div>
    <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Grado Académico</span>
        <span class="font-semibold text-slate-800 dark:text-slate-200 text-sm block mt-0.5"><?= e($inscripcion['grado_nombre'] ?? $inscripcion['grado'] ?? 'N/A') ?></span>
    </div>
    <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Sección</span>
        <span class="font-semibold text-slate-800 dark:text-slate-200 text-sm block mt-0.5"><?= e($inscripcion['seccion_nombre'] ?? $inscripcion['seccion'] ?? 'N/A') ?></span>
    </div>
</div>

<?php if (empty($calificaciones)): ?>
    <div class="p-6 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 text-blue-700 dark:text-blue-300 text-sm text-center">
        <i class="bi bi-info-circle text-xl block mb-2"></i>
        <span>No se encontraron calificaciones registradas para el período actual.</span>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50">
                        <th class="py-3 px-4">Asignatura</th>
                        <th class="py-3 px-4">Desglose de Evaluaciones</th>
                        <th class="py-3 px-4 text-center">Promedio Final</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($calificaciones as $cal): 
                        $prom = $cal['promedio'] ?? 0;
                        $isApproved = $prom >= 10;
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-900 dark:text-white align-top"><?= e($cal['materia'] ?? $cal['materia_nombre'] ?? '') ?></td>
                        <td class="py-4 px-4 align-top">
                            <?php if (!empty($cal['evaluaciones'])): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <?php foreach ($cal['evaluaciones'] as $eval): ?>
                                        <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/40 flex items-center justify-between">
                                            <span class="text-slate-700 dark:text-slate-300 font-medium"><?= e($eval['nombre']) ?></span>
                                            <span class="font-mono font-semibold text-blue-600 dark:text-blue-400 me-1"><?= e($eval['nota']) ?> pts (<?= e($eval['ponderacion']) ?>%)</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 italic">Sin evaluaciones registradas</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-4 text-center align-top">
                            <span class="inline-block px-3 py-1 rounded-full font-mono font-extrabold text-sm <?= $isApproved ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' ?>">
                                <?= number_format($prom, 2) ?> pts
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
