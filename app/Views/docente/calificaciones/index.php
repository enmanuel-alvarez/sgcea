<?php
use Core\Security;
$titulo = 'Gestión de Calificaciones';
require_once __DIR__ . '/../../../layouts/header.php';
require_once __DIR__ . '/../../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Calificaciones</h1>
</div>
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
                            <td><a href="?route=docente/calificaciones/registrar&id_asignacion=<?= $asn['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil me-1"></i>Registrar Notas</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>$(document).ready(function() { $('#tablaAsignaciones').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' } }); });</script>
<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
