<?php
/**
 * Vista: Admin - Asignar Permisos ACL Compacto en Menús Desplegables (Tailwind CSS v3)
 */
$titulo = 'Asignar Permisos ACL';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

// Agrupar permisos por Vista / Módulo
$vistas = [];
foreach ($todosPermisos as $p) {
    $nombre = $p['nombre'] ?? '';
    $partes = explode('.', $nombre);
    
    $modulo = $p['modulo'] ?? $partes[0] ?? 'general';
    $seccion = $partes[1] ?? 'general';

    $vistaKey = $modulo . '_' . $seccion;
    $vistaTitulo = ucfirst($modulo) . ' - ' . ucfirst($seccion);
    
    if ($modulo === 'admin') {
        $nombresVistas = [
            'admin_dashboard' => 'Dashboard Administrativo',
            'admin_usuarios' => 'Vista: Gestión de Usuarios',
            'admin_estudiantes' => 'Vista: Gestión de Estudiantes',
            'admin_docentes' => 'Vista: Gestión de Docentes',
            'admin_materias' => 'Vista: Gestión de Materias',
            'admin_secciones' => 'Vista: Gestión de Secciones',
            'admin_asignaciones' => 'Vista: Asignaciones Académicas',
            'admin_constancias' => 'Vista: Solicitudes de Constancias',
            'admin_permisos' => 'Vista: Control de Permisos ACL',
            'admin_configuracion' => 'Vista: Configuración del Sistema',
            'admin_backup' => 'Vista: Copias de Seguridad (JSON)'
        ];
        if (isset($nombresVistas[$vistaKey])) $vistaTitulo = $nombresVistas[$vistaKey];
    } elseif ($modulo === 'docente') {
        $nombresVistas = [
            'docente_dashboard' => 'Dashboard Docente',
            'docente_calificaciones' => 'Vista: Carga de Calificaciones',
            'docente_asistencia' => 'Vista: Tomador de Asistencias',
            'docente_planevaluacion' => 'Vista: Plan de Evaluación'
        ];
        if (isset($nombresVistas[$vistaKey])) $vistaTitulo = $nombresVistas[$vistaKey];
    } elseif ($modulo === 'estudiante') {
        $nombresVistas = [
            'estudiante_dashboard' => 'Dashboard Estudiante',
            'estudiante_boletin' => 'Vista: Boletín de Notas',
            'estudiante_asistencia' => 'Vista: Historial de Asistencias',
            'estudiante_constancias' => 'Vista: Solicitud de Constancias',
            'estudiante_perfil' => 'Vista: Mi Perfil Estudiantil'
        ];
        if (isset($nombresVistas[$vistaKey])) $vistaTitulo = $nombresVistas[$vistaKey];
    }

    if (!isset($vistas[$vistaKey])) {
        $vistas[$vistaKey] = [
            'clave' => $vistaKey,
            'titulo' => $vistaTitulo,
            'modulo' => $modulo,
            'permisos' => []
        ];
    }
    $vistas[$vistaKey]['permisos'][] = $p;
}
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Matriz de Permisos por Vista</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Configuración individual para <strong class="text-blue-600 dark:text-blue-400 font-bold"><?= htmlspecialchars($usuario['nombre_completo'] ?? ($usuario['nombre'] . ' ' . $usuario['apellido'])) ?></strong> (<?= htmlspecialchars($usuario['correo'] ?? $usuario['email'] ?? '') ?>).
        </p>
    </div>
    <div>
        <a href="<?= url('/admin/usuarios') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a Usuarios</span>
        </a>
    </div>
</div>

<form method="POST" action="<?= url('/admin/permisos/guardar/' . $usuario['id']) ?>" class="space-y-6 max-w-4xl mx-auto">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

    <!-- Global Controls -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Desplegables por Vista / Módulo:</span>
        <div class="space-x-3">
            <button type="button" onclick="setAllGlobal(true)" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                Marcar Todos
            </button>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <button type="button" onclick="setAllGlobal(false)" class="text-xs font-bold text-slate-500 hover:underline">
                Desmarcar Todos
            </button>
        </div>
    </div>

    <!-- COMPACT COLLAPSIBLE ACCORDION LIST -->
    <div class="space-y-3">
        <?php foreach ($vistas as $vKey => $vistaInfo): ?>
            <details class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden" open>
                <summary class="flex items-center justify-between p-4 font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none bg-slate-50/50 dark:bg-slate-900/40 hover:bg-slate-100/80 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <i class="bi bi-chevron-right text-xs text-slate-400 group-open:rotate-90 transition-transform duration-200"></i>
                        <span><?= htmlspecialchars($vistaInfo['titulo']) ?></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-xs font-mono px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                            <?= count($vistaInfo['permisos']) ?> permisos
                        </span>
                        <button type="button" onclick="event.stopPropagation(); toggleVistaGroup('<?= $vKey ?>')" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            Toggle
                        </button>
                    </div>
                </summary>

                <div class="p-4 space-y-2.5 border-t border-slate-100 dark:border-slate-700/50 bg-white dark:bg-slate-800">
                    <?php foreach ($vistaInfo['permisos'] as $p): 
                        $pId = $p['id'] ?? $p['id_permiso'];
                        $desc = $p['descripcion'] ?? $p['nombre'] ?? '';
                        $nombrePermiso = $p['nombre'] ?? '';
                        $isVer = str_ends_with($nombrePermiso, '.ver') || str_ends_with($nombrePermiso, '.dashboard');
                        $checked = in_array($pId, $permisosUsuario) ? 'checked' : '';
                    ?>
                        <label for="permiso_<?= $pId ?>" class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors cursor-pointer select-none border border-slate-100/80 dark:border-slate-700/40">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="permiso_<?= $pId ?>" <?= $checked ?> data-group="<?= $vKey ?>"
                                       class="permiso-page-cb h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block"><?= htmlspecialchars($desc) ?></span>
                                    <span class="font-mono text-[10px] text-slate-400 dark:text-slate-500 block"><?= htmlspecialchars($nombrePermiso) ?></span>
                                </div>
                            </div>
                            <?php if ($isVer): ?>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                                    Acceso a Vista
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    Acción Individual
                                </span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endforeach; ?>
    </div>

    <!-- Submit Bar -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
        <a href="<?= url('/admin/usuarios') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Guardar Matriz de Permisos</span>
        </button>
    </div>
</form>

<script>
function toggleVistaGroup(groupKey) {
    const checkboxes = document.querySelectorAll(`input[data-group="${groupKey}"]`);
    if (checkboxes.length === 0) return;
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
function setAllGlobal(state) {
    document.querySelectorAll('.permiso-page-cb').forEach(cb => cb.checked = state);
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
