<?php
/**
 * Vista: Admin - Editar Estudiante
 */
use Core\Security;

$titulo = 'Editar Estudiante';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Estudiante</h1>
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

    

    <form method="POST" action="?route=admin/estudiantes/actualizar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id" value="<?= $estudiante['id'] ?>">
        
        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Datos Personales</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="cedula" class="form-label">Cédula *</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" 
                               value="<?= htmlspecialchars($estudiante['cedula']) ?>" required maxlength="20">
                    </div>
                    <div class="col-md-4">
                        <label for="nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" 
                               value="<?= htmlspecialchars($estudiante['nombres']) ?>" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                               value="<?= htmlspecialchars($estudiante['apellidos']) ?>" required maxlength="100">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento *</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                               value="<?= htmlspecialchars($estudiante['fecha_nacimiento']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="genero" class="form-label">Género *</label>
                        <select class="form-select" id="genero" name="genero" required>
                            <option value="">Seleccione</option>
                            <option value="M" <?= $estudiante['genero'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $estudiante['genero'] === 'F' ? 'selected' : '' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" 
                               value="<?= htmlspecialchars($estudiante['direccion'] ?? '') ?>" maxlength="200">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?= htmlspecialchars($estudiante['telefono'] ?? '') ?>" maxlength="20">
                    </div>
                    <div class="col-md-5">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" 
                               value="<?= htmlspecialchars($estudiante['correo'] ?? '') ?>" maxlength="100">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Datos Académicos</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="id_grado" class="form-label">Grado *</label>
                        <select class="form-select" id="id_grado" name="id_grado" required>
                            <option value="">Seleccione Grado</option>
                            <?php foreach ($grados as $grado): ?>
                                <option value="<?= $grado['id'] ?>" <?= $estudiante['id_grado'] == $grado['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($grado['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="id_seccion" class="form-label">Sección *</label>
                        <select class="form-select" id="id_seccion" name="id_seccion" required>
                            <option value="">Seleccione Sección</option>
                            <?php foreach ($secciones as $seccion): ?>
                                <option value="<?= $seccion['id'] ?>" <?= $estudiante['id_seccion'] == $seccion['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($seccion['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="ano_ingreso" class="form-label">Año de Ingreso *</label>
                        <input type="number" class="form-control" id="ano_ingreso" name="ano_ingreso" 
                               value="<?= htmlspecialchars($estudiante['ano_ingreso']) ?>" min="2000" max="<?= date('Y') + 1 ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="activo" class="form-label">Estado *</label>
                        <select class="form-select" id="activo" name="activo" required>
                            <option value="1" <?= $estudiante['activo'] == 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $estudiante['activo'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Datos del Representante</h5>
            </div>
            <div class="border-0 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nombre_representante" class="form-label">Nombre Representante *</label>
                        <input type="text" class="form-control" id="nombre_representante" name="nombre_representante" 
                               value="<?= htmlspecialchars($estudiante['nombre_representante']) ?>" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label for="cedula_representante" class="form-label">Cédula Representante *</label>
                        <input type="text" class="form-control" id="cedula_representante" name="cedula_representante" 
                               value="<?= htmlspecialchars($estudiante['cedula_representante']) ?>" required maxlength="20">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono_representante" class="form-label">Teléfono Representante *</label>
                        <input type="tel" class="form-control" id="telefono_representante" name="telefono_representante" 
                               value="<?= htmlspecialchars($estudiante['telefono_representante']) ?>" required maxlength="20">
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="?route=admin/estudiantes" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>


<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
