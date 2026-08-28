<?php
/**
 * Vista: Admin - Solicitudes de Constancias (Tailwind CSS v3)
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
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($sol['estudiante_nombre']) ?></td>
                    <td>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                            <?= htmlspecialchars(ucfirst($sol['tipo'])) ?>
                        </span>
                    </td>
                    <td class="text-slate-500 dark:text-slate-400 text-xs"><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
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
                                <button type="button" onclick="aprobar(<?= $sol['id'] ?>)" class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 transition-colors" title="Aprobar Solicitud">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button type="button" onclick="rechazar(<?= $sol['id'] ?>)" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Rechazar Solicitud">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php elseif ($sol['estado'] === 'aprobada'): ?>
                                <a href="<?= url('/constancias/imprimir/' . $sol['id']) ?>" target="_blank" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors" title="Imprimir / PDF">
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

<form id="formAprobar" method="POST" action="" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
</form>

<form id="formRechazar" method="POST" action="" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="motivo_rechazo" id="motivoRechazo">
</form>

<script>
function aprobar(id) {
    if (confirm('¿Desea aprobar esta solicitud de constancia?')) {
        var form = document.getElementById('formAprobar');
        form.action = '<?= url('/admin/constancias/aprobar/') ?>' + id;
        form.submit();
    }
}
function rechazar(id) {
    var motivo = prompt('Motivo del rechazo de la solicitud:');
    if (motivo) {
        document.getElementById('motivoRechazo').value = motivo;
        var form = document.getElementById('formRechazar');
        form.action = '<?= url('/admin/constancias/rechazar/') ?>' + id;
        form.submit();
    }
}

$(document).ready(function() {
    $('#tablaConstancias').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[2, 'desc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
