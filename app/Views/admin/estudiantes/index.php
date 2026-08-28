<?php
/**
 * Vista: Admin - Listado de Estudiantes (Tailwind CSS v3)
 */
$titulo = 'Gestión de Estudiantes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Estudiantes</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Directorio de estudiantes matriculados en la institución.</p>
    </div>
    <?php if (in_array('admin.estudiantes.crear', $_SESSION['usuario_permisos'] ?? []) || in_array('estudiantes.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
        <a href="<?= url('/admin/estudiantes/crear') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Estudiante</span>
        </a>
    <?php endif; ?>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaEstudiantes" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Fecha Nacimiento</th>
                    <th>Género</th>
                    <th>Grado/Sección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($estudiantes as $est): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-medium text-slate-800 dark:text-slate-200"><?= htmlspecialchars($est['cedula']) ?></td>
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($est['nombres']) ?></td>
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($est['apellidos']) ?></td>
                    <td class="text-slate-500 dark:text-slate-400 text-xs"><?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?></td>
                    <td>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $est['genero'] === 'M' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-300' ?>">
                            <?= $est['genero'] === 'M' ? 'Masculino' : 'Femenino' ?>
                        </span>
                    </td>
                    <td class="text-slate-600 dark:text-slate-300">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            <?= htmlspecialchars($est['grado_nombre'] ?? 'N/A') ?> - <?= htmlspecialchars($est['seccion_nombre'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td>
                        <?php if (($est['activo'] ?? $est['estado'] ?? 1) == 1): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactivo
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="<?= url('/admin/estudiantes/editar/' . $est['id']) ?>" class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" onclick="confirmarEliminar(<?= $est['id'] ?>, '<?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']) ?>')" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar">
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

<form id="formEliminar" method="POST" action="<?= url('/admin/estudiantes/eliminar') ?>" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar al estudiante "' + nombre + '"?')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}

$(document).ready(function() {
    $('#tablaEstudiantes').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>