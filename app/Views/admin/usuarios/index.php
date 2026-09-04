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
                        $uExcepciones = $usuarioExcepcionesMap[$u['id']] ?? [];
                        $tieneExcepciones = !empty($uExcepciones);

                        if ($tipoReal === 'admin' || $tipoReal === 'administrador'): 
                        ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/60">
                                <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                <?php if ($tieneExcepciones): ?><i class="bi bi-sliders ms-1 text-amber-600" title="Con excepciones directas"></i><?php endif; ?>
                            </span>
                        <?php elseif ($tipoReal === 'docente'): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                <i class="bi bi-person-badge-fill me-1"></i> Docente
                                <?php if ($tieneExcepciones): ?><i class="bi bi-sliders ms-1 text-amber-600" title="Con excepciones directas"></i><?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                                <i class="bi bi-mortarboard-fill me-1"></i> Estudiante
                                <?php if ($tieneExcepciones): ?><i class="bi bi-sliders ms-1 text-amber-600" title="Con excepciones directas"></i><?php endif; ?>
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

                        <!-- PERMISOS ACL (MODAL MINIMALISTA HYBRID RBAC) -->
                        <button type="button" onclick='abrirModalPermisos(<?= $uId ?>, <?= json_encode(($u["nombre"] ?? "") . " " . ($u["apellido"] ?? "")) ?>, <?= json_encode(ucfirst($u["tipo"] ?? "estudiante")) ?>, <?= json_encode($userPerms) ?>, <?= json_encode($uExcepciones) ?>)' 
                                class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Gestionar Permisos y Excepciones ACL">
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

<?php
// Obtener matriz estructurada por Vistas y Acciones
$permisoService = $permisoService ?? new \Src\Models\Services\PermisoService();
$matrizVistas = $permisoService->obtenerMatrizPermisosPorVista();

$vistasAdmin = [];
$vistasOtras = [];

foreach ($matrizVistas as $vKey => $vInfo) {
    $modLower = strtolower($vInfo['modulo'] ?? '');
    if ($modLower === 'admin' || str_contains($modLower, 'admin')) {
        $vistasAdmin[$vKey] = $vInfo;
    } else {
        $vistasOtras[$vKey] = $vInfo;
    }
}
?>

