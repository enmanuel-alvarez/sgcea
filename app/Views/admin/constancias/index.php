<?php
use Core\Security;
$titulo = 'Solicitudes de Constancias';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row mb-3"><div class="col-12"><h2><i class="bi bi-file-text me-2"></i>Solicitudes de Constancias</h2></div></div>
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['flash_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaConstancias" class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr><th>Estudiante</th><th>Tipo</th><th>Fecha Solicitud</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $sol): ?>
                        <tr>
                            <td><?= htmlspecialchars($sol['estudiante_nombre']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars(ucfirst($sol['tipo'])) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
                            <td>
                                <?php if ($sol['estado'] === 'pendiente'): ?>
                                    <span class="badge bg-warning">Pendiente</span>
                                <?php elseif ($sol['estado'] === 'aprobada'): ?>
                                    <span class="badge bg-success">Aprobada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sol['estado'] === 'pendiente'): ?>
                                    <button class="btn btn-sm btn-success" onclick="aprobar(<?= $sol['id'] ?>)"><i class="bi bi-check-lg"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="rechazar(<?= $sol['id'] ?>)"><i class="bi bi-x-lg"></i></button>
                                <?php elseif ($sol['estado'] === 'aprobada'): ?>
                                    <a href="?route=constancias/imprimir&id=<?= $sol['id'] ?>" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-printer"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<form id="formAprobar" method="POST" action="?route=admin/constancias/aprobar" style="display:none;"><input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>"><input type="hidden" name="id" id="idAprobar"></form>
<form id="formRechazar" method="POST" action="?route=admin/constancias/rechazar" style="display:none;"><input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>"><input type="hidden" name="id" id="idRechazar"><input type="hidden" name="motivo_rechazo" id="motivoRechazo"></form>
<script>
$(document).ready(function() { $('#tablaConstancias').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }, order: [[2, 'desc']] }); });
function aprobar(id) { if(confirm('¿Aprobar esta constancia?')) { document.getElementById('idAprobar').value = id; document.getElementById('formAprobar').submit(); } }
function rechazar(id) { var motivo = prompt('Motivo del rechazo:'); if(motivo) { document.getElementById('idRechazar').value = id; document.getElementById('motivoRechazo').value = motivo; document.getElementById('formRechazar').submit(); } }
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
