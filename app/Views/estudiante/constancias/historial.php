<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"></h1>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($titulo ?? 'Historial de Constancias') ?></h1>
</div>

    

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-info-circle me-2"></i> No tienes solicitudes de constancias.
        </div>
    <?php else: ?>
        <div class="table-responsive">
                    <table class="table table-striped table-sm" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Fecha Solicitud</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $s): ?>
                                <tr>
                                    <td><?= e($s['id']) ?></td>
                                    <td><?= ucfirst(e($s['tipo'])) ?></td>
                                    <td><?= e($s['motivo']) ?></td>
                                    <td><?= e($s['fecha_solicitud']) ?></td>
                                    <td>
                                        <?php if ($s['estado'] == 'aprobada'): ?>
                                            <span class="badge bg-success">Aprobada</span>
                                        <?php elseif ($s['estado'] == 'rechazada'): ?>
                                            <span class="badge bg-danger">Rechazada</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($s['estado'] == 'aprobada'): ?>
                                            <a href="<?= url('/constancias/imprimir/' . $s['id']) ?>" class="btn btn-sm btn-primary" target="_blank" title="Imprimir">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#dataTable').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
            order: [[0, 'desc']]
        });
    }
});
</script>
