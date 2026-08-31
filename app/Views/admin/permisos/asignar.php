<?php
/**
 * Vista: Admin - Asignar Permisos ACL por 3 Tabs (Administrador, Docente, Estudiante)
 */
$titulo = 'Asignar Permisos ACL';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

// Estructurar permisos en 3 Pestañas / Switches principales según el Rol Default
$permisosPorRol = [
    'admin' => [
        'titulo' => 'Permisos de Administrador',
        'icono' => 'bi-shield-lock-fill',
        'color' => 'blue',
        'badge' => 'Rol Default: Administrador',
        'vistas' => []
    ],
    'docente' => [
        'titulo' => 'Permisos de Docente',
        'icono' => 'bi-person-badge-fill',
        'color' => 'emerald',
        'badge' => 'Rol Default: Docente',
        'vistas' => []
    ],
    'estudiante' => [
        'titulo' => 'Permisos de Estudiante',
        'icono' => 'bi-mortarboard-fill',
        'color' => 'purple',
        'badge' => 'Rol Default: Estudiante',
        'vistas' => []
    ]
];

foreach ($todosPermisos as $p) {
    $nombre = $p['nombre'] ?? '';
    $partes = explode('.', $nombre);
    $modulo = $p['modulo'] ?? $partes[0] ?? 'admin';

    if ($modulo === 'docente' || str_starts_with($nombre, 'docente.')) {
        $catRol = 'docente';
    } elseif ($modulo === 'estudiante' || str_starts_with($nombre, 'estudiante.')) {
        $catRol = 'estudiante';
    } else {
        $catRol = 'admin'; // Incluye admin, reportes y utilidades de administración
    }

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
            'docente_planevaluacion' => 'Vista: Plan de Evaluación',
            'docente_revisiones' => 'Vista: Solicitudes de Revisión de Notas'
        ];
        if (isset($nombresVistas[$vistaKey])) $vistaTitulo = $nombresVistas[$vistaKey];
    } elseif ($modulo === 'estudiante') {
        $nombresVistas = [
            'estudiante_dashboard' => 'Dashboard Estudiante',
            'estudiante_boletin' => 'Vista: Boletín de Notas',
            'estudiante_asistencia' => 'Vista: Historial de Asistencias',
            'estudiante_constancias' => 'Vista: Solicitud de Constancias',
            'estudiante_perfil' => 'Vista: Mi Perfil Estudiantil',
            'estudiante_revisiones' => 'Vista: Solicitudes de Revisión de Notas'
        ];
        if (isset($nombresVistas[$vistaKey])) $vistaTitulo = $nombresVistas[$vistaKey];
    } elseif ($modulo === 'reportes') {
        $vistaTitulo = 'Vista: Reportes Generales';
    }

    if (!isset($permisosPorRol[$catRol]['vistas'][$vistaKey])) {
        $permisosPorRol[$catRol]['vistas'][$vistaKey] = [
            'clave' => $vistaKey,
            'titulo' => $vistaTitulo,
            'permisos' => []
        ];
    }
    $permisosPorRol[$catRol]['vistas'][$vistaKey]['permisos'][] = $p;
}
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestor de Permisos por Rol Default</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Asignación flexible para <strong class="text-blue-600 dark:text-blue-400 font-bold"><?= htmlspecialchars($usuario['nombre_completo'] ?? ($usuario['nombre'] . ' ' . $usuario['apellido'])) ?></strong> 
            (Rol Base: <span class="uppercase font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($usuario['tipo_usuario'] ?? $usuario['rol'] ?? 'Usuario') ?></span>).
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

    <!-- BARRA DE SWITCHES / TABS POR ROL DEFAULT -->
    <div class="bg-white dark:bg-slate-800 p-2 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex flex-wrap gap-2">
        <button type="button" onclick="switchRoleTab('admin')" id="roleBtn-admin"
                class="role-tab-btn flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-extrabold border-2 transition-all flex items-center justify-center space-x-2 border-blue-600 bg-blue-50/50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
            <i class="bi bi-shield-lock-fill text-blue-600 dark:text-blue-400"></i>
            <span>1. Administrador</span>
            <span class="ml-1.5 px-2 py-0.5 text-[11px] font-mono rounded-full bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100">
                <?= count($todosPermisos) ?>
            </span>
        </button>

        <button type="button" onclick="switchRoleTab('docente')" id="roleBtn-docente"
                class="role-tab-btn flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-semibold border-2 transition-all flex items-center justify-center space-x-2 border-transparent text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/40 dark:text-slate-400">
            <i class="bi bi-person-badge-fill text-emerald-600 dark:text-emerald-400"></i>
            <span>2. Docente</span>
        </button>

        <button type="button" onclick="switchRoleTab('estudiante')" id="roleBtn-estudiante"
                class="role-tab-btn flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-semibold border-2 transition-all flex items-center justify-center space-x-2 border-transparent text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/40 dark:text-slate-400">
            <i class="bi bi-mortarboard-fill text-purple-600 dark:text-purple-400"></i>
            <span>3. Estudiante</span>
        </button>
    </div>

    <!-- BOTONES DE ACCIONES RÁPIDAS -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
        <div class="flex items-center space-x-2">
            <span class="text-xs font-bold uppercase text-slate-600 dark:text-slate-400">Marcar Rol Completo:</span>
            <button type="button" onclick="marcarRolGroup('admin', true)" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 text-xs font-bold rounded-lg transition-colors">
                + Todos Admin
            </button>
            <button type="button" onclick="marcarRolGroup('docente', true)" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 text-xs font-bold rounded-lg transition-colors">
                + Todos Docente
            </button>
            <button type="button" onclick="marcarRolGroup('estudiante', true)" class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 text-xs font-bold rounded-lg transition-colors">
                + Todos Estudiante
            </button>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="setAllGlobal(true)" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                Marcar Todos
            </button>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <button type="button" onclick="setAllGlobal(false)" class="text-xs font-bold text-rose-500 hover:underline">
                Desmarcar Todos
            </button>
        </div>
    </div>

    <!-- PANELES DE PERMISOS POR ROL -->
    <?php foreach ($permisosPorRol as $rolKey => $rolInfo): 
        $hiddenClass = ($rolKey !== 'admin') ? 'hidden' : '';
    ?>
        <div id="rolePanel-<?= $rolKey ?>" class="role-tab-panel space-y-4 <?= $hiddenClass ?>">
            
            <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                <div class="flex items-center space-x-2">
                    <i class="bi <?= $rolInfo['icono'] ?> text-lg text-<?= $rolInfo['color'] ?>-500"></i>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white"><?= htmlspecialchars($rolInfo['titulo']) ?></h2>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-<?= $rolInfo['color'] ?>-100 text-<?= $rolInfo['color'] ?>-800 dark:bg-<?= $rolInfo['color'] ?>-900/60 dark:text-<?= $rolInfo['color'] ?>-300">
                        <?= htmlspecialchars($rolInfo['badge']) ?>
                    </span>
                </div>
                <div class="space-x-2">
                    <button type="button" onclick="marcarRolGroup('<?= $rolKey ?>', true)" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Marcar esta pestaña
                    </button>
                    <span class="text-slate-300 dark:text-slate-700">|</span>
                    <button type="button" onclick="marcarRolGroup('<?= $rolKey ?>', false)" class="text-xs font-bold text-slate-500 hover:underline">
                        Desmarcar pestaña
                    </button>
                </div>
            </div>

            <div class="space-y-3">
                <?php if (empty($rolInfo['vistas'])): ?>
                    <p class="text-xs text-slate-500 italic p-4 bg-white dark:bg-slate-800 rounded-xl">No hay permisos registrados en este rol.</p>
                <?php else: ?>
                    <?php foreach ($rolInfo['vistas'] as $vKey => $vistaInfo): ?>
                        <details class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm overflow-hidden" open>
                            <summary class="flex items-center justify-between p-4 font-bold text-sm text-slate-900 dark:text-white cursor-pointer select-none bg-slate-50/50 dark:bg-slate-900/40 hover:bg-slate-100/80 dark:hover:bg-slate-700/50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <i class="bi bi-chevron-right text-xs text-slate-400 group-open:rotate-90 transition-transform duration-200"></i>
                                    <span><?= htmlspecialchars($vistaInfo['titulo']) ?></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs font-mono px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= count($vistaInfo['permisos']) ?> opciones
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
                                            <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="permiso_<?= $pId ?>" <?= $checked ?> data-group="<?= $vKey ?>" data-rol="<?= $rolKey ?>"
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
                                                Acción
                                            </span>
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

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
function switchRoleTab(rolKey) {
    // Esconder todos los paneles
    document.querySelectorAll('.role-tab-panel').forEach(panel => panel.classList.add('hidden'));

    // Resetear estilos de todos los botones
    document.querySelectorAll('.role-tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'bg-blue-50/50', 'text-blue-700', 'dark:bg-blue-900/40', 'dark:text-blue-300', 'font-extrabold', 'border-emerald-600', 'bg-emerald-50/50', 'text-emerald-700', 'border-purple-600', 'bg-purple-50/50', 'text-purple-700');
        btn.classList.add('border-transparent', 'text-slate-500', 'font-semibold');
    });

    // Mostrar panel activo
    const panel = document.getElementById('rolePanel-' + rolKey);
    if (panel) panel.classList.remove('hidden');

    // Resaltar botón activo
    const btn = document.getElementById('roleBtn-' + rolKey);
    if (btn) {
        btn.classList.remove('border-transparent', 'text-slate-500', 'font-semibold');
        btn.classList.add('font-extrabold');
        if (rolKey === 'admin') {
            btn.classList.add('border-blue-600', 'bg-blue-50/50', 'text-blue-700', 'dark:bg-blue-900/40', 'dark:text-blue-300');
        } else if (rolKey === 'docente') {
            btn.classList.add('border-emerald-600', 'bg-emerald-50/50', 'text-emerald-700', 'dark:bg-emerald-900/40', 'dark:text-emerald-300');
        } else if (rolKey === 'estudiante') {
            btn.classList.add('border-purple-600', 'bg-purple-50/50', 'text-purple-700', 'dark:bg-purple-900/40', 'dark:text-purple-300');
        }
    }
}

function marcarRolGroup(rolKey, state) {
    document.querySelectorAll(`input[data-rol="${rolKey}"]`).forEach(cb => cb.checked = state);
}

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


