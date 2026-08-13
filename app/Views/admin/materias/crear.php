<?php
/**
 * Vista: Admin - Crear Materia
 */
use Core\Security;

$titulo = 'Nueva Materia';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>


    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    
        </div>
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

    <form method="POST" action="?route=admin/materias/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        
        <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="codigo" class="form-label">Código *</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                               placeholder="Ej: MAT-001" required maxlength="20">
                    </div>
                    <div class="col-md-8">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               placeholder="Ej: Matemáticas" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label for="id_grado" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado" name="id_grado" required>
                            <option value="">Seleccione Grado</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= $grado['id'] ?>"><?= htmlspecialchars($grado['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="horas_semanales" class="form-label">Horas Semanales *</label>
                        <input type="number" class="form-control" id="horas_semanales" name="horas_semanales" 
                               min="1" max="20" value="5" required>
                    </div>
                    <div class="col-md-3">
                        <label for="activo" class="form-label">Estado *</label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="?route=admin/materias" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Guardar Materia
            </button>
        </div>
    </form>


<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
