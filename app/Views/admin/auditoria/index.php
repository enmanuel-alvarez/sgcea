<?php
/**
 * Vista: Admin - Logs de Auditoría y Bitácora del Sistema (Tailwind CSS v3)
 */
$titulo = 'Logs de Auditoría';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Bitácora y Logs de Auditoría</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Historial cronológico de eventos, acciones administrativas y cambios del sistema.</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="tablaAuditoria">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha / Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo / Tabla</th>
                    <th>Dirección IP</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($logs as $log): 
                    $userNom = trim(($log['nombre'] ?? '') . ' ' . ($log['apellido'] ?? ''));
                    if (empty($userNom)) $userNom = 'Sistema / Anon';
                    $accionStr = strtoupper($log['accion'] ?? 'INFO');
                    
                    // Badge styles by action
                    $badgeStyle = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
                    if (str_contains($accionStr, 'CREA') || str_contains($accionStr, 'INSERT') || str_contains($accionStr, 'GUARDAR')) {
                        $badgeStyle = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
                    } elseif (str_contains($accionStr, 'EDIT') || str_contains($accionStr, 'UPDATE') || str_contains($accionStr, 'ACTUALIZAR')) {
                        $badgeStyle = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
                    } elseif (str_contains($accionStr, 'ELIMINAR') || str_contains($accionStr, 'DELETE') || str_contains($accionStr, 'REINICIAR')) {
                        $badgeStyle = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
                    } elseif (str_contains($accionStr, 'EXPORT') || str_contains($accionStr, 'IMPORT')) {
                        $badgeStyle = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300';
                    }
                ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="font-mono text-xs text-slate-400 dark:text-slate-500">#<?= $log['id'] ?></td>
                        <td class="font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            <?= date('d/m/Y H:i:s', strtotime($log['fecha'] ?? 'now')) ?>
                        </td>
                        <td class="font-semibold text-slate-900 dark:text-white">
                            <?= htmlspecialchars($userNom) ?>
                            <?php if (!empty($log['email'])): ?>
                                <span class="block text-[11px] font-normal text-slate-400 font-mono"><?= htmlspecialchars($log['email']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono tracking-wider uppercase <?= $badgeStyle ?>">
                                <?= htmlspecialchars($accionStr) ?>
                            </span>
                        </td>
                        <td class="font-mono text-xs text-slate-700 dark:text-slate-300">
                            <?= htmlspecialchars($log['tabla'] ?? 'general') ?>
                            <?php if (!empty($log['registro_id'])): ?>
                                <span class="text-slate-400 me-1">(ID: <?= $log['registro_id'] ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-mono text-xs text-slate-500 dark:text-slate-400">
                            <?= htmlspecialchars($log['ip'] ?? '127.0.0.1') ?>
                        </td>
                        <td class="text-xs text-slate-600 dark:text-slate-300 max-w-xs truncate" title="<?= htmlspecialchars(is_string($log['detalles']) ? $log['detalles'] : json_encode($log['detalles'])) ?>">
                            <?= htmlspecialchars(is_string($log['detalles']) ? $log['detalles'] : json_encode($log['detalles'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaAuditoria').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        responsive: true
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
