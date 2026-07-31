<?php
/**
 * Vista: Admin - Listado de Asignaciones
 */
use Core\Security;

$titulo = 'Gestión de Asignaciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Asignaciones</h1>
    <?php if (in_array('asignaciones.crear', $_SESSION['usuario_permisos'] ?? [])): ?>
        <a href="?route=admin/asignaciones/crear" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nueva Asignación
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaAsignaciones" class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th>Grado</th>
                            <th>Sección</th>
                            <th>Año Académico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asignaciones as $asn): ?>
                        <tr>
                            <td><?= htmlspecialchars($asn['docente_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['materia_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['grado_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['seccion_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['ano_academico']) ?></td>
                            <td>
                                <span class="badge bg-<?= $asn['activo'] == 1 ? 'success' : 'secondary' ?>">
                                    <?= $asn['activo'] == 1 ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (in_array('asignaciones.eliminar', $_SESSION['usuario_permisos'] ?? [])): ?>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmarEliminar(<?= $asn['id'] ?>, '<?= htmlspecialchars($asn['docente_nombre'] . ' - ' . $asn['materia_nombre']) ?>')" 
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


<form id="formEliminar" method="POST" action="?route=admin/asignaciones/eliminar" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
$(document).ready(function() {
    $('#tablaAsignaciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});

function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar la asignación "' + nombre + '"?')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
