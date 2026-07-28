<?php
use Core\Security;
$titulo = 'Registrar Asistencia';
require_once __DIR__ . '/../../../layouts/header.php';
require_once __DIR__ . '/../../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="bi bi-person-check me-2"></i>Asistencia - <?= htmlspecialchars($asignacion['materia_nombre']) ?></h2>
            <a href="?route=docente/asistencia" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        </div>
    </div>
    <form method="POST" action="?route=docente/asistencia/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id_asignacion" value="<?= $asignacion['id'] ?>">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <label for="fecha" class="form-label fw-bold mb-0">Fecha:</label>
                <input type="date" class="form-control d-inline-block w-auto ms-2" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light"><tr><th>Estudiante</th><th class="text-center">Estado</th></tr></thead>
                        <tbody>
                            <?php foreach ($estudiantes as $est): 
                                $asistExist = current(array_filter($asistencias ?? [], fn($a) => $a['id_estudiante'] == $est['id']));
                                $estado = $asistExist ? $asistExist['estado'] : 'presente';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']) ?></td>
                                <td class="text-center">
                                    <select class="form-select d-inline-block w-auto" name="asistencia[<?= $est['id'] ?>]">
                                        <option value="presente" <?= $estado === 'presente' ? 'selected' : '' ?>>Presente</option>
                                        <option value="ausente" <?= $estado === 'ausente' ? 'selected' : '' ?>>Ausente</option>
                                        <option value="tarde" <?= $estado === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                                        <option value="justificado" <?= $estado === 'justificado' ? 'selected' : '' ?>>Justificado</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Guardar Asistencia</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
