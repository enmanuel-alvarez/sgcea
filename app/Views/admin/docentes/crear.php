<?php
/**
 * Vista: Admin - Crear Docente
 */
use Core\Security;

$titulo = 'Nuevo Docente';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nuevo Docente</h1>
</div>

    <?php if (isset($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    

    <form method="POST" action="?route=admin/docentes/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        
        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Datos Personales</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="cedula" class="form-label">Cédula *</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required maxlength="20">
                    </div>
                    <div class="col-md-4">
                        <label for="nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento *</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>
                    <div class="col-md-3">
                        <label for="genero" class="form-label">Género *</label>
                        <select class="form-select" id="genero" name="genero" required>
                            <option value="">Seleccione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" maxlength="200">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono *</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" required maxlength="20">
                    </div>
                    <div class="col-md-5">
                        <label for="correo" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="correo" name="correo" required maxlength="100">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-book me-2"></i>Datos Académicos</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="especialidad" class="form-label">Especialidad *</label>
                        <input type="text" class="form-control" id="especialidad" name="especialidad" 
                               placeholder="Ej: Matemáticas, Ciencias, Lengua..." required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label for="titulo_obtenido" class="form-label">Título Obtenido *</label>
                        <input type="text" class="form-control" id="titulo_obtenido" name="titulo_obtenido" 
                               placeholder="Ej: Licenciado en Educación, Ingeniero..." required maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                        <input type="number" class="form-control" id="anos_experiencia" name="anos_experiencia" 
                               min="0" max="50" value="0">
                    </div>
                    <div class="col-md-8">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="?route=admin/docentes" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>


<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
