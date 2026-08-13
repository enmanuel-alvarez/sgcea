<?php
$titulo = 'Dashboard Docente';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel del Docente</h1>
</div>
<div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white h-100 text-center">
                <h1 class="display-4"><?= $estadisticas['total_asignaciones'] ?? 0 ?></h1>
                <p class="mb-0">Asignaciones Activas</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success text-white h-100 text-center">
                <h1 class="display-4"><?= $estadisticas['total_estudiantes'] ?? 0 ?></h1>
                <p class="mb-0">Estudiantes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-warning text-dark h-100 text-center">
                <h1 class="display-4"><?= $estadisticas['calificaciones_pendientes'] ?? 0 ?></h1>
                <p class="mb-0">Notas por Registrar</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-info text-white h-100 text-center">
                <h1 class="display-4"><?= $estadisticas['promedio_general'] ?? 'N/A' ?></h1>
                <p class="mb-0">Promedio General</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-group list-group-flush">
                <?php foreach ($asignaciones as $asn): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($asn['materia_nombre']) ?> - <?= htmlspecialchars($asn['grado_nombre']) ?> <?= htmlspecialchars($asn['seccion_nombre']) ?></span>
                    <a href="?route=docente/calificaciones/registrar&id_asignacion=<?= $asn['id'] ?>" class="btn btn-sm btn-primary">Gestionar</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-6">
            <div class="d-grid gap-2">
                <a href="?route=docente/calificaciones" class="btn btn-outline-primary"><i class="bi bi-pencil-square me-2"></i>Registrar Calificaciones</a>
                <a href="?route=docente/asistencia" class="btn btn-outline-success"><i class="bi bi-person-check me-2"></i>Tomar Asistencia</a>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
