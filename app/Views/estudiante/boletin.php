<?php
/**
 * Vista: Estudiante - Boletín y Progreso de Calificaciones
 */
$titulo = 'Boletín de Calificaciones';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Boletín de Calificaciones</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consulte el registro de evaluaciones progresivas y su boletín oficial.</p>
    </div>
    <div>
        <?php if (!empty($boletinCompleto)): ?>
            <button onclick="window.print()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
                <i class="bi bi-printer"></i>
                <span>Imprimir Boletín Oficial</span>
            </button>
        <?php else: ?>
            <button disabled type="button" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 font-semibold rounded-xl text-sm cursor-not-allowed opacity-75" title="El boletín oficial sólo estará disponible cuando todas las materias tengan cargadas el 100% de las calificaciones">
                <i class="bi bi-lock-fill"></i>
                <span>Boletín Oficial (Incompleto)</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Student Data Banner -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Estudiante</span>
        <span class="font-bold text-slate-900 dark:text-white text-base block mt-0.5"><?= e(($estudiante['nombres'] ?? $estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellidos'] ?? $estudiante['apellido'] ?? '')) ?></span>
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

<!-- Estado de Disponibilidad del Boletín -->
<?php if (empty($boletinCompleto)): 
    $totMat = (int)($totalMaterias ?? 0);
    $matComp = (int)($materiasCompletas ?? 0);
    $porcentajeProgreso = $totMat > 0 ? round(($matComp / $totMat) * 100) : 0;
?>
    <div class="p-6 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-800 dark:text-amber-300 text-sm mb-6 shadow-sm">
        <div class="flex items-start space-x-3.5">
            <i class="bi bi-clock-history text-amber-500 text-2xl shrink-0 mt-0.5"></i>
            <div class="w-full">
                <h4 class="font-bold text-base text-amber-900 dark:text-amber-200">Boletín Oficial en Proceso de Carga</h4>
                <p class="mt-1 text-xs text-amber-800/90 dark:text-amber-300/90 leading-relaxed">
                    El boletín oficial definitivo solo se habilitará para impresión cuando todos los profesores hayan registrado el 100% de las calificaciones correspondientes a todas las asignaturas asignadas a su sección. Mientras tanto, a continuación puede consultar el avance de las calificaciones parciales registradas hasta la fecha.
                </p>
                <div class="mt-4 pt-3 border-t border-amber-200/80 dark:border-amber-900/50">
                    <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span>Progreso de Carga Docente: <?= $matComp ?> de <?= $totMat ?> asignaturas completadas al 100%</span>
                        <span class="font-mono"><?= $porcentajeProgreso ?>%</span>
                    </div>
                    <div class="w-full bg-amber-200 dark:bg-amber-900/60 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-600 h-2.5 rounded-full transition-all duration-500" style="width: <?= $porcentajeProgreso ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-sm mb-6 flex items-center space-x-3">
        <i class="bi bi-check-circle-fill text-emerald-500 text-xl shrink-0"></i>
        <div>
            <h4 class="font-bold text-sm">Boletín Oficial Completo y Publicado</h4>
            <p class="text-xs opacity-90">Se han registrado el 100% de las notas de todas las asignaturas. Ya puede imprimir su boletín definitivo.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Tabla de Asignaciones y Evaluaciones Progresivas -->
<?php if (empty($calificaciones)): ?>
    <div class="p-6 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 text-blue-700 dark:text-blue-300 text-sm text-center">
        <i class="bi bi-info-circle text-xl block mb-2"></i>
        <span>No se encontraron asignaturas o calificaciones cargadas para el período académico actual.</span>
    </div>
<?php else: ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50">
                        <th class="py-3 px-4">Asignatura</th>
                        <th class="py-3 px-4">Desglose de Evaluaciones Parciales</th>
                        <th class="py-3 px-4 text-center">Estado Carga</th>
                        <th class="py-3 px-4 text-center">Promedio Ponderado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($calificaciones as $cal): 
                        $prom = (float)($cal['promedio'] ?? 0);
                        $isApproved = $prom >= 10;
                        $materiaComp = !empty($cal['materia_completa']);
                        $countEval = (int)($cal['evaluaciones_count'] ?? 0);
                        $countEvaluadas = (int)($cal['evaluadas_count'] ?? 0);
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-900 dark:text-white align-top">
                            <?= e($cal['materia'] ?? $cal['materia_nombre'] ?? '') ?>
                        </td>
                        <td class="py-4 px-4 align-top">
                            <?php if (!empty($cal['evaluaciones'])): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <?php foreach ($cal['evaluaciones'] as $eval): 
                                        $evaluada = !empty($eval['evaluada']);
                                    ?>
                                        <div class="p-2 rounded-lg border flex items-center justify-between <?= $evaluada ? 'bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/40' : 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200/60 dark:border-amber-900/40' ?>">
                                            <span class="text-slate-700 dark:text-slate-300 font-medium"><?= e($eval['nombre']) ?></span>
                                            <?php if ($evaluada): ?>
                                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 me-1"><?= e($eval['nota']) ?> pts <span class="text-[10px] text-slate-400">(<?= e($eval['ponderacion']) ?>%)</span></span>
                                            <?php else: ?>
                                                <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 italic">Pendiente (<?= e($eval['ponderacion']) ?>%)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 italic">Sin plan de evaluación registrado</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-4 text-center align-top">
                            <?php if ($materiaComp): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Completa (100%)</span>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span><?= $countEvaluadas ?> / <?= $countEval ?> eval.</span>
                                </span>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

