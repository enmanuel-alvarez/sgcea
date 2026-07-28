<?php
use Core\Security;
$titulo = 'Asignar Permisos';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="container-fluid py-4">
    <div class="row mb-3"><div class="col-12"><h2><i class="bi bi-shield-lock me-2"></i>Asignar Permisos a Usuario</h2></div></div>
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['flash_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Usuario: <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= htmlspecialchars($usuario['correo']) ?>)</h5></div>
    </div>
    <form method="POST" action="?route=admin/permisos/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <?php 
                    $grupos = [];
                    foreach ($todosPermisos as $p) {
                        $parte = explode('.', $p['codigo']);
                        $grupo = $parte[0];
                        if (!isset($grupos[$grupo])) $grupos[$grupo] = [];
                        $grupos[$grupo][] = $p;
                    }
                    foreach ($grupos as $grupo => $permisos): 
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light"><h6 class="mb-0 text-uppercase"><?= htmlspecialchars(ucfirst($grupo)) ?></h6></div>
                            <div class="card-body">
                                <?php foreach ($permisos as $p): 
                                    $checked = in_array($p['id'], $permisosUsuario) ? 'checked' : '';
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="<?= $p['id'] ?>" id="permiso_<?= $p['id'] ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="permiso_<?= $p['id'] ?>"><?= htmlspecialchars($p['descripcion']) ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="?route=admin/usuarios" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Permisos</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
