<?php
use Core\Security;
$titulo = 'Nueva Asignación';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    </div></div>
    <?php if (isset($errores)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="POST" action="?route=admin/asignaciones/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Datos de la Asignación</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="id_profesor" class="form-label">Docente *</label>
                        <select class="form-select" id="id_profesor" name="id_profesor" required>
                            <option value="">Seleccione Docente</option>
                            <?php foreach ($docentes as $doc): ?>
                                <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['nombres'] . ' ' . $doc['apellidos']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_materia" class="form-label">Materia *</label>
                        <select class="form-select" id="id_materia" name="id_materia" required>
                            <option value="">Seleccione Materia</option>
                            <?php foreach ($materias as $mat): ?>
                                <option value="<?= $mat['id'] ?>"><?= htmlspecialchars($mat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="id_grado" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado" name="id_grado" required>
                            <option value="">Seleccione Grado</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= $grado['id'] ?>"><?= htmlspecialchars($grado['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="id_seccion" class="form-label">Sección *</label>
                        <select class="form-select" id="id_seccion" name="id_seccion" required>
                            <option value="">Seleccione Sección</option>
                            <?php foreach ($secciones as $sec): ?>
                                <option value="<?= $sec['id'] ?>"><?= htmlspecialchars($sec['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="ano_academico" class="form-label">Año Académico *</label>
                        <input type="text" class="form-control" id="ano_academico" name="ano_academico" 
                               value="<?= date('Y') ?>" maxlength="9" placeholder="Ej: 2024-2025" required>
                    </div>
                    <div class="col-md-12">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" maxlength="300"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="?route=admin/asignaciones" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Asignación</button>
        </div>
    </form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
