<?php
$titulo = 'Dashboard Estudiante';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row mb-3"><div class="col-12"><h2><i class="bi bi-speedometer2 me-2"></i>Mi Panel</h2></div></div>
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card bg-primary text-white h-100"><div class="card-body text-center"><h1 class="display-4"><?= $estadisticas['promedio_general'] ?? 'N/A' ?></h1><p class="mb-0">Promedio General</p></div></div></div>
        <div class="col-md-3"><div class="card bg-success text-white h-100"><div class="card-body text-center"><h1 class="display-4"><?= $estadisticas['asistencia_porcentaje'] ?? 'N/A' ?>%</h1><p class="mb-0">Asistencia</p></div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark h-100"><div class="card-body text-center"><h1 class="display-4"><?= $estadisticas['constancias_pendientes'] ?? 0 ?></h1><p class="mb-0">Constancias Pendientes</p></div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white h-100"><div class="card-body text-center"><h1 class="display-4"><?= htmlspecialchars($estudiante['grado_nombre'] ?? 'N/A') ?></h1><p class="mb-0">Grado/Sección</p></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card shadow-sm"><div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Accesos Rápidos</h5></div><div class="card-body"><div class="d-grid gap-2"><a href="?route=estudiante/boletin" class="btn btn-outline-primary"><i class="bi bi-journal-bookmark me-2"></i>Ver Boletín</a><a href="?route=estudiante/asistencia" class="btn btn-outline-success"><i class="bi bi-calendar-check me-2"></i>Mi Asistencia</a><a href="?route=estudiante/constancias/solicitar" class="btn btn-outline-info"><i class="bi bi-file-earmark-text me-2"></i>Solicitar Constancia</a></div></div></div></div>
        <div class="col-md-6"><div class="card shadow-sm"><div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Últimas Calificaciones</h5></div><div class="card-body"><ul class="list-group list-group-flush"><?php foreach ($ultimas_notas as $nota): ?><li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($nota['materia']) ?></span><span class="badge bg-<?= $nota['promedio'] >= 70 ? 'success' : 'danger' ?>"><?= number_format($nota['promedio'], 2) ?></span></li><?php endforeach; ?></ul></div></div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
