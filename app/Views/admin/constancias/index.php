<?php
use Core\Security;
$basePath = defined('BASE_PATH') ? BASE_PATH : '/sgcea/public';
$titulo = 'Solicitudes de Constancias';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Solicitudes de Constancias</h1>
</div>
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['flash_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    
            <div class="table-responsive">
                <table id="tablaConstancias" class="table table-striped table-sm">
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
                                    <a href="<?= $basePath ?>/constancias/imprimir/<?= $sol['id'] ?>" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-printer"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<form id="formAprobar" method="POST" action="" style="display:none;"><input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>"></form>
<form id="formRechazar" method="POST" action="" style="display:none;"><input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>"><input type="hidden" name="motivo_rechazo" id="motivoRechazo"></form>
<script>
$(document).ready(function() { $('#tablaConstancias').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }, order: [[2, 'desc']] }); });
function aprobar(id) { if(confirm('¿Aprobar esta constancia?')) { var form = document.getElementById('formAprobar'); form.action = '<?= $basePath ?>/admin/constancias/aprobar/' + id; form.submit(); } }
function rechazar(id) { var motivo = prompt('Motivo del rechazo:'); if(motivo) { document.getElementById('motivoRechazo').value = motivo; var form = document.getElementById('formRechazar'); form.action = '<?= $basePath ?>/admin/constancias/rechazar/' + id; form.submit(); } }
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
