<?php
/**
 * Vista: Dashboard del Estudiante
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">
                Bienvenido, <?= htmlspecialchars($estudiante['nombre'] ?? 'Estudiante') ?>
            </h1>
        </div>
    </div>

    <!-- Información del estudiante -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0"><i class="bi bi-person-circle"></i> Mi Información</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nombre Completo</label>
                            <p class="mb-0"><?= htmlspecialchars(($estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellido'] ?? '')) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Cédula</label>
                            <p class="mb-0"><?= htmlspecialchars($estudiante['cedula'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Grado Actual</label>
                            <p class="mb-0">
                                <?= htmlspecialchars($inscripcion['grado_nombre'] ?? 'N/A') ?> 
                                - Sección <?= htmlspecialchars($inscripcion['seccion_nombre'] ?? 'N/A') ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Año Académico</label>
                            <p class="mb-0"><?= $inscripcion['ano_academico'] ?? date('Y') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Mi Rendimiento</h4>
                    <div class="row mt-4">
                        <div class="col-6">
                            <h2 class="text-<?= ($promedioGeneral ?? 0) >= 70 ? 'success' : 'warning' ?>">
                                <?= number_format($promedioGeneral ?? 0, 1) ?>
                            </h2>
                            <small class="text-muted">Promedio General</small>
                        </div>
                        <div class="col-6">
                            <h2 class="text-<?= ($porcentajeAsistencia ?? 0) >= 70 ? 'success' : 'danger' ?>">
                                <?= number_format($porcentajeAsistencia ?? 0, 0) ?>%
                            </h2>
                            <small class="text-muted">Asistencia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <a href="/estudiante/boletin" class="card text-white bg-info h-100 text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2">Mi Boletín</h5>
                    <small>Ver calificaciones</small>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="/estudiante/asistencia" class="card text-white bg-success h-100 text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2">Mi Asistencia</h5>
                    <small>Ver historial</small>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="/estudiante/constancias/solicitar" class="card text-white bg-warning h-100 text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-check" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2">Constancias</h5>
                    <small>Solicitar/Descargar</small>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="/estudiante/perfil" class="card text-white bg-secondary h-100 text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2">Mi Perfil</h5>
                    <small>Editar datos</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Últimas notas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimas Calificaciones</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Actividad</th>
                                    <th>Nota</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ultimasNotas)): ?>
                                    <?php foreach ($ultimasNotas as $nota): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($nota['materia_nombre']) ?></td>
                                            <td><?= htmlspecialchars($nota['actividad_nombre']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $nota['nota'] >= 70 ? 'success' : 'danger' ?>">
                                                    <?= number_format($nota['nota'], 1) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($nota['fecha_registro'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">No hay calificaciones registradas</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
