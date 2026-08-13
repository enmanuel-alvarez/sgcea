<?php
/**
 * Vista: Admin - Listado de Estudiantes
 */
use Core\Security;

$titulo = 'Gestión de Estudiantes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Estudiantes</h1>
    <?php if (in_array('estudiantes.crear', $_SESSION['usuario_permisos'] ?? [])): ?>
        <a href="?route=admin/estudiantes/crear" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Estudiante
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
                <table id="tablaEstudiantes" class="table table-striped table-sm">
                    <thead class="table-dark">
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
                    <tbody>
                        <?php foreach ($estudiantes as $est): ?>
                        <tr>
                            <td><?= htmlspecialchars($est['cedula']) ?></td>
                            <td><?= htmlspecialchars($est['nombres']) ?></td>
                            <td><?= htmlspecialchars($est['apellidos']) ?></td>
                            <td><?= date('d/m/Y', strtotime($est['fecha_nacimiento'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $est['genero'] === 'M' ? 'primary' : 'danger' ?>">
                                    <?= $est['genero'] === 'M' ? 'Masculino' : 'Femenino' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($est['grado_nombre'] ?? 'N/A') ?> - <?= htmlspecialchars($est['seccion_nombre'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $est['activo'] == 1 ? 'success' : 'secondary' ?>">
                                    <?= $est['activo'] == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (in_array('estudiantes.editar', $_SESSION['usuario_permisos'] ?? [])): ?>
                                    <a href="?route=admin/estudiantes/editar&id=<?= $est['id'] ?>" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (in_array('estudiantes.eliminar', $_SESSION['usuario_permisos'] ?? [])): ?>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmarEliminar(<?= $est['id'] ?>, '<?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']) ?>')" 
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


<form id="formEliminar" method="POST" action="?route=admin/estudiantes/eliminar" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id" id="idEliminar">
</form>

<script>
$(document).ready(function() {
    $('#tablaEstudiantes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
    });
});

function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro de eliminar al estudiante "' + nombre + '"? Esta acción no se puede deshacer.')) {
        document.getElementById('idEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
