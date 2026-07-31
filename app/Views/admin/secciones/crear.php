<?php
use Core\Security;
$titulo = 'Nueva Sección';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nueva Sección</h1>
</div>

    <?php if (isset($errores)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    
    <form method="POST" action="?route=admin/secciones/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <div class="border-0 mb-4">
            <div class="border-0 mb-4"><h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Datos de la Sección</h5></div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: A, B, Única" required maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label for="capacidad_maxima" class="form-label">Capacidad Máxima</label>
                        <input type="number" class="form-control" id="capacidad_maxima" name="capacidad_maxima" min="1" max="100" value="35">
                    </div>
                    <div class="col-md-4">
                        <label for="activo" class="form-label">Estado *</label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="200"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="?route=admin/secciones" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
