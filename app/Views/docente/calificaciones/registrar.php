<?php
/**
 * Vista: Docente - Registrar Calificaciones (Tailwind CSS v3)
 */
$titulo = 'Registrar Calificaciones';
require_once __DIR__ . '/../../../layouts/header.php';
require_once __DIR__ . '/../../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Carga de Calificaciones</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Asignación: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($asignacion['materia_nombre'] ?? '') ?></strong> • 
            Sección: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars(($asignacion['grado_nombre'] ?? '') . ' - ' . ($asignacion['seccion_nombre'] ?? '')) ?></strong>
        </p>
    </div>
    <div>
        <a href="<?= url('/docente/calificaciones') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<form method="POST" action="<?= url('/docente/calificaciones/guardar') ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id_asignacion" value="<?= $asignacion['id'] ?>">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        <th class="py-3 px-4 font-bold text-xs uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <?php foreach ($evaluaciones as $ev): ?>
                            <th class="py-3 px-4 text-center font-bold text-xs uppercase text-slate-500 dark:text-slate-400 min-w-[110px]">
                                <?= htmlspecialchars($ev['nombre'] ?? $ev['nombre_actividad'] ?? '') ?>
                                <span class="block text-[10px] text-blue-600 dark:text-blue-400 font-mono font-normal">(<?= $ev['ponderacion'] ?? $ev['porcentaje'] ?? 0 ?>%)</span>
                            </th>
                        <?php endforeach; ?>
                        <th class="py-3 px-4 text-center font-bold text-xs uppercase text-blue-600 dark:text-blue-400 min-w-[90px]">Promedio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($estudiantes as $est): 
                        $estId = $est['id'] ?? $est['id_estudiante'];
                        $notasEst = array_filter($notas, fn($n) => ($n['id_estudiante'] ?? $n['estudiante_id']) == $estId);
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            <?= htmlspecialchars(($est['nombres'] ?? $est['nombre'] ?? '') . ' ' . ($est['apellidos'] ?? $est['apellido'] ?? '')) ?>
                        </td>
                        <?php foreach ($evaluaciones as $ev): 
                            $evId = $ev['id'] ?? $ev['id_plan'];
                            $notaExist = current(array_filter($notasEst, fn($n) => ($n['id_plan'] ?? $n['plan_evaluacion_id']) == $evId));
                            $valor = $notaExist ? ($notaExist['nota'] ?? $notaExist['valor_obtenido'] ?? '') : '';
                        ?>
                        <td class="py-2 px-2 text-center">
                            <input type="number" name="notas[<?= $estId ?>][<?= $evId ?>]" value="<?= htmlspecialchars($valor) ?>"
                                   min="0" max="20" step="0.01" placeholder="0 - 20"
                                   class="w-20 px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-center font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </td>
                        <?php endforeach; ?>
                        <td class="py-3 px-4 text-center font-bold text-slate-900 dark:text-white font-mono">
                            <?= number_format($est['promedio'] ?? 0, 2) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 pt-4">
        <a href="<?= url('/docente/calificaciones') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Guardar Calificaciones</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
