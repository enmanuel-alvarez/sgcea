<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"></h1>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($titulo ?? 'Plan de Evaluación') ?></h1>
</div>

    

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Asignación</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Materia:</strong> <?= e($asignacion['materia'] ?? '') ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Grado:</strong> <?= e($asignacion['grado'] ?? '') ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Sección:</strong> <?= e($asignacion['seccion'] ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Evaluaciones Programadas</h6>
                    <?php 
                        $totalPonderacion = 0;
                        if (!empty($planes)) {
                            foreach ($planes as $p) {
                                $totalPonderacion += (float)$p['ponderacion'];
                            }
                        }
                    ?>
                    <span class="badge <?= $totalPonderacion == 100 ? 'bg-success' : 'bg-warning text-dark' ?>">
                        Total Ponderación: <?= $totalPonderacion ?>%
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($totalPonderacion != 100): ?>
                        <div class="alert alert-warning shadow-sm">
                            <i class="bi bi-exclamation-triangle"></i> La suma de las ponderaciones debe ser exactamente 100%.
                        </div>
                    <?php endif; ?>

                    <?php if (empty($planes)): ?>
                        <div class="alert alert-info">No hay evaluaciones registradas.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Ponderación %</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planes as $p): ?>
                                    <tr>
                                        <td><?= e($p['nombre']) ?></td>
                                        <td><?= e(ucfirst($p['tipo'])) ?></td>
                                        <td><?= e($p['ponderacion']) ?>%</td>
                                        <td><?= e($p['fecha_programada']) ?></td>
                                        <td>
                                            <?php if (($p['estado'] ?? '') == 'realizada'): ?>
                                                <span class="badge bg-success">Realizada</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Agregar Evaluación</h6>
                </div>
                <div class="card-body">
                    <form action="<?= url('/docente/planevaluacion/guardar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="asignacion_id" value="<?= e($asignacion['id'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de Evaluación</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione</option>
                                <option value="examen">Examen</option>
                                <option value="tarea">Tarea</option>
                                <option value="proyecto">Proyecto</option>
                                <option value="participacion">Participación</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ponderacion" class="form-label">Ponderación (%)</label>
                            <input type="number" class="form-control" id="ponderacion" name="ponderacion" min="1" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_programada" class="form-label">Fecha Programada</label>
                            <input type="date" class="form-control" id="fecha_programada" name="fecha_programada" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" <?= $totalPonderacion >= 100 ? 'disabled' : '' ?>>
                                <i class="bi bi-plus-circle"></i> Agregar al Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
