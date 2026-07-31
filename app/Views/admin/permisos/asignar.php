<?php
use Core\Security;
$titulo = 'Asignar Permisos';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Asignar Permisos a Usuario</h1>
</div>
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['flash_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <div class="border-0 mb-4">
        <div class="border-0 mb-4"><h5 class="mb-0">Usuario: <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= htmlspecialchars($usuario['correo']) ?>)</h5></div>
    </div>
    <form method="POST" action="?route=admin/permisos/guardar">
        <input type="hidden" name="csrf_token" value="<?= Security::generarTokenCSRF() ?>">
        <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
        <div class="border-0 mb-4">
            <div class="border-0 mb-4">
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
                        <div class="border-0 mb-4">
                            <div class="border-0 mb-4"><h6 class="mb-0 text-uppercase"><?= htmlspecialchars(ucfirst($grupo)) ?></h6></div>
                            <div class="border-0 mb-4">
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
        <hr class="my-4">
<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="?route=admin/usuarios" class="btn btn-secondary me-md-2">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
</form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
