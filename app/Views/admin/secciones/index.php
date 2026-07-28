<?php
/**
 * Vista: Admin - Listado de Secciones
 */
use Core\Security;

$titulo = 'Gestión de Secciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-door-open me-2"></i>Secciones</h2>
                <?php if (in_array('secciones.crear', $_SESSION['usuario_permisos'] ?? [])): ?>
                    <a href="?route=admin/secciones/crear" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Nueva Sección
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaSecciones" class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Capacidad Máxima</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($secciones as $sec): ?>
                        <tr>
                            <td><?= htmlspecialchars($sec['nombre']) ?></td>
                            <td><?= htmlspecialchars($sec['descripcion'] ?? 'N/A') ?></td>
                            <td><?= $sec['capacidad_maxima'] ?? 'N/A' ?></td>
                            <td>
                                <span class="badge bg-<?= $sec['activo'] == 1 ? 'success' : 'secondary' ?>">
                                    <?= $sec['activo'] == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (in_array('secciones.editar', $_SESSION['usuario_permisos'] ?? [])): ?>
                                    <a href="?route=admin/secciones/editar&id=<?= $sec['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (in_array('secciones.eliminar', $_SESSION['usuario_permisos'] ?? [])): ?>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmarEliminar(<?= $sec['id'] ?>, '<?= htmlspecialchars($sec['nombre']) ?>')" 
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
        </div>
    </div>
</div>

<form id="formEliminar" method="POST" action="?route=admin/secciones/eliminar" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
$(document).ready(function() {
    $('#tablaSecciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});

function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar la sección "' + nombre + '"?')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
