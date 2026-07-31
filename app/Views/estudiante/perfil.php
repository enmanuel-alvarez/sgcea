<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Title</h1>
</div>
<div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Mi Perfil') ?></h4>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Mi Perfil') ?></h4>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h4 class="mb-3"><?= e($titulo ?? 'Mi Perfil') ?></h4>


    

    <div class="row">
        <div class="col-md-4 mb-4">
            
                
                    <img src="<?= asset('img/undraw_profile.svg') ?>" alt="Avatar" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    <h5 class="font-weight-bold"><?= e($estudiante['nombres'] ?? '') ?> <?= e($estudiante['apellidos'] ?? '') ?></h5>
                    <p class="text-muted mb-1">Cédula: <?= e($estudiante['cedula'] ?? '') ?></p>
                    <p class="text-muted">Usuario: <?= e($usuario['username'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            
                
                    <h6 class="m-0 font-weight-bold text-primary">Actualizar Información</h6>
                </div>
                
                    <form action="<?= url('/estudiante/perfil/actualizar') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= e($estudiante['telefono'] ?? '') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="telefono_representante" class="form-label">Teléfono del Representante</label>
                                <input type="text" class="form-control" id="telefono_representante" name="telefono_representante" value="<?= e($estudiante['telefono_representante'] ?? '') ?>">
                            </div>

                            <div class="col-12 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="3"><?= e($estudiante['direccion'] ?? '') ?></textarea>
                            </div>
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
