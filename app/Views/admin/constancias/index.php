<?php
/**
 * Vista: Admin - Solicitudes de Constancias (Tailwind CSS v3 con Modales de Aprobación/Rechazo)
 */
$titulo = 'Solicitudes de Constancias';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Solicitudes de Constancias</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Revisión, aprobación y emisión de constancias estudiantiles.</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaConstancias" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Tipo de Constancia</th>
                    <th>Fecha Solicitud</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($solicitudes as $sol): ?>
                <?php 
                    $nombreEst = $sol['estudiante_nombre'] . ' ' . ($sol['estudiante_apellido'] ?? '');
                    $fechaFormateada = date('d/m/Y H:i', strtotime($sol['fecha_solicitud']));
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($nombreEst) ?></td>
                    <td>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                            <?= htmlspecialchars(ucfirst($sol['tipo'])) ?>
                        </span>
                    </td>
                    <td class="text-slate-500 dark:text-slate-400 text-xs"><?= $fechaFormateada ?></td>
                    <td>
                        <?php if ($sol['estado'] === 'pendiente'): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 me-1"></span> Pendiente
                            </span>
                        <?php elseif ($sol['estado'] === 'aprobada'): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Aprobada
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 me-1"></span> Rechazada
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <?php if ($sol['estado'] === 'pendiente'): ?>
                                <button type="button" 
                                        onclick="abrirModalAprobar(<?= $sol['id'] ?>, '<?= htmlspecialchars(addslashes($nombreEst)) ?>', '<?= htmlspecialchars(addslashes($sol['tipo'])) ?>', '<?= $fechaFormateada ?>')" 
                                        class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 transition-colors" 
                                        title="Aprobar Solicitud">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" 
                                        onclick="abrirModalRechazar(<?= $sol['id'] ?>, '<?= htmlspecialchars(addslashes($nombreEst)) ?>', '<?= htmlspecialchars(addslashes($sol['tipo'])) ?>')" 
                                        class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" 
                                        title="Rechazar Solicitud">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php elseif ($sol['estado'] === 'aprobada'): ?>
                                <a href="<?= url('/constancias/imprimir/' . $sol['id']) ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors" title="Imprimir / PDF">
                                    <i class="bi bi-printer"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: APROBAR CONSTANCIA -->
<div id="modalAprobar" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
        <div class="px-6 py-4 bg-emerald-600 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="bi bi-check-circle-fill text-xl"></i>
                <h3 class="font-bold text-base">Aprobar Solicitud de Constancia</h3>
            </div>
            <button type="button" onclick="cerrarModalAprobar()" class="text-emerald-200 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form id="formAprobarModal" method="POST" action="" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Estudiante:</span>
                    <strong id="modalAprobarEstudiante" class="text-slate-900 dark:text-white font-bold"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tipo de Constancia:</span>
                    <strong id="modalAprobarTipo" class="text-emerald-600 dark:text-emerald-400 font-bold uppercase"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Fecha de Solicitud:</span>
                    <span id="modalAprobarFecha" class="text-slate-700 dark:text-slate-300"></span>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Al aprobar esta solicitud, la constancia se emitirá inmediatamente y estará disponible para su descarga e impresión.
            </p>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="cerrarModalAprobar()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                    <i class="bi bi-check-lg text-base"></i>
                    <span>Confirmar Aprobación</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: RECHAZAR CONSTANCIA -->
<div id="modalRechazar" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
        <div class="px-6 py-4 bg-rose-600 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="bi bi-exclamation-octagon-fill text-xl"></i>
                <h3 class="font-bold text-base">Rechazar Solicitud de Constancia</h3>
            </div>
            <button type="button" onclick="cerrarModalRechazar()" class="text-rose-200 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <form id="formRechazarModal" method="POST" action="" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Estudiante:</span>
                    <strong id="modalRechazarEstudiante" class="text-slate-900 dark:text-white font-bold"></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tipo de Constancia:</span>
                    <strong id="modalRechazarTipo" class="text-rose-600 dark:text-rose-400 font-bold uppercase"></strong>
                </div>
            </div>

            <div>
                <label for="inputMotivoRechazo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Motivo del Rechazo *</label>
                <textarea name="motivo_rechazo" id="inputMotivoRechazo" rows="3" required placeholder="Indique la razón oficial del rechazo para conocimiento del estudiante..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-rose-500 focus:border-transparent text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="cerrarModalRechazar()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                    <i class="bi bi-x-lg text-base"></i>
                    <span>Confirmar Rechazo</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAprobar(id, estudiante, tipo, fecha) {
    document.getElementById('modalAprobarEstudiante').innerText = estudiante;
    document.getElementById('modalAprobarTipo').innerText = tipo;
    document.getElementById('modalAprobarFecha').innerText = fecha;
    document.getElementById('formAprobarModal').action = '<?= url('/admin/constancias/aprobar/') ?>' + id;
    document.getElementById('modalAprobar').classList.remove('hidden');
}

function cerrarModalAprobar() {
    document.getElementById('modalAprobar').classList.add('hidden');
}

function abrirModalRechazar(id, estudiante, tipo) {
    document.getElementById('modalRechazarEstudiante').innerText = estudiante;
    document.getElementById('modalRechazarTipo').innerText = tipo;
    document.getElementById('inputMotivoRechazo').value = '';
    document.getElementById('formRechazarModal').action = '<?= url('/admin/constancias/rechazar/') ?>' + id;
    document.getElementById('modalRechazar').classList.remove('hidden');
}

function cerrarModalRechazar() {
    document.getElementById('modalRechazar').classList.add('hidden');
}

$(document).ready(function() {
    $('#tablaConstancias').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[2, 'desc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
