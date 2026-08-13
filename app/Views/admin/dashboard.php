<?php
/**
 * Vista: Dashboard del Admin
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Estudiantes</h6>
                            <h2 class="mb-0"><?= number_format($estadisticas['total_estudiantes'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Docentes</h6>
                            <h2 class="mb-0"><?= number_format($estadisticas['total_docentes'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-person-badge-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Materias</h6>
                            <h2 class="mb-0"><?= number_format($estadisticas['total_materias'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-book-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Secciones Activas</h6>
                            <h2 class="mb-0"><?= number_format($estadisticas['total_secciones'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-door-open-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y tablas recientes -->
    <div class="row">
        <div class="col-lg-6 mb-4">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Grado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($estadisticas['ultimos_estudiantes'])): ?>
                                    <?php foreach ($estadisticas['ultimos_estudiantes'] as $est): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($est['nombre'] . ' ' . $est['apellido']) ?></td>
                                            <td><?= htmlspecialchars($est['grado_nombre'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($est['fecha_creacion'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No hay registros</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
        </div>

        <div class="col-lg-6 mb-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-warning"><?= $estadisticas['constancias_pendientes'] ?? 0 ?></h4>
                            <small>Pendientes</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success"><?= $estadisticas['constancias_aprobadas'] ?? 0 ?></h4>
                            <small>Aprobadas</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger"><?= $estadisticas['constancias_rechazadas'] ?? 0 ?></h4>
                            <small>Rechazadas</small>
                        </div>
                    </div>
        </div>
    </div>
