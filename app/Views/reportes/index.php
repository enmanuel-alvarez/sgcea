<?php
use Core\Security;
$basePath = defined('BASE_PATH') ? BASE_PATH : '/sgcea/public';
$titulo = 'Reportes';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reportes Estadísticos</h1>
</div>
<div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-people me-2"></i>Estudiantes</h5></div>
                <div class="card-body">
                    <p class="text-muted">Total de estudiantes matriculados por grado y sección.</p>
                    <a href="<?= $basePath ?>/reportes?tipo=estudiantes" class="btn btn-outline-primary w-100"><i class="bi bi-eye me-1"></i>Ver Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Rendimiento Académico</h5></div>
                <div class="card-body">
                    <p class="text-muted">Análisis de calificaciones por materia y período.</p>
                    <a href="<?= $basePath ?>/reportes/rendimiento" class="btn btn-outline-success w-100"><i class="bi bi-eye me-1"></i>Ver Reporte</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Asistencia</h5></div>
                <div class="card-body">
                    <p class="text-muted">Resumen de asistencia por estudiante y período.</p>
                    <a href="<?= $basePath ?>/reportes/asistencia" class="btn btn-outline-info w-100"><i class="bi bi-eye me-1"></i>Ver Reporte</a>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
