<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"></h1>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($titulo ?? 'Reporte de Rendimiento') ?></h1>
</div>

    

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card border-left-primary shadow h-100 py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Promedio General</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-promedio">0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card border-left-success shadow h-100 py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">% Aprobados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-aprobados">0%</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card border-left-danger shadow h-100 py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">% Reprobados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-reprobados">0%</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <form id="filtro-form" class="row g-3">
            <div class="col-md-3">
                <label for="grado_id" class="form-label">Grado</label>
                <select class="form-select" id="grado_id" name="grado_id">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="seccion_id" class="form-label">Sección</label>
                <select class="form-select" id="seccion_id" name="seccion_id">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="materia_id" class="form-label">Materia</label>
                <select class="form-select" id="materia_id" name="materia_id">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="periodo" class="form-label">Periodo</label>
                <select class="form-select" id="periodo" name="periodo">
                    <option value="">Todos</option>
                    <option value="1">1er Lapso</option>
                    <option value="2">2do Lapso</option>
                    <option value="3">3er Lapso</option>
                </select>
            </div>
            <div class="col-12 mt-3 text-end">
                <button type="button" class="btn btn-primary" id="btn-buscar">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Promedio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via JS/AJAX -->
            </tbody>
        </table>
    </div>
    

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#dataTable').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' }
        });
    }
});
</script>