<!-- ════════════════ MODAL REESTRUCTURADO: GESTOR DE PERMISOS ACL (VISTAS Y DESPLEGABLE DE ACCIONES) ════════════════ -->
<div id="modalPermisosACL" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity">
    <div class="relative w-full max-w-5xl bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">
        
        <!-- Header Minimalista -->
        <div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white" id="modalPermisosTitulo">Gestor de Permisos por Vistas y Acciones</h3>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Modelo RBAC Híbrido</span>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <span id="modalPermisosRoleBadge" class="font-bold text-amber-600 dark:text-amber-400">Rol Base: Administrador</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="cerrarModalPermisos()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Leyenda y Filtros Rápidos -->
        <div class="px-6 py-2.5 bg-slate-100/50 dark:bg-slate-950/40 border-b border-slate-200/40 dark:border-slate-800/40 flex flex-wrap items-center justify-between gap-2 text-xs shrink-0">
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center text-emerald-700 dark:text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Conceder (+)
                </span>
                <span class="inline-flex items-center text-rose-700 dark:text-rose-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> Revocar (-)
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <input type="text" id="filtroPermisoBuscar" oninput="filtrarPermisosModal()" placeholder="Buscar vista o acción..." 
                       class="px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500 w-52">
            </div>
        </div>

        <!-- Formulario de Permisos (Vistas Principales y Desplegables de Acciones) -->
        <form id="formPermisosModal" method="POST" action="" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                
                <!-- COLUMNA 1: VISTAS DEL MÓDULO ADMINISTRACIÓN -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 px-1 text-xs font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider">
                        <i class="bi bi-shield-lock-fill text-purple-500"></i>
                        <span>Vistas Administrativas</span>
                    </div>

                    <?php foreach ($vistasAdmin as $vKey => $vInfo): ?>
                        <details class="group border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-800/60 shadow-sm hover:shadow-md transition-all overflow-hidden" open>
                            <summary class="flex items-center justify-between p-3.5 bg-slate-50/80 dark:bg-slate-800/80 hover:bg-slate-100/80 dark:hover:bg-slate-700/60 cursor-pointer select-none border-b border-slate-100 dark:border-slate-800/60">
                                <div class="flex items-center space-x-2">
                                    <i class="bi bi-chevron-right text-xs text-slate-400 group-open:rotate-90 transition-transform duration-200"></i>
                                    <span class="font-extrabold text-xs text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        <?= htmlspecialchars($vInfo['titulo']) ?>
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2.5 shrink-0" onclick="event.stopPropagation()">
                                    <?php if ($vInfo['acceso_vista']): 
                                        $pVis = $vInfo['acceso_vista'];
                                        $pVisId = (int)$pVis['id'];
                                    ?>
                                        <div class="flex items-center space-x-2 bg-purple-50/80 dark:bg-purple-950/40 px-2.5 py-1 rounded-xl border border-purple-200/60 dark:border-purple-800/60 text-xs" data-permiso-text="<?= strtolower($pVis['nombre'] . ' ' . ($pVis['descripcion'] ?? '')) ?>">
                                            <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300">Ver Vista</span>
                                            <div class="flex items-center space-x-1 select-none">
                                                <label title="Conceder Vista (+)" class="flex items-center cursor-pointer">
                                                    <input type="checkbox" name="permisos[]" value="<?= $pVisId ?>" id="permiso_conc_<?= $pVisId ?>" onchange="sincronizarCasillaPermiso(<?= $pVisId ?>, 'conc')"
                                                           class="cb-permiso-conceder h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 ml-0.5">+</span>
                                                </label>
                                                <label title="Revocar Vista (-)" class="flex items-center cursor-pointer ml-1">
                                                    <input type="checkbox" name="permisos_revocar[]" value="<?= $pVisId ?>" id="permiso_rev_<?= $pVisId ?>" onchange="sincronizarCasillaPermiso(<?= $pVisId ?>, 'rev')"
                                                           class="cb-permiso-revocar h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 ml-0.5">-</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($vInfo['acciones'])): ?>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            <?= count($vInfo['acciones']) ?> acciones
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </summary>

                            <?php if (!empty($vInfo['acciones'])): ?>
                                <div class="p-3 space-y-2 bg-slate-50/40 dark:bg-slate-900/40">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Acciones Específicas</div>
                                    <?php foreach ($vInfo['acciones'] as $p): 
                                        $pId = (int)$p['id'];
                                        $pNom = $p['nombre'];
                                        $pDesc = $p['descripcion'] ?? $pNom;
                                    ?>
                                        <div class="item-permiso flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 transition-colors" data-permiso-text="<?= strtolower($pNom . ' ' . $pDesc) ?>">
                                            <div class="space-y-0.5 pr-2">
                                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight"><?= htmlspecialchars($pDesc) ?></div>
                                                <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500"><?= htmlspecialchars($pNom) ?></div>
                                            </div>

                                            <div class="flex items-center space-x-3 shrink-0 select-none">
                                                <label title="Conceder acción (+)" class="flex items-center space-x-1 cursor-pointer">
                                                    <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="permiso_conc_<?= $pId ?>" onchange="sincronizarCasillaPermiso(<?= $pId ?>, 'conc')"
                                                           class="cb-permiso-conceder h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">+</span>
                                                </label>
                                                <label title="Revocar acción (-)" class="flex items-center space-x-1 cursor-pointer">
                                                    <input type="checkbox" name="permisos_revocar[]" value="<?= $pId ?>" id="permiso_rev_<?= $pId ?>" onchange="sincronizarCasillaPermiso(<?= $pId ?>, 'rev')"
                                                           class="cb-permiso-revocar h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">-</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </details>
                    <?php endforeach; ?>
                </div>

                <!-- COLUMNA 2: VISTAS DOCENTE, ESTUDIANTE Y REPORTES APILADAS -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 px-1 text-xs font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                        <i class="bi bi-person-workspace text-emerald-500"></i>
                        <span>Vistas Operacionales y Reportes</span>
                    </div>

                    <?php foreach ($vistasOtras as $vKey => $vInfo): 
                        $modColor = 'blue';
                        $modLower = strtolower($vInfo['modulo'] ?? '');

                        if (str_contains($modLower, 'docente')) {
                            $modColor = 'emerald';
                        } elseif (str_contains($modLower, 'estudiante')) {
                            $modColor = 'indigo';
                        } elseif (str_contains($modLower, 'reporte')) {
                            $modColor = 'amber';
                        }
                    ?>
                        <details class="group border border-slate-200/80 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-800/60 shadow-sm hover:shadow-md transition-all overflow-hidden" open>
                            <summary class="flex items-center justify-between p-3.5 bg-slate-50/80 dark:bg-slate-800/80 hover:bg-slate-100/80 dark:hover:bg-slate-700/60 cursor-pointer select-none border-b border-slate-100 dark:border-slate-800/60">
                                <div class="flex items-center space-x-2">
                                    <i class="bi bi-chevron-right text-xs text-slate-400 group-open:rotate-90 transition-transform duration-200"></i>
                                    <span class="font-extrabold text-xs text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        <?= htmlspecialchars($vInfo['titulo']) ?>
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2.5 shrink-0" onclick="event.stopPropagation()">
                                    <?php if ($vInfo['acceso_vista']): 
                                        $pVis = $vInfo['acceso_vista'];
                                        $pVisId = (int)$pVis['id'];
                                    ?>
                                        <div class="flex items-center space-x-2 bg-<?= $modColor ?>-50/80 dark:bg-<?= $modColor ?>-950/40 px-2.5 py-1 rounded-xl border border-<?= $modColor ?>-200/60 dark:border-<?= $modColor ?>-800/60 text-xs" data-permiso-text="<?= strtolower($pVis['nombre'] . ' ' . ($pVis['descripcion'] ?? '')) ?>">
                                            <span class="text-[10px] font-bold text-<?= $modColor ?>-700 dark:text-<?= $modColor ?>-300">Ver Vista</span>
                                            <div class="flex items-center space-x-1 select-none">
                                                <label title="Conceder Vista (+)" class="flex items-center cursor-pointer">
                                                    <input type="checkbox" name="permisos[]" value="<?= $pVisId ?>" id="permiso_conc_<?= $pVisId ?>" onchange="sincronizarCasillaPermiso(<?= $pVisId ?>, 'conc')"
                                                           class="cb-permiso-conceder h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 ml-0.5">+</span>
                                                </label>
                                                <label title="Revocar Vista (-)" class="flex items-center cursor-pointer ml-1">
                                                    <input type="checkbox" name="permisos_revocar[]" value="<?= $pVisId ?>" id="permiso_rev_<?= $pVisId ?>" onchange="sincronizarCasillaPermiso(<?= $pVisId ?>, 'rev')"
                                                           class="cb-permiso-revocar h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 ml-0.5">-</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($vInfo['acciones'])): ?>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            <?= count($vInfo['acciones']) ?> acciones
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </summary>

                            <?php if (!empty($vInfo['acciones'])): ?>
                                <div class="p-3 space-y-2 bg-slate-50/40 dark:bg-slate-900/40">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Acciones Específicas</div>
                                    <?php foreach ($vInfo['acciones'] as $p): 
                                        $pId = (int)$p['id'];
                                        $pNom = $p['nombre'];
                                        $pDesc = $p['descripcion'] ?? $pNom;
                                    ?>
                                        <div class="item-permiso flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 transition-colors" data-permiso-text="<?= strtolower($pNom . ' ' . $pDesc) ?>">
                                            <div class="space-y-0.5 pr-2">
                                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight"><?= htmlspecialchars($pDesc) ?></div>
                                                <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500"><?= htmlspecialchars($pNom) ?></div>
                                            </div>

                                            <div class="flex items-center space-x-3 shrink-0 select-none">
                                                <label title="Conceder acción (+)" class="flex items-center space-x-1 cursor-pointer">
                                                    <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="permiso_conc_<?= $pId ?>" onchange="sincronizarCasillaPermiso(<?= $pId ?>, 'conc')"
                                                           class="cb-permiso-conceder h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">+</span>
                                                </label>
                                                <label title="Revocar acción (-)" class="flex items-center space-x-1 cursor-pointer">
                                                    <input type="checkbox" name="permisos_revocar[]" value="<?= $pId ?>" id="permiso_rev_<?= $pId ?>" onchange="sincronizarCasillaPermiso(<?= $pId ?>, 'rev')"
                                                           class="cb-permiso-revocar h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">-</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </details>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                <button type="button" onclick="limpiarExcepcionesModal()" class="px-3.5 py-2 text-rose-600 hover:text-rose-700 dark:text-rose-400 font-semibold text-xs transition-all flex items-center space-x-1.5">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Limpiar Selecciones</span>
                </button>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="cerrarModalPermisos()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs shadow-md shadow-amber-500/20 transition-all flex items-center space-x-2">
                        <i class="bi bi-shield-check"></i>
                        <span>Guardar Excepciones</span>
                    </button>
                </div>
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

