<?php
/**
 * Vista: Estudiante - Solicitudes de Revisión de Notas
 */
$titulo = 'Solicitudes de Revisión de Notas';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Solicitudes de Revisión de Notas</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consulte el estado de sus reclamos o envíe una nueva solicitud de revisión a sus profesores.</p>
    </div>
    <div>
        <button type="button" onclick="abrirModalRevision()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-plus-lg"></i>
            <span>Nueva Solicitud de Revisión</span>
        </button>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaRevisiones" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400">
                    <th class="py-3 px-3">Asignatura</th>
                    <th class="py-3 px-3">Motivo / Reclamo</th>
                    <th class="py-3 px-3">Estado</th>
                    <th class="py-3 px-3">Respuesta del Profesor</th>
                    <th class="py-3 px-3 text-right">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400 text-sm">No ha enviado solicitudes de revisión de notas.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($solicitudes as $sol): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                            <?= htmlspecialchars($sol['materia_nombre'] ?? 'N/A') ?>
                            <?php if (!empty($sol['evaluacion_nombre'])): ?>
                                <span class="block text-xs font-normal text-slate-500 dark:text-slate-400"><?= htmlspecialchars($sol['evaluacion_nombre']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="<?= htmlspecialchars($sol['motivo']) ?>">
                            <?= htmlspecialchars($sol['motivo']) ?>
                        </td>
                        <td class="py-3 px-3">
                            <?php 
                            $est = $sol['estado'] ?? 'pendiente';
                            if ($est === 'aprobada'): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Aprobada</span>
                                </span>
                            <?php elseif ($est === 'rechazada'): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Rechazada</span>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Pendiente</span>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300 text-xs">
                            <?= !empty($sol['respuesta']) ? htmlspecialchars($sol['respuesta']) : '<span class="text-slate-400 italic">En espera de respuesta...</span>' ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-xs text-slate-500 dark:text-slate-400">
                            <?= date('d/m/Y', strtotime($sol['fecha_solicitud'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Nueva Solicitud de Revisión -->
<div id="modalRevision" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-chat-left-dots text-blue-600"></i> Solicitar Revisión de Nota
            </h3>
            <button type="button" onclick="cerrarModalRevision()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form method="POST" action="<?= url('/estudiante/revisiones/guardar') ?>" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Asignatura</label>
                <select name="id_asignacion" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Seleccionar Asignatura --</option>
                    <?php foreach ($asignaciones as $asg): 
                        $deshabilitado = !empty($asg['tiene_revision_activa']);
                    ?>
                        <option value="<?= $asg['id'] ?>" <?= $deshabilitado ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($asg['materia_nombre']) ?><?= $deshabilitado ? ' (Revisión activa en curso)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Motivo / Justificación</label>
                <textarea name="motivo" rows="4" required placeholder="Explique detalladamente por qué solicita la revisión de su calificación..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                <button type="button" onclick="cerrarModalRevision()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all">Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRevision() {
    $('#modalRevision').removeClass('hidden');
}
function cerrarModalRevision() {
    $('#modalRevision').addClass('hidden');
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

