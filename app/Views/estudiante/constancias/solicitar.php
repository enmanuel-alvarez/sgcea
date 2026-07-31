<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Title</h1>
</div>
<div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Solicitar Constancia') ?></h4>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Solicitar Constancia') ?></h4>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Solicitar Constancia') ?></h4>


    

    
        
            <h6 class="m-0 font-weight-bold text-primary">Formulario de Solicitud</h6>
        </div>
        
            <form action="<?= url('/estudiante/constancias/guardar') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Constancia</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="estudio">Constancia de Estudio</option>
                        <option value="conducta">Constancia de Buena Conducta</option>
                        <option value="notas">Constancia de Notas</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo de la Solicitud</label>
                    <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                    <div class="form-text">Indique para qué trámite o institución necesita la constancia.</div>
                </div>

                <hr class="my-4">
<hr class="my-4">
<hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="#" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>
        </div>
    </div>
</div>

        </div>
    </div>
</div>