function abrirModalPermisos(userId, userName, userRole, activePerms, userExceptions) {
    const modal = document.getElementById('modalPermisosACL');
    const form = document.getElementById('formPermisosModal');
    const titulo = document.getElementById('modalPermisosTitulo');
    const badge = document.getElementById('modalPermisosRoleBadge');
    
    if (form) {
        form.action = '<?= url('/admin/permisos/guardar/') ?>' + userId;
    }
    if (titulo) {
        titulo.innerText = 'Permisos ACL: ' + userName;
    }
    if (badge) {
        badge.innerText = 'Rol Principal: ' + (userRole || 'Estudiante').toUpperCase();
    }

    // Resetear todas las casillas de conceder y revocar
    document.querySelectorAll('.cb-permiso-conceder').forEach(cb => cb.checked = false);
    document.querySelectorAll('.cb-permiso-revocar').forEach(cb => cb.checked = false);

    // Marcar permisos calculados/activos (Concedidos)
    if (Array.isArray(activePerms)) {
        activePerms.forEach(pId => {
            const cbConc = document.getElementById('permiso_conc_' + pId);
            if (cbConc) cbConc.checked = true;
        });
    }

    // Marcar excepciones específicas (CONCEDER / REVOCAR)
    if (Array.isArray(userExceptions)) {
        userExceptions.forEach(exc => {
            const pId = exc.permiso_id;
            if (exc.tipo === 'REVOCAR') {
                const cbRev = document.getElementById('permiso_rev_' + pId);
                if (cbRev) cbRev.checked = true;
                const cbConc = document.getElementById('permiso_conc_' + pId);
                if (cbConc) cbConc.checked = false;
            } else if (exc.tipo === 'CONCEDER') {
                const cbConc = document.getElementById('permiso_conc_' + pId);
                if (cbConc) cbConc.checked = true;
            }
        });
    }

    if (modal) modal.classList.remove('hidden');
}

