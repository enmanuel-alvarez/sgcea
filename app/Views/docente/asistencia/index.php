<?php
use Core\Security;
$titulo = 'Gestión de Asistencia';
require_once __DIR__ . '/../../../layouts/header.php';
require_once __DIR__ . '/../../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row mb-3"><div class="col-12"><h2><i class="bi bi-calendar-check me-2"></i>Gestión de Asistencia</h2></div></div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaAsignaciones" class="table table-hover">
                    <thead class="table-dark"><tr><th>Materia</th><th>Grado</th><th>Sección</th><th>Año</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($asignaciones as $asn): ?>
                        <tr>
                            <td><?= htmlspecialchars($asn['materia_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['grado_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['seccion_nombre']) ?></td>
                            <td><?= htmlspecialchars($asn['ano_academico']) ?></td>
                            <td><a href="?route=docente/asistencia/registrar&id_asignacion=<?= $asn['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-person-check me-1"></i>Tomar Asistencia</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>$(document).ready(function() { $('#tablaAsignaciones').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' } }); });</script>
<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
