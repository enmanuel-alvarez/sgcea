<?php
use Core\Security;
$titulo = 'Editar Sección';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Sección</h1>
</div>

    <?php if (isset($errores)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    
    <form method="POST" action="?route=admin/secciones/actualizar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id" value="<?= $seccion['id'] ?>">
        <div class="border-0 mb-4">
            <div class="border-0 mb-4"><h5 class="mb-0"><i class="bi bi-door-open me-2"></i>Datos de la Sección</h5></div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($seccion['nombre']) ?>" required maxlength="50">
                    </div>
                    <div class="col-md-4">
                        <label for="capacidad_maxima" class="form-label">Capacidad Máxima</label>
                        <input type="number" class="form-control" id="capacidad_maxima" name="capacidad_maxima" value="<?= htmlspecialchars($seccion['capacidad_maxima'] ?? 35) ?>" min="1" max="100">
                    </div>
                    <div class="col-md-4">
                        <label for="activo" class="form-label">Estado *</label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1" <?= $seccion['activo'] == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $seccion['activo'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="200"><?= htmlspecialchars($seccion['descripcion'] ?? '') ?></textarea>
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
