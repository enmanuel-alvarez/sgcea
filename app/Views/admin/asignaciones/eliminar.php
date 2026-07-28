<?php
/**
 * Vista: Admin - Confirmar eliminación de asignación (modal)
 */
use Core\Security;
$titulo = 'Eliminar Asignación';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                </div>
                <div class="card-body text-center">
                    <p class="lead">¿Está seguro que desea eliminar esta asignación?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                    <hr>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="?route=admin/asignaciones" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                        <form method="POST" action="?route=admin/asignaciones/eliminar" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
                            <input type="hidden" name="id" value="<?= $asignacion['id'] ?>">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
