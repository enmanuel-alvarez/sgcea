<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Inscribir Estudiante</h1>
    <a href="<?= url('/admin/estudiantes') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="bi bi-arrow-left fa-sm text-white-50"></i> Volver a Estudiantes
    </a>
</div>

    

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Datos del Estudiante</h6>
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> <?= e($estudiante['nombre'] ?? '') ?> <?= e($estudiante['apellido'] ?? '') ?></p>
            <p><strong>Cédula:</strong> <?= e($estudiante['cedula'] ?? '') ?></p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nueva Inscripción</h6>
        </div>
        <div class="card-body">
            <form action="<?= url('/admin/estudiantes/inscribir') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="estudiante_id" value="<?= e($estudiante['id'] ?? '') ?>">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="grado_id" class="form-label">Grado</label>
                        <select class="form-select" id="grado_id" name="grado_id" required>
                            <option value="">Seleccione un grado</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= e($grado['id']) ?>"><?= e($grado['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="seccion_id" class="form-label">Sección</label>
                        <select class="form-select" id="seccion_id" name="seccion_id" required>
                            <option value="">Seleccione una sección</option>
                            <?php foreach ($secciones as $seccion): ?>
                                <option value="<?= e($seccion['id']) ?>" data-grado="<?= e($seccion['grado_id']) ?>"><?= e($seccion['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="ano_academico" class="form-label">Año Académico</label>
                        <input type="text" class="form-control" id="ano_academico" name="ano_academico" value="2024-2025" required>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Inscribir Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradoSelect = document.getElementById('grado_id');
    const seccionSelect = document.getElementById('seccion_id');
    const allSecciones = Array.from(seccionSelect.options).filter(opt => opt.value !== '');

    gradoSelect.addEventListener('change', function() {
        const gradoId = this.value;
        
        // Reset section select
        seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';
        
        if (gradoId) {
            allSecciones.forEach(opt => {
                if (opt.dataset.grado === gradoId) {
                    seccionSelect.appendChild(opt.cloneNode(true));
                }
            });
        }
    });
});
</script>
