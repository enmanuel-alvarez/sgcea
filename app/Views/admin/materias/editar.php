<?php
/**
 * Vista: Admin - Editar Materia
 */
use Core\Security;

$titulo = 'Editar Materia';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Materia</h1>
</div>

    <?php if (isset($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    

    <form method="POST" action="?route=admin/materias/actualizar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id" value="<?= $materia['id'] ?>">
        
        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-book me-2"></i>Datos de la Materia</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="codigo" class="form-label">Código *</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                               value="<?= htmlspecialchars($materia['codigo']) ?>" required maxlength="20">
                    </div>
                    <div class="col-md-8">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($materia['nombre']) ?>" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label for="id_grado" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado" name="id_grado" required>
                            <option value="">Seleccione Grado</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= $grado['id'] ?>" <?= $materia['id_grado'] == $grado['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($grado['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="horas_semanales" class="form-label">Horas Semanales *</label>
                        <input type="number" class="form-control" id="horas_semanales" name="horas_semanales" 
                               value="<?= htmlspecialchars($materia['horas_semanales']) ?>" min="1" max="20" required>
                    </div>
                    <div class="col-md-3">
                        <label for="activo" class="form-label">Estado *</label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1" <?= $materia['activo'] == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $materia['activo'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500"><?= htmlspecialchars($materia['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="?route=admin/materias" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>


<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
