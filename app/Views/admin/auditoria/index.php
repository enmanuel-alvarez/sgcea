<?php
/**
 * Vista: Admin - Logs de Auditoría y Bitácora del Sistema (Tailwind CSS v3)
 */
$titulo = 'Logs de Auditoría';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

// Contadores rápidos para tarjetas de resumen
$totalLogs = count($logs);
$totalCreaciones = 0;
$totalEdiciones = 0;
$totalEliminaciones = 0;

foreach ($logs as $l) {
    $act = strtoupper($l['accion'] ?? '');
    if (str_contains($act, 'CREA') || str_contains($act, 'INSERT') || str_contains($act, 'SOLICITAR')) {
        $totalCreaciones++;
    } elseif (str_contains($act, 'EDIT') || str_contains($act, 'UPDATE') || str_contains($act, 'ACTUALIZAR') || str_contains($act, 'APROBAR')) {
        $totalEdiciones++;
    } elseif (str_contains($act, 'ELIMINAR') || str_contains($act, 'DELETE') || str_contains($act, 'REINICIAR') || str_contains($act, 'RECHAZAR')) {
        $totalEliminaciones++;
    }
}
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
            <i class="bi bi-shield-check text-blue-600 dark:text-blue-400"></i>
            Bitácora y Logs de Auditoría
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Historial cronológico de eventos, acciones administrativas y cambios del sistema.</p>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0">
            <i class="bi bi-journal-text"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Registros</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5"><?= number_format($totalLogs) ?></p>
        </div>
    </div>

    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
            <i class="bi bi-plus-circle"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Creaciones</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5"><?= number_format($totalCreaciones) ?></p>
        </div>
    </div>

    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Modificaciones</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5"><?= number_format($totalEdiciones) ?></p>
        </div>
    </div>

    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Acciones Críticas</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5"><?= number_format($totalEliminaciones) ?></p>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="tablaAuditoria">
            <thead class="bg-slate-100/80 dark:bg-slate-700/60 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="px-4 py-3.5">ID</th>
                    <th class="px-4 py-3.5">Fecha / Hora</th>
                    <th class="px-4 py-3.5">Usuario</th>
                    <th class="px-4 py-3.5">Acción</th>
                    <th class="px-4 py-3.5">Módulo / Tabla</th>
                    <th class="px-4 py-3.5">Dirección IP</th>
                    <th class="px-4 py-3.5">Detalles</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($logs as $log): 
                    $userNom = trim(($log['nombre'] ?? '') . ' ' . ($log['apellido'] ?? ''));
                    if (empty($userNom)) $userNom = 'Sistema / Anónimo';
                    $accionStr = strtoupper($log['accion'] ?? 'INFO');
                    
                    // Style badges by action type
                    $badgeStyle = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600';
                    if (str_contains($accionStr, 'CREA') || str_contains($accionStr, 'INSERT') || str_contains($accionStr, 'GUARDAR') || str_contains($accionStr, 'SOLICITAR') || str_contains($accionStr, 'LOGIN')) {
                        $badgeStyle = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40';
                    } elseif (str_contains($accionStr, 'EDIT') || str_contains($accionStr, 'UPDATE') || str_contains($accionStr, 'ACTUALIZAR') || str_contains($accionStr, 'APROBAR')) {
                        $badgeStyle = 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/40';
                    } elseif (str_contains($accionStr, 'ELIMINAR') || str_contains($accionStr, 'DELETE') || str_contains($accionStr, 'REINICIAR') || str_contains($accionStr, 'RECHAZAR') || str_contains($accionStr, 'LOGOUT')) {
                        $badgeStyle = 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40';
                    } elseif (str_contains($accionStr, 'EXPORT') || str_contains($accionStr, 'IMPORT') || str_contains($accionStr, 'BACKUP')) {
                        $badgeStyle = 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40';
                    }

                    // Process details cleanly
                    $detallesRaw = $log['detalles'] ?? '';
                    $detallesParsed = null;
                    if (is_array($detallesRaw)) {
                        $detallesParsed = $detallesRaw;
                    } elseif (is_string($detallesRaw) && !empty($detallesRaw)) {
                        $decoded = json_decode($detallesRaw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $detallesParsed = $decoded;
                        }
                    }

                    if (is_array($detallesParsed)) {
                        $mensaje = $detallesParsed['mensaje'] ?? null;
                        if ($mensaje) {
                            $detallesTexto = $mensaje;
                        } else {
                            $detallesTexto = json_encode($detallesParsed, JSON_UNESCAPED_UNICODE);
                        }
                        $jsonCompleto = json_encode($detallesParsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    } else {
                        $detallesTexto = (string)$detallesRaw;
                        $jsonCompleto = $detallesTexto;
                    }
                    if (empty($detallesTexto)) {
                        $detallesTexto = 'Sin detalles adicionados';
                    }
                ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono text-xs text-slate-400 dark:text-slate-500">#<?= $log['id'] ?></td>
                        <td class="px-4 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            <?= date('d/m/Y H:i:s', strtotime($log['fecha'] ?? 'now')) ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-semibold text-slate-900 dark:text-white block"><?= htmlspecialchars($userNom) ?></span>
                            <?php if (!empty($log['email'])): ?>
                                <span class="text-[11px] text-slate-400 font-mono block"><?= htmlspecialchars($log['email']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono tracking-wider uppercase <?= $badgeStyle ?>">
                                <?= htmlspecialchars($accionStr) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700/70 border border-slate-200 dark:border-slate-600">
                                <?= htmlspecialchars($log['tabla'] ?? 'general') ?>
                            </span>
                            <?php if (!empty($log['registro_id'])): ?>
                                <span class="text-slate-400 text-[11px] ms-1">(ID: <?= $log['registro_id'] ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <i class="bi bi-hdd-network text-slate-400 me-1"></i><?= htmlspecialchars($log['ip'] ?? '127.0.0.1') ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center space-x-2">
                                <div class="max-w-xs md:max-w-sm truncate text-xs text-slate-600 dark:text-slate-300 font-sans" title="<?= htmlspecialchars($detallesTexto) ?>">
                                    <?= htmlspecialchars($detallesTexto) ?>
                                </div>
                                <?php if (!empty($detallesParsed) || strlen($jsonCompleto) > 40): ?>
                                    <button type="button" 
                                            onclick='verDetallesModal(<?= json_encode([
                                                "id" => $log["id"],
                                                "fecha" => date("d/m/Y H:i:s", strtotime($log["fecha"] ?? "now")),
                                                "usuario" => $userNom,
                                                "email" => $log["email"] ?? "",
                                                "accion" => $accionStr,
                                                "tabla" => $log["tabla"] ?? "general",
                                                "registro_id" => $log["registro_id"] ?? null,
                                                "ip" => $log["ip"] ?? "127.0.0.1",
                                                "user_agent" => $log["user_agent"] ?? "",
                                                "detalles_json" => $jsonCompleto
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                            class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300 hover:bg-blue-100 transition-colors shrink-0">
                                        Ver +
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para ver detalles completos del log -->
<div id="modalDetalles" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-xl w-full p-6 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4 mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="bi bi-info-circle text-blue-500"></i>
                Detalles del Evento de Auditoría <span id="modalLogId" class="text-slate-400 font-mono text-sm"></span>
            </h3>
            <button type="button" onclick="cerrarModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="space-y-3 text-sm">
            <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Fecha / Hora</span>
                    <span id="modalFecha" class="font-mono text-slate-800 dark:text-slate-200 text-xs"></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Usuario</span>
                    <span id="modalUsuario" class="font-semibold text-slate-800 dark:text-slate-200 text-xs"></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Acción</span>
                    <span id="modalAccion" class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400"></span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Módulo / IP</span>
                    <span id="modalModulo" class="font-mono text-slate-800 dark:text-slate-200 text-xs"></span>
                </div>
            </div>
            
            <div>
                <span class="text-xs text-slate-400 uppercase font-semibold block mb-1">Payload / Detalles JSON</span>
                <pre id="modalJson" class="p-3 bg-slate-900 text-emerald-400 font-mono text-xs rounded-xl overflow-x-auto max-h-60 border border-slate-800"></pre>
            </div>
            
            <div id="wrapperUserAgent" class="hidden">
                <span class="text-xs text-slate-400 uppercase font-semibold block mb-1">Navegador / User Agent</span>
                <span id="modalUserAgent" class="font-mono text-[11px] text-slate-500 dark:text-slate-400 block break-all bg-slate-100 dark:bg-slate-900/40 p-2 rounded-lg"></span>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="cerrarModal()" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold rounded-xl text-sm hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                Cerrar
            </button>
        </div>
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

function verDetallesModal(log) {
    $('#modalLogId').text('#' + log.id);
    $('#modalFecha').text(log.fecha);
    $('#modalUsuario').text(log.usuario + (log.email ? ' (' + log.email + ')' : ''));
    $('#modalAccion').text(log.accion);
    $('#modalModulo').text(log.tabla + (log.registro_id ? ' (ID: ' + log.registro_id + ')' : '') + ' - IP: ' + log.ip);
    $('#modalJson').text(log.detalles_json);
    
    if (log.user_agent) {
        $('#modalUserAgent').text(log.user_agent);
        $('#wrapperUserAgent').removeClass('hidden');
    } else {
        $('#wrapperUserAgent').addClass('hidden');
    }

    $('#modalDetalles').removeClass('hidden').addClass('flex');
}

function cerrarModal() {
    $('#modalDetalles').addClass('hidden').removeClass('flex');
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>


