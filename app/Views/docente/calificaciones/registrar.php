<?php
use Core\Security;
$titulo = 'Registrar Calificaciones';
require_once __DIR__ . '/../../../layouts/header.php';
require_once __DIR__ . '/../../../layouts/sidebar.php';
?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    
            <a href="?route=docente/calificaciones" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        </div>
    </div>
    <form method="POST" action="?route=docente/calificaciones/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id_asignacion" value="<?= $asignacion['id'] ?>">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Estudiante</th>
                                <?php foreach ($evaluaciones as $ev): ?>
                                <th class="text-center" width="100"><?= htmlspecialchars($ev['nombre']) ?> (<?= $ev['porcentaje'] ?>%)</th>
                                <?php endforeach; ?>
                                <th class="text-center" width="80">Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudiantes as $est): 
                                $notasEst = array_filter($notas, fn($n) => $n['id_estudiante'] == $est['id']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($est['nombres'] . ' ' . $est['apellidos']) ?></td>
                                <?php foreach ($evaluaciones as $ev): 
                                    $notaExist = current(array_filter($notasEst, fn($n) => $n['id_plan'] == $ev['id']));
                                    $valor = $notaExist ? $notaExist['nota'] : '';
                                ?>
                                <td><input type="number" class="form-control text-center" name="notas[<?= $est['id'] ?>][<?= $ev['id'] ?>]" value="<?= htmlspecialchars($valor) ?>" min="0" max="100" step="0.01"></td>
                                <?php endforeach; ?>
                                <td class="text-center fw-bold"><?= number_format($est['promedio'] ?? 0, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Calificaciones</button>
    </form>

<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
