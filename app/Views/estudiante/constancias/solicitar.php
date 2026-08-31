<?php
/**
 * Vista: Estudiante - Gestión e Historial de Constancias (Tailwind CSS v3)
 */
$titulo = 'Gestión de Constancias';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header y Botón para Modal -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestión de Constancias</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consulte su histórico de trámites y solicite nuevas constancias oficiales.</p>
    </div>
    <div class="flex items-center space-x-3">
        <button type="button" onclick="abrirModalSolicitarConstancia()" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-plus-lg text-base"></i>
            <span>Solicitar Nueva Constancia</span>
        </button>
        <a href="<?= url('/estudiante/dashboard') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<?php if (!empty($tiposPendientes)): ?>
    <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs flex items-start space-x-3 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold">Solicitudes pendientes en curso</p>
            <p class="mt-0.5">Actualmente posee trámites pendientes para: <strong class="underline font-semibold"><?= implode(', ', array_map('ucfirst', $tiposPendientes)) ?></strong>. Recuerde que existe un límite de 1 solicitud activa por tipo de documento a la vez.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Tabla de Histórico de Constancias -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <i class="bi bi-clock-history text-blue-500"></i>
            <span>Histórico de Solicitudes</span>
        </h3>
        <span class="text-xs text-slate-400 font-mono">Total: <?= count($solicitudes ?? []) ?></span>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="p-8 text-center rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 space-y-3">
            <i class="bi bi-file-earmark-text text-4xl text-slate-400 dark:text-slate-500 block"></i>
            <p class="text-sm font-medium">No registra solicitudes de constancias archivadas o activas.</p>
            <button type="button" onclick="abrirModalSolicitarConstancia()" class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-md hover:bg-blue-500 transition-all">
                <i class="bi bi-plus-lg"></i>
                <span>Crear Primera Solicitud</span>
            </button>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table id="tablaHistorialConstancias" class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase font-bold text-slate-400">
                        <th class="py-3 px-2">#</th>
                        <th class="py-3 px-2">Tipo de Documento</th>
                        <th class="py-3 px-2">Motivo / Destino</th>
                        <th class="py-3 px-2">Fecha Solicitud</th>
                        <th class="py-3 px-2">Estado</th>
                        <th class="py-3 px-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php foreach ($solicitudes as $s): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="py-3 px-2 font-mono text-xs text-slate-500 dark:text-slate-400">#<?= e($s['id']) ?></td>
                            <td class="py-3 px-2">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200/50 dark:border-blue-800/50">
                                    <?= htmlspecialchars(ucfirst(e($s['tipo']))) ?>
                                </span>
                            </td>
                            <td class="py-3 px-2 text-slate-700 dark:text-slate-300 text-xs font-medium max-w-xs truncate" title="<?= htmlspecialchars(e($s['motivo'])) ?>">
                                <?= htmlspecialchars(e($s['motivo'])) ?>
                            </td>
                            <td class="py-3 px-2 text-slate-500 dark:text-slate-400 text-xs">
                                <?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?>
                            </td>
                            <td class="py-3 px-2">
                                <?php if ($s['estado'] == 'aprobada'): ?>
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Aprobada
                                    </span>
                                <?php elseif ($s['estado'] == 'rechazada'): ?>
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/50" title="Motivo: <?= htmlspecialchars(e($s['resolucion_motivo'] ?? 'No especificado')) ?>">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 me-1"></span> Rechazada
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 me-1"></span> En Proceso
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-2 text-right">
                                <?php if ($s['estado'] == 'aprobada'): ?>
                                    <a href="<?= url('/constancias/imprimir/' . $s['id']) ?>" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors" title="Imprimir / Exportar Documento">
                                        <i class="bi bi-printer-fill text-xs"></i>
                                        <span>Imprimir PDF</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 dark:text-slate-500 text-xs italic">Pendiente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ════════════════ MODAL FLOTANTE: SOLICITAR NUEVA CONSTANCIA ════════════════ -->
<div id="modalSolicitarConstancia" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <div class="flex items-center space-x-2 text-blue-600 dark:text-blue-400">
                <i class="bi bi-file-earmark-plus text-2xl"></i>
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Nueva Solicitud de Constancia</h3>
            </div>
            <button type="button" onclick="cerrarModalSolicitarConstancia()" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="<?= url('/estudiante/constancias/guardar') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="modal_tipo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Tipo de Documento *</label>
                <select id="modal_tipo" name="tipo" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">Seleccione un documento...</option>
                    <option value="estudio" <?= in_array('estudio', $tiposPendientes ?? []) ? 'disabled' : '' ?>>
                        Constancia de Estudio <?= in_array('estudio', $tiposPendientes ?? []) ? '(Pendiente Activa)' : '' ?>
                    </option>
                    <option value="conducta" <?= in_array('conducta', $tiposPendientes ?? []) ? 'disabled' : '' ?>>
                        Constancia de Buena Conducta <?= in_array('conducta', $tiposPendientes ?? []) ? '(Pendiente Activa)' : '' ?>
                    </option>
                    <option value="notas" <?= in_array('notas', $tiposPendientes ?? []) ? 'disabled' : '' ?>>
                        Constancia / Certificación de Notas <?= in_array('notas', $tiposPendientes ?? []) ? '(Pendiente Activa)' : '' ?>
                    </option>
                </select>
            </div>

            <div>
                <label for="modal_motivo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Motivo / Institución Destino *</label>
                <textarea id="modal_motivo" name="motivo" rows="3" required placeholder="Ej: Presentación para beca escolar, trámite legal, inscripción deportiva..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" onclick="cerrarModalSolicitarConstancia()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-send"></i>
                    <span>Enviar Solicitud</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalSolicitarConstancia() {
    document.getElementById('modalSolicitarConstancia').classList.remove('hidden');
}

function cerrarModalSolicitarConstancia() {
    document.getElementById('modalSolicitarConstancia').classList.add('hidden');
}

$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tablaHistorialConstancias').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
            order: [[0, 'desc']]
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
