<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"></h1>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($titulo ?? 'Boletín de Calificaciones') ?></h1>
    <?php if (!empty($calificaciones)): ?>
    <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="bi bi-printer fa-sm text-white-50"></i> Imprimir Boletín
    </button>
    <?php endif; ?>
</div>

    

    <div class="row mb-4">
        <div class="col-md-6">
            <p><strong>Estudiante:</strong> <?= e($estudiante['nombres'] ?? '') ?> <?= e($estudiante['apellidos'] ?? '') ?></p>
            <p><strong>Cédula:</strong> <?= e($estudiante['cedula'] ?? '') ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Grado:</strong> <?= e($inscripcion['grado'] ?? 'N/A') ?></p>
            <p><strong>Sección:</strong> <?= e($inscripcion['seccion'] ?? 'N/A') ?></p>
        </div>
    </div>

    <?php if (empty($calificaciones)): ?>
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-info-circle me-2"></i> No hay calificaciones registradas.
        </div>
    <?php else: ?>
        <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Materia</th>
                                <th>Evaluaciones</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calificaciones as $cal): ?>
                            <tr>
                                <td><?= e($cal['materia'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($cal['evaluaciones'])): ?>
                                        <ul class="list-unstyled mb-0">
                                        <?php foreach ($cal['evaluaciones'] as $eval): ?>
                                            <li><?= e($eval['nombre']) ?>: <?= e($eval['nota']) ?> (<?= e($eval['ponderacion']) ?>%)</li>
                                        <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <span class="text-muted">Sin evaluaciones</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= e($cal['promedio'] ?? 'N/A') ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
</div>
    <?php endif; ?>

<style>
@media print {
    body * { visibility: hidden; }
    .container-fluid, .container-fluid * { visibility: visible; }
    .container-fluid { position: absolute; left: 0; top: 0; width: 100%; }
    .btn { display: none !important; }
}
</style>
