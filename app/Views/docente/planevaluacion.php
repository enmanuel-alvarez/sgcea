<?php
/**
 * Vista: Docente - Plan de Evaluación (Tailwind CSS v3)
 */
$titulo = 'Plan de Evaluación';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= e($titulo ?? 'Plan de Evaluación') ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configuración de actividades evaluativas y ponderaciones (Total 100%).</p>
    </div>
    <div>
        <a href="<?= url('/docente/calificaciones') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<!-- Asignacion Info Card -->
<div class="bg-blue-50/50 dark:bg-slate-800/60 border border-blue-200/60 dark:border-slate-700/60 rounded-2xl p-5 mb-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
            <i class="bi bi-journal-bookmark"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-900 dark:text-white"><?= e($asignacion['materia'] ?? $asignacion['materia_nombre'] ?? '') ?></h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Grado: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= e($asignacion['grado'] ?? $asignacion['grado_nombre'] ?? '') ?></span> • 
                Sección: <span class="font-semibold text-slate-700 dark:text-slate-300"><?= e($asignacion['seccion'] ?? $asignacion['seccion_nombre'] ?? '') ?></span>
            </p>
        </div>
    </div>
    <div class="text-right">
        <span class="text-xs text-slate-500 dark:text-slate-400 block font-semibold uppercase tracking-wider">Acumulado</span>
        <span class="text-2xl font-black <?= ($totalPonderacion ?? 0) == 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' ?>">
            <?= (int)($totalPonderacion ?? 0) ?>% / 100%
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- List of Evaluations -->
    <div class="lg:col-span-2 space-y-4">
        <?php if (($totalPonderacion ?? 0) != 100): ?>
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-700 dark:text-amber-300 text-sm flex items-center space-x-2">
                <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
                <span>La suma de las ponderaciones de las evaluaciones debe ser exactamente 100%.</span>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700/50 pb-3">Actividades Programadas</h3>
            
            <?php if (empty($planes)): ?>
                <p class="text-slate-400 text-sm text-center py-6">No hay evaluaciones registradas en este plan.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400">
                                <th class="py-3 px-3">Nombre</th>
                                <th class="py-3 px-3">Tipo</th>
                                <th class="py-3 px-3">Ponderación</th>
                                <th class="py-3 px-3">Fecha</th>
                                <th class="py-3 px-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php foreach ($planes as $p): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white"><?= e($p['nombre']) ?></td>
                                <td class="py-3 px-3 text-slate-600 dark:text-slate-300 capitalize"><?= e($p['tipo']) ?></td>
                                <td class="py-3 px-3 font-mono font-bold text-blue-600 dark:text-blue-400"><?= e($p['ponderacion']) ?>%</td>
                                <td class="py-3 px-3 text-slate-500 dark:text-slate-400 text-xs font-mono"><?= e($p['fecha_programada']) ?></td>
                                <td class="py-3 px-3">
                                    <?php if (($p['estado'] ?? '') == 'realizada'): ?>
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Realizada</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Evaluation Form -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-700/50 pb-3">Nueva Evaluación</h3>

        <form action="<?= url('/docente/planevaluacion/guardar') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="asignacion_id" value="<?= e($asignacion['id'] ?? '') ?>">

            <div>
                <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Nombre de Evaluación *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej: Examen Parcial I" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="tipo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Tipo de Actividad *</label>
                <select id="tipo" name="tipo" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione</option>
                    <option value="examen">Examen Escrito</option>
                    <option value="tarea">Tarea / Taller</option>
                    <option value="proyecto">Proyecto / Exposición</option>
                    <option value="participacion">Participación en Clase</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div>
                <label for="ponderacion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Ponderación (%) *</label>
                <input type="number" id="ponderacion" name="ponderacion" min="1" max="100" placeholder="1 - 100" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div>
                <label for="fecha_programada" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Fecha Programada *</label>
                <input type="date" id="fecha_programada" name="fecha_programada" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <button type="submit" <?= ($totalPonderacion ?? 0) >= 100 ? 'disabled' : '' ?>
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-300 dark:disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="bi bi-plus-circle"></i>
                <span>Agregar al Plan</span>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
