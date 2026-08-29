<?php
/**
 * Vista: Docente - Revisiones de Notas Recibidas
 */
$titulo = 'Solicitudes de Revisión de Notas';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Solicitudes de Revisión de Notas</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Revise los reclamos enviados por los estudiantes y emita su respuesta o corrección.</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaRevisionesDocente" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400">
                    <th class="py-3 px-3">Estudiante</th>
                    <th class="py-3 px-3">Asignatura / Sección</th>
                    <th class="py-3 px-3">Motivo del Reclamo</th>
                    <th class="py-3 px-3">Estado</th>
                    <th class="py-3 px-3">Respuesta Emitida</th>
                    <th class="py-3 px-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php if (empty($solicitudes)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">No hay solicitudes de revisión pendientes.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($solicitudes as $sol): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                            <?= htmlspecialchars(($sol['estudiante_nombre'] ?? '') . ' ' . ($sol['estudiante_apellido'] ?? '')) ?>
                        </td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                            <span class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($sol['materia_nombre'] ?? 'N/A') ?></span>
                            <span class="block text-xs text-slate-500"><?= htmlspecialchars(($sol['grado_nombre'] ?? '') . ' - ' . ($sol['seccion_nombre'] ?? '')) ?></span>
                        </td>
                        <td class="py-3 px-3 text-slate-600 dark:text-slate-300 text-xs max-w-xs" title="<?= htmlspecialchars($sol['motivo']) ?>">
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
                        <td class="py-3 px-3 text-xs text-slate-600 dark:text-slate-300">
                            <?= !empty($sol['respuesta']) ? htmlspecialchars($sol['respuesta']) : '<span class="text-slate-400 italic">Sin responder</span>' ?>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <button type="button" 
                                    onclick='abrirModalResponder(<?= json_encode($sol, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                    class="inline-flex items-center space-x-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors">
                                <i class="bi bi-reply-fill"></i>
                                <span>Responder</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Responder Solicitud -->
<div id="modalResponder" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-reply-fill text-blue-600"></i> Responder Solicitud de Revisión
            </h3>
            <button type="button" onclick="cerrarModalResponder()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form method="POST" action="<?= url('/docente/revisiones/responder') ?>" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
            <input type="hidden" name="id_solicitud" id="modalSolicitudId" value="0">

            <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700/80 text-xs space-y-1">
                <p><strong>Estudiante:</strong> <span id="modalEstudianteNombre">-</span></p>
                <p><strong>Asignatura:</strong> <span id="modalMateriaNombre">-</span></p>
                <p><strong>Motivo del Reclamo:</strong> <span id="modalMotivoTexto" class="italic text-slate-600 dark:text-slate-300">-</span></p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Estado del Reclamo</label>
                <select name="estado" id="modalEstadoSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="aprobada">Aprobada (Procede corrección de nota)</option>
                    <option value="rechazada">Rechazada (Nota se mantiene)</option>
                    <option value="en_revision">En Revisión</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Respuesta al Estudiante</label>
                <textarea name="respuesta" id="modalRespuestaTexto" rows="3" required placeholder="Escriba su respuesta o aclaratoria sobre la calificación..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700">
                <a id="linkIrACalificar" href="#" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Ir a editar calificaciones
                </a>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="cerrarModalResponder()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all">Guardar Respuesta</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalResponder(sol) {
    $('#modalSolicitudId').val(sol.id);
    $('#modalEstudianteNombre').text((sol.estudiante_nombre || '') + ' ' + (sol.estudiante_apellido || ''));
    $('#modalMateriaNombre').text(sol.materia_nombre || '');
    $('#modalMotivoTexto').text(sol.motivo || '');
    $('#modalRespuestaTexto').val(sol.respuesta || '');
    $('#modalEstadoSelect').val(sol.estado || 'aprobada');
    $('#linkIrACalificar').attr('href', '<?= url('/docente/calificaciones/registrar/') ?>' + sol.asignacion_id);
    $('#modalResponder').removeClass('hidden');
}
function cerrarModalResponder() {
    $('#modalResponder').addClass('hidden');
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
