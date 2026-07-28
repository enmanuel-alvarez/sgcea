<?php
/**
 * Vista: Dashboard del Docente
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Panel del Docente</h1>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Mis Asignaciones</h6>
                            <h2 class="mb-0"><?= count($asignaciones ?? []) ?></h2>
                        </div>
                        <i class="bi bi-book" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Estudiantes</h6>
                            <h2 class="mb-0"><?= number_format($totalEstudiantes ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Actividades Evaluativas</h6>
                            <h2 class="mb-0"><?= number_format($actividadesEvaluativas ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-list-check" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Calificaciones Recientes</h6>
                            <h2 class="mb-0"><?= count($ultimasCalificaciones ?? []) ?></h2>
                        </div>
                        <i class="bi bi-pencil-square" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Asignaciones -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Asignaciones</h6>
                    <a href="/docente/calificaciones" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Gestionar Calificaciones
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Grado</th>
                                    <th>Sección</th>
                                    <th>Estudiantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($asignaciones)): ?>
                                    <?php foreach ($asignaciones as $asignacion): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($asignacion['materia_nombre']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['grado_nombre']) ?></td>
                                            <td><?= htmlspecialchars($asignacion['seccion_nombre']) ?></td>
                                            <td><?= $asignacion['total_estudiantes'] ?? 0 ?></td>
                                            <td>
                                                <a href="/docente/calificaciones/registrar/<?= $asignacion['id'] ?>" class="btn btn-sm btn-info" title="Calificaciones">
                                                    <i class="bi bi-star"></i>
                                                </a>
                                                <a href="/docente/asistencia/registrar/<?= $asignacion['id'] ?>" class="btn btn-sm btn-success" title="Asistencia">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <a href="/docente/plan-evaluacion/<?= $asignacion['id'] ?>" class="btn btn-sm btn-warning" title="Plan Evaluación">
                                                    <i class="bi bi-list-task"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">No tiene asignaciones activas</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Últimas Calificaciones</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($ultimasCalificaciones)): ?>
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($ultimasCalificaciones as $nota): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($nota['estudiante_nombre']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($nota['actividad_nombre']) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $nota['nota'] >= 70 ? 'success' : 'danger' ?>">
                                        <?= number_format($nota['nota'], 1) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No hay calificaciones recientes</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
