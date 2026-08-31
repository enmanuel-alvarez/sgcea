<?php
/**
 * Vista: Docente - Registrar Asistencia Masiva (Tailwind CSS v3)
 */
$titulo = 'Registrar Asistencia';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Control de Asistencia</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Asignación: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($asignacion['materia_nombre'] ?? '') ?></strong> • 
            Sección: <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars(($asignacion['grado_nombre'] ?? '') . ' - ' . ($asignacion['seccion_nombre'] ?? '')) ?></strong>
        </p>
    </div>
    <div>
        <a href="<?= url('/docente/asistencia') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<form method="POST" action="<?= url('/docente/asistencia/guardar') ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id_asignacion" value="<?= $asignacion['id'] ?>">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-700/50 pb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-calendar-event text-emerald-500"></i>
                <span>Fecha de la Clase</span>
            </h3>
            <div class="w-full sm:w-auto">
                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>"
                       class="px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm font-medium focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        <th class="py-3 px-4 font-bold text-xs uppercase text-slate-500 dark:text-slate-400">Estudiante</th>
                        <th class="py-3 px-4 text-center font-bold text-xs uppercase text-slate-500 dark:text-slate-400">Estado de Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($estudiantes as $est): 
                        $estId = $est['id'] ?? $est['id_estudiante'];
                        $asistExist = current(array_filter($asistencias ?? [], fn($a) => ($a['id_estudiante'] ?? $a['estudiante_id']) == $estId));
                        $estado = $asistExist ? $asistExist['estado'] : 'presente';
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            <?= htmlspecialchars(($est['nombres'] ?? $est['nombre'] ?? '') . ' ' . ($est['apellidos'] ?? $est['apellido'] ?? '')) ?>
                        </td>
                        <td class="py-2 px-4 text-center">
                            <select name="asistencia[<?= $estId ?>]"
                                    class="px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-medium text-xs focus:ring-2 focus:ring-emerald-500">
                                <option value="presente" <?= $estado === 'presente' ? 'selected' : '' ?>>Presente</option>
                                <option value="ausente" <?= $estado === 'ausente' ? 'selected' : '' ?>>Ausente</option>
                                <option value="tarde" <?= $estado === 'tarde' ? 'selected' : '' ?>>Llegada Tarde</option>
                                <option value="justificado" <?= $estado === 'justificado' ? 'selected' : '' ?>>Inasistencia Justificada</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 pt-4">
        <a href="<?= url('/docente/asistencia') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-emerald-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Guardar Asistencia</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>


