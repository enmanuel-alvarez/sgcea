<?php
/**
 * Vista: Listado de Usuarios con Modales Flotantes (Crear, Editar, Permisos ACL, Confirmación 2FA)
 */
$titulo = 'Gestión de Usuarios y Roles';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

// Estructurar el catálogo de permisos por 3 Roles Default (Administrador, Docente, Estudiante)
$permisosModalPorRol = [
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

if (!empty($todosPermisos)) {
    foreach ($todosPermisos as $p) {
        $nombre = $p['nombre'] ?? '';
        $partes = explode('.', $nombre);
        
        $modulo = $p['modulo'] ?? $partes[0] ?? 'admin';
        if ($modulo === 'docente' || str_starts_with($nombre, 'docente.')) {
            $catRol = 'docente';
        } elseif ($modulo === 'estudiante' || str_starts_with($nombre, 'estudiante.')) {
            $catRol = 'estudiante';
        } else {
            $catRol = 'admin';
        }

        $seccion = $partes[1] ?? 'general';
        $vistaKey = $modulo . '_' . $seccion;
        $vistaTitulo = ucfirst($modulo) . ' - ' . ucfirst($seccion);

        if ($modulo === 'admin') {
            $nombres = [
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
            if (isset($nombres[$vistaKey])) $vistaTitulo = $nombres[$vistaKey];
        } elseif ($modulo === 'docente') {
            $nombres = [
                'docente_dashboard' => 'Dashboard Docente',
                'docente_calificaciones' => 'Vista: Carga de Calificaciones',
                'docente_asistencia' => 'Vista: Control de Asistencias',
                'docente_planevaluacion' => 'Vista: Plan de Evaluación'
            ];
            if (isset($nombres[$vistaKey])) $vistaTitulo = $nombres[$vistaKey];
        } elseif ($modulo === 'estudiante') {
            $nombres = [
                'estudiante_dashboard' => 'Dashboard Estudiante',
                'estudiante_boletin' => 'Vista: Boletín de Notas',
                'estudiante_asistencia' => 'Vista: Historial de Asistencias',
                'estudiante_constancias' => 'Vista: Solicitud de Constancias',
                'estudiante_perfil' => 'Vista: Mi Perfil'
            ];
            if (isset($nombres[$vistaKey])) $vistaTitulo = $nombres[$vistaKey];
        } elseif ($modulo === 'reportes') {
            $vistaTitulo = 'Vista: Reportes Generales';
        }

        if (!isset($permisosModalPorRol[$catRol]['vistas'][$vistaKey])) {
            $permisosModalPorRol[$catRol]['vistas'][$vistaKey] = [
                'clave' => $vistaKey,
                'titulo' => $vistaTitulo,
                'permisos' => []
            ];
        }
        $permisosModalPorRol[$catRol]['vistas'][$vistaKey]['permisos'][] = $p;
    }
}
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestión de Usuarios y Accesos</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Administración de cuentas, perfiles de usuario y permisos por rol default.</p>
    </div>
    <?php if (in_array('admin.usuarios.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
    <div>
        <button type="button" onclick="abrirModalCrearUsuario()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Usuario</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Tabla Principal de Usuarios -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
    <div class="overflow-x-auto">
        <table id="tablaUsuarios" class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <th class="pb-3">ID</th>
                    <th class="pb-3">Usuario / Nombre</th>
                    <th class="pb-3">Correo Electrónico</th>
                    <th class="pb-3">Rol Default</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                <?php foreach ($usuarios as $u): 
                    $uId = (int)$u['id'];
                    $userPerms = $usuarioPermisosMap[$uId] ?? [];
                ?>
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="py-3.5 font-mono text-xs text-slate-400"><?= $uId ?></td>
                    <td class="py-3.5 font-semibold text-slate-900 dark:text-white">
                        <?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '')) ?>
                        <span class="block font-mono text-xs font-normal text-slate-400">C.I.: <?= htmlspecialchars($u['cedula'] ?? 'N/A') ?></span>
                    </td>
                    <td class="py-3.5 text-slate-600 dark:text-slate-300 font-mono text-xs"><?= htmlspecialchars($u['email'] ?? $u['correo'] ?? '') ?></td>
                    <td class="py-3.5">
                        <?php 
                        $tipoReal = strtolower($u['tipo'] ?? $u['tipo_usuario'] ?? $u['rol'] ?? 'estudiante');
                        $tienePermisosCustom = !empty($userPerms) || ((int)($u['total_permisos'] ?? 0) > 0);

                        if ($tipoReal === 'custom' || ($tienePermisosCustom && $tipoReal !== 'admin')): 
                        ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60" title="Usuario con permisos o accesos personalizados">
                                <i class="bi bi-sliders me-1"></i> Custom
                            </span>
                        <?php elseif ($tipoReal === 'admin' || $tipoReal === 'administrador'): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60">
                                <i class="bi bi-shield-lock-fill me-1"></i> Admin
                            </span>
                        <?php elseif ($tipoReal === 'docente' || $tipoReal === 'profesor'): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                <i class="bi bi-person-workspace me-1"></i> Docente
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                                <i class="bi bi-mortarboard-fill me-1"></i> Estudiante
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5">
                        <?php if (($u['activo'] ?? $u['estado'] ?? 1) == 1): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactivo
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 text-right space-x-2">
                        <!-- EDITAR INFORMACIÓN DEL USUARIO -->
                        <button type="button" onclick='abrirModalEditarUsuario(<?= json_encode($u) ?>)' class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors" title="Editar Datos del Usuario">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <!-- PERMISOS ACL (MODAL) -->
                        <button type="button" onclick='abrirModalPermisos(<?= $uId ?>, <?= json_encode($u["nombre"] . " " . $u["apellido"]) ?>, <?= json_encode($userPerms) ?>)' 
                                class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Gestionar Permisos ACL">
                            <i class="bi bi-key-fill"></i>
                        </button>

                        <!-- ELIMINAR USUARIO -->
                        <button type="button" onclick="confirmarEliminar2FA('<?= url('/admin/usuarios/eliminar/' . $uId) ?>', '<?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '')) ?>')" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar Usuario">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ════════════════ MODAL FLOTANTE: REGISTRO / EDICIÓN DE USUARIO ════════════════ -->
<div id="modalFormUsuario" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-4 shrink-0">
            <div class="flex items-center space-x-3 text-blue-600 dark:text-blue-400">
                <i class="bi bi-person-plus-fill text-2xl"></i>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalUsuarioTitulo">Nuevo Usuario</h3>
            </div>
            <button type="button" onclick="cerrarModalUsuario()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formUsuario" method="POST" action="" onsubmit="validarEnvioUsuario(event)" class="flex-1 overflow-y-auto pr-1 space-y-4 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="usr_cedula" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Cédula *</label>
                    <input type="text" id="usr_cedula" name="cedula" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="usr_correo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Correo Electrónico *</label>
                    <input type="email" id="usr_correo" name="correo" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="usr_nombre" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nombre *</label>
                    <input type="text" id="usr_nombre" name="nombre" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="usr_apellido" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Apellido *</label>
                    <input type="text" id="usr_apellido" name="apellido" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="usr_tipo_usuario" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Rol / Tipo de Cuenta *</label>
                    <select id="usr_tipo_usuario" name="tipo_usuario" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="estudiante">Estudiante</option>
                        <option value="docente">Docente</option>
                        <option value="admin">Administrador (Admin)</option>
                        <option value="custom">Personalizado (Custom)</option>
                    </select>
                </div>
                <div>
                    <label for="usr_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5" id="lblUsrPassword">Contraseña *</label>
                    <input type="password" id="usr_password" name="password" minlength="6" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" id="usr_activo" name="activo" value="1" checked class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="usr_activo" class="text-xs font-semibold text-slate-800 dark:text-slate-200">Cuenta Activa</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
                <button type="button" onclick="cerrarModalUsuario()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span id="btnUsuarioSubmitTexto">Guardar Usuario</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ════════════════ MODAL REESTRUCTURADO: PERMISOS ACL POR 3 TABS DE ROL ════════════════ -->
<div id="modalPermisosACL" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-4 max-h-[92vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3 shrink-0">
            <div class="flex items-center space-x-3 text-amber-500">
                <i class="bi bi-shield-lock-fill text-2xl"></i>
                <div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalPermisosTitulo">Asignar Permisos ACL</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Asigne permisos de forma flexible divididos por rol predeterminado.</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalPermisos()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- SWITCHES TABS DEL MODAL POR ROL DEFAULT -->
        <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl shrink-0">
            <button type="button" onclick="switchModalRoleTab('admin')" id="mRoleBtn-admin"
                    class="m-role-btn flex-1 py-2 px-3 rounded-lg text-xs font-extrabold transition-all border-2 border-blue-600 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-300 shadow-sm flex items-center justify-center space-x-1.5">
                <i class="bi bi-shield-lock-fill text-blue-600 dark:text-blue-400"></i>
                <span>1. Administrador</span>
            </button>
            <button type="button" onclick="switchModalRoleTab('docente')" id="mRoleBtn-docente"
                    class="m-role-btn flex-1 py-2 px-3 rounded-lg text-xs font-semibold transition-all border-2 border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 flex items-center justify-center space-x-1.5">
                <i class="bi bi-person-badge-fill text-emerald-600 dark:text-emerald-400"></i>
                <span>2. Docente</span>
            </button>
            <button type="button" onclick="switchModalRoleTab('estudiante')" id="mRoleBtn-estudiante"
                    class="m-role-btn flex-1 py-2 px-3 rounded-lg text-xs font-semibold transition-all border-2 border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 flex items-center justify-center space-x-1.5">
                <i class="bi bi-mortarboard-fill text-purple-600 dark:text-purple-400"></i>
                <span>3. Estudiante</span>
            </button>
        </div>

        <!-- CONTROLES DE CARGA RÁPIDA -->
        <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-xs shrink-0">
            <div class="flex items-center space-x-1.5">
                <span class="font-bold text-slate-500 uppercase text-[10px]">Cargar Rol:</span>
                <button type="button" onclick="marcarModalRolGroup('admin', true)" class="px-2 py-0.5 bg-blue-100 hover:bg-blue-200 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 font-bold rounded">
                    + Admin
                </button>
                <button type="button" onclick="marcarModalRolGroup('docente', true)" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 font-bold rounded">
                    + Docente
                </button>
                <button type="button" onclick="marcarModalRolGroup('estudiante', true)" class="px-2 py-0.5 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 font-bold rounded">
                    + Estudiante
                </button>
            </div>
            <div class="space-x-2">
                <button type="button" onclick="seleccionarTodosModal(true)" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Todos
                </button>
                <span class="text-slate-300">|</span>
                <button type="button" onclick="seleccionarTodosModal(false)" class="font-bold text-rose-500 hover:underline">
                    Limpiar
                </button>
            </div>
        </div>

        <!-- Formulario de Permisos -->
        <form id="formPermisosModal" method="POST" action="" class="flex-1 overflow-y-auto pr-1 space-y-4 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <!-- PANELES DEL MODAL POR ROL DEFAULT -->
            <?php foreach ($permisosModalPorRol as $rolKey => $rolInfo): 
                $hiddenClass = ($rolKey !== 'admin') ? 'hidden' : '';
            ?>
                <div id="mRolePanel-<?= $rolKey ?>" class="m-role-panel space-y-3 <?= $hiddenClass ?>">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/50 dark:border-slate-700/40">
                        <div class="flex items-center space-x-2 text-xs">
                            <i class="bi <?= $rolInfo['icono'] ?> text-<?= $rolInfo['color'] ?>-500"></i>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($rolInfo['titulo']) ?></span>
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded bg-<?= $rolInfo['color'] ?>-100 text-<?= $rolInfo['color'] ?>-800 dark:bg-<?= $rolInfo['color'] ?>-900/60 dark:text-<?= $rolInfo['color'] ?>-300">
                                <?= htmlspecialchars($rolInfo['badge']) ?>
                            </span>
                        </div>
                        <div class="space-x-2 text-[11px]">
                            <button type="button" onclick="marcarModalRolGroup('<?= $rolKey ?>', true)" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Marcar tab</button>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="marcarModalRolGroup('<?= $rolKey ?>', false)" class="font-bold text-slate-500 hover:underline">Desmarcar tab</button>
                        </div>
                    </div>

                    <?php if (!empty($rolInfo['vistas'])): ?>
                        <?php foreach ($rolInfo['vistas'] as $vKey => $vInfo): ?>
                            <details class="group bg-slate-50/70 dark:bg-slate-900/50 rounded-xl border border-slate-200/80 dark:border-slate-700/70 overflow-hidden" open>
                                <summary class="flex items-center justify-between p-3 font-bold text-xs text-slate-800 dark:text-slate-200 cursor-pointer select-none bg-white dark:bg-slate-800 hover:bg-slate-100/80 dark:hover:bg-slate-700/60 transition-colors">
                                    <div class="flex items-center space-x-2.5">
                                        <i class="bi bi-chevron-right text-[10px] text-slate-400 group-open:rotate-90 transition-transform duration-200"></i>
                                        <span><?= htmlspecialchars($vInfo['titulo']) ?></span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            <?= count($vInfo['permisos']) ?> opciones
                                        </span>
                                        <button type="button" onclick="event.stopPropagation(); toggleGrupoModal('<?= $vKey ?>')" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                            Toggle
                                        </button>
                                    </div>
                                </summary>

                                <div class="p-3 space-y-2 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/40 dark:bg-slate-900/40">
                                    <?php foreach ($vInfo['permisos'] as $p): 
                                        $pId = (int)($p['id'] ?? $p['id_permiso']);
                                        $desc = $p['descripcion'] ?? $p['nombre'] ?? '';
                                        $nombrePermiso = $p['nombre'] ?? '';
                                        $isVer = str_ends_with($nombrePermiso, '.ver') || str_ends_with($nombrePermiso, '.dashboard');
                                    ?>
                                        <label for="m_permiso_<?= $pId ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800/80 transition-colors cursor-pointer select-none border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="m_permiso_<?= $pId ?>" data-group="<?= $vKey ?>" data-mrol="<?= $rolKey ?>"
                                                       class="modal-permiso-cb h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($desc) ?></span>
                                            </div>
                                            <?php if ($isVer): ?>
                                                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                                                    Acceso a Vista
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[9px] font-mono px-2 py-0.5 rounded bg-slate-200/60 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
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
            <?php endforeach; ?>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
                <button type="button" onclick="cerrarModalPermisos()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span>Guardar Permisos</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ════════════════ MODAL FLOTANTE: CONFIRMACIÓN DE MODIFICACIÓN ════════════════ -->
<div id="modalConfirmarModificacion" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl border border-blue-200 dark:border-blue-900/50 shadow-2xl p-6 sm:p-8 space-y-5 text-center">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl">
            <i class="bi bi-question-circle-fill"></i>
        </div>
        <div class="space-y-2">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Confirmar Modificación</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                ¿Está seguro de guardar los cambios realizados en este registro?
            </p>
        </div>
        <div class="flex items-center space-x-3 pt-2">
            <button type="button" onclick="cerrarModalConfirmarModificacion()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                Cancelar
            </button>
            <button type="button" onclick="ejecutarSubmitConfirmado()" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-600/20 transition-all">
                Sí, Guardar Cambios
            </button>
        </div>
    </div>
</div>

<!-- ════════════════ MODAL FLOTANTE: ELIMINACIÓN CON DOBLE FACTOR (2FA + FRASE) ════════════════ -->
<div id="modalEliminar2FA" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl border border-rose-200 dark:border-rose-900/50 shadow-2xl p-6 sm:p-8 space-y-5 text-center">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="space-y-2">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">Confirmación de Eliminación</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed" id="delModalTexto">
                Esta acción eliminará de forma permanente el registro seleccionado.
            </p>
        </div>

        <form id="formEliminar2FA" method="POST" action="" class="space-y-4 text-left pt-1">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
            
            <div>
                <label for="txtFrase2FA" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Para confirmar, escriba la palabra <span class="select-none font-mono font-bold text-rose-600 dark:text-rose-400">ELIMINAR</span>:
                </label>
                <input type="text" id="txtFrase2FA" oninput="validar2FA()" placeholder="Escriba ELIMINAR aquí" autocomplete="off"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 text-sm font-mono tracking-wider">
            </div>

            <div class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-xl">
                <label class="flex items-start space-x-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="chk2FA" onchange="validar2FA()" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-xs font-semibold text-rose-900 dark:text-rose-200 leading-tight">
                        Entiendo las consecuencias y confirmo eliminar permanentemente este registro.
                    </span>
                </label>
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="button" onclick="cerrarModalEliminar2FA()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" id="btnSubmit2FA" disabled class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl text-xs shadow-md shadow-rose-600/20 transition-all">
                    Eliminar Permanente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let esEdicionUsuario = false;
let formPendienteSubmit = null;

function abrirModalCrearUsuario() {
    esEdicionUsuario = false;
    const form = document.getElementById('formUsuario');
    form.reset();
    form.action = '<?= url('/admin/usuarios/guardar') ?>';
    document.getElementById('modalUsuarioTitulo').innerText = 'Nuevo Usuario';
    document.getElementById('btnUsuarioSubmitTexto').innerText = 'Guardar Usuario';
    document.getElementById('lblUsrPassword').innerText = 'Contraseña *';
    document.getElementById('usr_password').required = true;
    document.getElementById('modalFormUsuario').classList.remove('hidden');
}

function abrirModalEditarUsuario(u) {
    esEdicionUsuario = true;
    const form = document.getElementById('formUsuario');
    form.reset();
    form.action = '<?= url('/admin/usuarios/actualizar/') ?>' + u.id;
    
    document.getElementById('modalUsuarioTitulo').innerText = 'Editar Usuario: ' + (u.nombre || '') + ' ' + (u.apellido || '');
    document.getElementById('btnUsuarioSubmitTexto').innerText = 'Actualizar Usuario';
    document.getElementById('lblUsrPassword').innerText = 'Nueva Contraseña (Opcional)';
    document.getElementById('usr_password').required = false;

    document.getElementById('usr_cedula').value = u.cedula || '';
    document.getElementById('usr_correo').value = u.correo || u.email || '';
    document.getElementById('usr_nombre').value = u.nombre || '';
    document.getElementById('usr_apellido').value = u.apellido || '';
    document.getElementById('usr_tipo_usuario').value = (u.tipo || u.tipo_usuario || u.rol || 'estudiante').toLowerCase();
    document.getElementById('usr_activo').checked = (u.activo ?? u.estado ?? 1) == 1;

    document.getElementById('modalFormUsuario').classList.remove('hidden');
}

function cerrarModalUsuario() {
    document.getElementById('modalFormUsuario').classList.add('hidden');
}

function validarEnvioUsuario(e) {
    if (esEdicionUsuario && !formPendienteSubmit) {
        e.preventDefault();
        formPendienteSubmit = document.getElementById('formUsuario');
        document.getElementById('modalConfirmarModificacion').classList.remove('hidden');
    }
}

function switchModalRoleTab(rolKey) {
    document.querySelectorAll('.m-role-panel').forEach(panel => panel.classList.add('hidden'));

    document.querySelectorAll('.m-role-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'bg-white', 'dark:bg-slate-800', 'text-blue-700', 'dark:text-blue-300', 'font-extrabold', 'shadow-sm', 'border-emerald-600', 'text-emerald-700', 'border-purple-600', 'text-purple-700');
        btn.classList.add('border-transparent', 'text-slate-500', 'font-semibold');
    });

    const panel = document.getElementById('mRolePanel-' + rolKey);
    if (panel) panel.classList.remove('hidden');

    const btn = document.getElementById('mRoleBtn-' + rolKey);
    if (btn) {
        btn.classList.remove('border-transparent', 'text-slate-500', 'font-semibold');
        btn.classList.add('font-extrabold', 'bg-white', 'dark:bg-slate-800', 'shadow-sm');
        if (rolKey === 'admin') {
            btn.classList.add('border-blue-600', 'text-blue-700', 'dark:text-blue-300');
        } else if (rolKey === 'docente') {
            btn.classList.add('border-emerald-600', 'text-emerald-700', 'dark:text-emerald-300');
        } else if (rolKey === 'estudiante') {
            btn.classList.add('border-purple-600', 'text-purple-700', 'dark:text-purple-300');
        }
    }
}

function marcarModalRolGroup(rolKey, state) {
    document.querySelectorAll(`input[data-mrol="${rolKey}"]`).forEach(cb => cb.checked = state);
}

function abrirModalPermisos(userId, userName, activePerms) {
    const modal = document.getElementById('modalPermisosACL');
    const form = document.getElementById('formPermisosModal');
    const titulo = document.getElementById('modalPermisosTitulo');
    
    if (form) {
        form.action = '<?= url('/admin/permisos/guardar/') ?>' + userId;
    }
    if (titulo) {
        titulo.innerText = 'Permisos ACL: ' + userName;
    }

    document.querySelectorAll('.modal-permiso-cb').forEach(cb => cb.checked = false);

    if (Array.isArray(activePerms)) {
        activePerms.forEach(pId => {
            const cb = document.getElementById('m_permiso_' + pId);
            if (cb) cb.checked = true;
        });
    }

    switchModalRoleTab('admin');
    if (modal) modal.classList.remove('hidden');
}

function cerrarModalPermisos() {
    const modal = document.getElementById('modalPermisosACL');
    if (modal) modal.classList.add('hidden');
}

function toggleGrupoModal(vKey) {
    const cbs = document.querySelectorAll(`input[data-group="${vKey}"]`);
    if (cbs.length === 0) return;
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}

function seleccionarTodosModal(state) {
    document.querySelectorAll('.modal-permiso-cb').forEach(cb => cb.checked = state);
}

function cerrarModalConfirmarModificacion() {
    document.getElementById('modalConfirmarModificacion').classList.add('hidden');
    formPendienteSubmit = null;
}

function ejecutarSubmitConfirmado() {
    if (formPendienteSubmit) {
        const temp = formPendienteSubmit;
        formPendienteSubmit = null;
        cerrarModalConfirmarModificacion();
        temp.submit();
    }
}

function confirmarEliminar2FA(actionUrl, nombre) {
    const form = document.getElementById('formEliminar2FA');
    form.action = actionUrl;
    document.getElementById('delModalTexto').innerText = '¿Está seguro de eliminar al usuario "' + nombre + '"? Esta operación no se puede deshacer.';
    document.getElementById('txtFrase2FA').value = '';
    document.getElementById('chk2FA').checked = false;
    document.getElementById('btnSubmit2FA').disabled = true;
    document.getElementById('modalEliminar2FA').classList.remove('hidden');
}

function cerrarModalEliminar2FA() {
    document.getElementById('modalEliminar2FA').classList.add('hidden');
}

function validar2FA() {
    const txt = document.getElementById('txtFrase2FA').value.trim().toUpperCase();
    const chk = document.getElementById('chk2FA').checked;
    document.getElementById('btnSubmit2FA').disabled = !(txt === 'ELIMINAR' && chk);
}

$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        language: window.SGCEA_DATATABLES_SPANISH || {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        responsive: true
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

