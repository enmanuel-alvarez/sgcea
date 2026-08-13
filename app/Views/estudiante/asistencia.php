<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"></h1>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($titulo ?? 'Historial de Asistencia') ?></h1>
</div>

    

    <?php if (empty($asistencias)): ?>
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-info-circle me-2"></i> No hay registros de asistencia.
        </div>
    <?php else: ?>
        <?php 
            $total = count($asistencias);
            $presentes = 0; $ausentes = 0; $tardanzas = 0;
            foreach ($asistencias as $a) {
                if ($a['estado'] == 'Presente') $presentes++;
                elseif ($a['estado'] == 'Ausente') $ausentes++;
                elseif ($a['estado'] == 'Tardanza') $tardanzas++;
            }
            $porcentaje = $total > 0 ? round(($presentes / $total) * 100, 2) : 0;
        ?>
        <div class="row">
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="stat-card border-left-primary shadow h-100 py-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clases</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="stat-card border-left-success shadow h-100 py-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Presentes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $presentes ?></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-4">
                <div class="stat-card border-left-danger shadow h-100 py-2">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ausentes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $ausentes ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card border-left-warning shadow h-100 py-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tardanzas</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $tardanzas ?></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card border-left-info shadow h-100 py-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">% Asistencia</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $porcentaje ?>%</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
                    <table class="table table-striped table-sm" id="dataTable">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Materia</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asistencias as $a): ?>
                                <tr>
                                    <td><?= e($a['fecha']) ?></td>
                                    <td><?= e($a['materia']) ?></td>
                                    <td>
                                        <?php if ($a['estado'] == 'Presente'): ?>
                                            <span class="badge bg-success">Presente</span>
                                        <?php elseif ($a['estado'] == 'Ausente'): ?>
                                            <span class="badge bg-danger">Ausente</span>
                                        <?php elseif ($a['estado'] == 'Tardanza'): ?>
                                            <span class="badge bg-warning text-dark">Tardanza</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= e($a['estado']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($a['observaciones'] ?? '') ?></td>
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
