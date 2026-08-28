<?php
/**
 * Vista: Admin - Listado de Secciones (Tailwind CSS v3)
 */
$titulo = 'Gestión de Secciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Secciones</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Organización de aulas, capacidad de cupos y grados escolares.</p>
    </div>
    <?php if (in_array('admin.secciones.crear', $_SESSION['usuario_permisos'] ?? []) || in_array('secciones.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
        <a href="<?= url('/admin/secciones/crear') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-door-open"></i>
            <span>Nueva Sección</span>
        </a>
    <?php endif; ?>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaSecciones" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr>
                    <th>Nombre / Sección</th>
                    <th>Grado</th>
                    <th>Cupo Máximo</th>
                    <th>Año Académico</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($secciones as $sec): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($sec['nombre']) ?></td>
                    <td class="text-slate-600 dark:text-slate-300 font-medium"><?= htmlspecialchars($sec['grado_nombre'] ?? $sec['grado'] ?? 'N/A') ?></td>
                    <td class="text-slate-600 dark:text-slate-300"><?= $sec['cupo_maximo'] ?? $sec['capacidad_maxima'] ?? 35 ?> estudiantes</td>
                    <td class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= htmlspecialchars($sec['ano_academico'] ?? '2024-2025') ?></td>
                    <td>
                        <?php if (($sec['activo'] ?? $sec['estado'] ?? 1) == 1): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activa
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactiva
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="<?= url('/admin/secciones/editar/' . $sec['id']) ?>" class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" onclick="confirmarEliminar(<?= $sec['id'] ?>, '<?= htmlspecialchars($sec['nombre']) ?>')" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<form id="formEliminar" method="POST" action="<?= url('/admin/secciones/eliminar') ?>" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar la sección "' + nombre + '"?')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}

$(document).ready(function() {
    $('#tablaSecciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
