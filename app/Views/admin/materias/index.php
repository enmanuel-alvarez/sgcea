<?php
/**
 * Vista: Admin - Listado de Materias
 */
$titulo = 'Gestión de Materias';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Materias</h1>
    <?php if (in_array('materias.crear', $_SESSION['usuario_permisos'] ?? [])): ?>
        <a href="<?= url('/admin/materias/crear') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nueva Materia
        </a>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table id="tablaMaterias" class="table table-striped table-sm">
        <thead class="table-dark">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Créditos</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materias as $mat): ?>
            <tr>
                <td><?= htmlspecialchars($mat['codigo'] ?? '') ?></td>
                <td><?= htmlspecialchars($mat['nombre'] ?? '') ?></td>
                <td><?= (int)($mat['creditos'] ?? 0) ?></td>
                <td>
                    <span class="badge bg-<?= ($mat['activo'] ?? 0) == 1 ? 'success' : 'secondary' ?>">
                        <?= ($mat['activo'] ?? 0) == 1 ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <?php if (in_array('materias.editar', $_SESSION['usuario_permisos'] ?? [])): ?>
                        <a href="<?= url('/admin/materias/editar/' . $mat['id']) ?>" class="btn btn-sm btn-warning" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (in_array('materias.eliminar', $_SESSION['usuario_permisos'] ?? [])): ?>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirmarEliminar(<?= $mat['id'] ?>, '<?= htmlspecialchars($mat['nombre'] ?? '') ?>')" 
                                title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<form id="formEliminar" method="POST" action="<?= url('/admin/materias/eliminar') ?>" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
$(document).ready(function() {
    $('#tablaMaterias').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
    });
});

function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar la materia "' + nombre + '"? Esta acción no se puede deshacer.')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>