function cerrarModalPermisos() {
    const modal = document.getElementById('modalPermisosACL');
    if (modal) modal.classList.add('hidden');
}

function sincronizarCasillaPermiso(pId, modo) {
    if (modo === 'conc') {
        const cbConc = document.getElementById('permiso_conc_' + pId);
        if (cbConc && cbConc.checked) {
            const cbRev = document.getElementById('permiso_rev_' + pId);
            if (cbRev) cbRev.checked = false;
        }
    } else if (modo === 'rev') {
        const cbRev = document.getElementById('permiso_rev_' + pId);
        if (cbRev && cbRev.checked) {
            const cbConc = document.getElementById('permiso_conc_' + pId);
            if (cbConc) cbConc.checked = false;
        }
    }
}

function filtrarPermisosModal() {
    const q = (document.getElementById('filtroPermisoBuscar')?.value || '').toLowerCase().trim();
    document.querySelectorAll('.item-permiso').forEach(item => {
        const txt = item.getAttribute('data-permiso-text') || '';
        item.style.display = (q === '' || txt.includes(q)) ? '' : 'none';
    });
}

function limpiarExcepcionesModal() {
    document.querySelectorAll('.cb-permiso-conceder').forEach(cb => cb.checked = false);
    document.querySelectorAll('.cb-permiso-revocar').forEach(cb => cb.checked = false);
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

