<?php
/**
 * Vista: Docente - Gestión de Asistencia (Tailwind CSS v3)
 */
$titulo = 'Gestión de Asistencia';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestión de Asistencia</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Seleccione una materia asignada para registrar el control de asistencia del día.</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaAsignaciones" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr>
                    <th>Materia</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Año Académico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($asignaciones as $asn): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($asn['materia_nombre'] ?? '') ?></td>
                    <td class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($asn['grado_nombre'] ?? 'N/A') ?></td>
                    <td class="text-slate-600 dark:text-slate-300 font-semibold"><?= htmlspecialchars($asn['seccion_nombre'] ?? 'N/A') ?></td>
                    <td class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= htmlspecialchars($asn['ano_academico'] ?? '2024-2025') ?></td>
                    <td>
                        <a href="<?= url('/docente/asistencia/registrar/' . $asn['id']) ?>" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors">
                            <i class="bi bi-calendar-check"></i>
                            <span>Tomar Asistencia</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaAsignaciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>


