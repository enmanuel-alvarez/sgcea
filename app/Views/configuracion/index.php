<?php
use Core\Security;
$titulo = 'Configuración del Sistema';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Configuración</h1>
</div>
<?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['flash_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <form method="POST" action="?route=configuracion/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombre_sistema" class="form-label">Nombre del Sistema *</label>
                        <input type="text" class="form-control" id="nombre_sistema" name="nombre_sistema" value="<?= htmlspecialchars($config['nombre_sistema'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nombre_institucion" class="form-label">Nombre de la Institución *</label>
                        <input type="text" class="form-control" id="nombre_institucion" name="nombre_institucion" value="<?= htmlspecialchars($config['nombre_institucion'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="ano_lectivo" class="form-label">Año Lectivo *</label>
                        <input type="text" class="form-control" id="ano_lectivo" name="ano_lectivo" value="<?= htmlspecialchars($config['ano_lectivo'] ?? date('Y')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nota_minima" class="form-label">Nota Mínima Aprobatoria *</label>
                        <input type="number" class="form-control" id="nota_minima" name="nota_minima" value="<?= htmlspecialchars($config['nota_minima'] ?? 70) ?>" min="0" max="100" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nota_maxima" class="form-label">Nota Máxima *</label>
                        <input type="number" class="form-control" id="nota_maxima" name="nota_maxima" value="<?= htmlspecialchars($config['nota_maxima'] ?? 100) ?>" min="1" max="100" required>
                    </div>
                </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Configuración</button>
    </form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
