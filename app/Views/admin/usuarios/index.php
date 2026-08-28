<?php
/**
 * Vista: Listado de Usuarios y Roles con Modal de Permisos en Menús Desplegables (Tailwind CSS v3)
 */
$titulo = 'Gestión de Usuarios y Roles';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

// Estructurar el catálogo de permisos por Vista / Módulo para el Modal
$vistasCat = [];
if (!empty($todosPermisos)) {
    foreach ($todosPermisos as $p) {
        $nombre = $p['nombre'] ?? '';
        $partes = explode('.', $nombre);
        
        $modulo = $p['modulo'] ?? $partes[0] ?? 'general';
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
        }

        if (!isset($vistasCat[$vistaKey])) {
            $vistasCat[$vistaKey] = [
                'clave' => $vistaKey,
                'titulo' => $vistaTitulo,
                'modulo' => $modulo,
                'permisos' => []
            ];
        }
        $vistasCat[$vistaKey]['permisos'][] = $p;
    }
}
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestión de Usuarios y Roles</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Administración de cuentas de acceso, asignación de roles y permisología ACL.</p>
    </div>
    <?php if (in_array('admin.usuarios.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
        <a href="<?= url('/admin/usuarios/crear') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Usuario</span>
        </a>
    <?php endif; ?>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol de Usuario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($usuarios as $usuario): 
                    $uId = (int)$usuario['id'];
                    $userPerms = $usuarioPermisosMap[$uId] ?? [];
                ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= $uId ?></td>
                        <td class="font-medium text-slate-800 dark:text-slate-200"><?= htmlspecialchars($usuario['cedula']) ?></td>
                        <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                        <td class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($usuario['email']) ?></td>
                        <td>
                            <?php 
                                $tColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300';
                                $badgeIcon = 'bi-person';
                                if ($usuario['tipo'] === 'admin') {
                                    $tColor = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300';
                                    $badgeIcon = 'bi-shield-check';
                                } elseif ($usuario['tipo'] === 'docente') {
                                    $tColor = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300';
                                    $badgeIcon = 'bi-person-workspace';
                                } elseif ($usuario['tipo'] === 'estudiante') {
                                    $tColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
                                    $badgeIcon = 'bi-mortarboard';
                                }
                            ?>
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold <?= $tColor ?>">
                                <i class="bi <?= $badgeIcon ?>"></i>
                                <span><?= ucfirst($usuario['tipo']) ?></span>
                            </span>
                        </td>
                        <td>
                            <?php if ($usuario['estado'] == 1): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <a href="<?= url('/admin/usuarios/editar/' . $uId) ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors" title="Editar Rol y Datos">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- BOTÓN LLAVE: ABRE MODAL INTERACTIVO DE PERMISOS -->
                                <button type="button" onclick='abrirModalPermisos(<?= $uId ?>, <?= json_encode($usuario["nombre"] . " " . $usuario["apellido"]) ?>, <?= json_encode($userPerms) ?>)' 
                                        class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Permisos ACL (Modal Desplegable)">
                                    <i class="bi bi-key-fill"></i>
                                </button>

                                <?php if ($uId !== 1): ?>
                                    <button type="button" onclick="confirmarEliminar(<?= $uId ?>)" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar Usuario">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ════════════════ MODAL REESTRUCTURADO: PERMISOS ACL COMPACTO ════════════════ -->
<div id="modalPermisosACL" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-5 max-h-[90vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3 shrink-0">
            <div class="flex items-center space-x-3 text-amber-500">
                <i class="bi bi-shield-lock-fill text-2xl"></i>
                <div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalPermisosTitulo">Asignar Permisos ACL</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Configure los accesos a vistas y acciones desplegables.</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalPermisos()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Formulario -->
        <form id="formPermisosModal" method="POST" action="" class="flex-1 overflow-y-auto pr-1 space-y-4 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <!-- Quick Global Action Controls -->
            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Control Rápido de Permisos:</span>
                <div class="space-x-3">
                    <button type="button" onclick="seleccionarTodosModal(true)" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Marcar Todos
                    </button>
                    <span class="text-slate-300 dark:text-slate-700">|</span>
                    <button type="button" onclick="seleccionarTodosModal(false)" class="text-xs font-bold text-slate-500 hover:underline">
                        Desmarcar Todos
                    </button>
                </div>
            </div>

            <!-- COLLAPSIBLE ACCORDION LIST PER VIEW -->
            <div class="space-y-3">
                <?php foreach ($vistasCat as $vKey => $vInfo): ?>
                    <details class="group bg-slate-50/70 dark:bg-slate-900/50 rounded-xl border border-slate-200/80 dark:border-slate-700/70 overflow-hidden">
                        <summary class="flex items-center justify-between p-3.5 font-bold text-xs text-slate-800 dark:text-slate-200 cursor-pointer select-none bg-white dark:bg-slate-800 hover:bg-slate-100/80 dark:hover:bg-slate-700/60 transition-colors">
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

                        <!-- Permisos Items -->
                        <div class="p-3 space-y-2 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/40 dark:bg-slate-900/40">
                            <?php foreach ($vInfo['permisos'] as $p): 
                                $pId = (int)($p['id'] ?? $p['id_permiso']);
                                $desc = $p['descripcion'] ?? $p['nombre'] ?? '';
                                $nombrePermiso = $p['nombre'] ?? '';
                                $isVer = str_ends_with($nombrePermiso, '.ver') || str_ends_with($nombrePermiso, '.dashboard');
                            ?>
                                <label for="m_permiso_<?= $pId ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800/80 transition-colors cursor-pointer select-none border border-transparent hover:border-slate-200 dark:hover:border-slate-700">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" name="permisos[]" value="<?= $pId ?>" id="m_permiso_<?= $pId ?>" data-group="<?= $vKey ?>"
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
            </div>

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

<form id="formEliminar" method="POST" action="<?= url('/admin/usuarios/eliminar') ?>">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
    <input type="hidden" name="id_usuario" id="idUsuarioEliminar">
</form>

<script>
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

    // Desmarcar todos los checkboxes
    document.querySelectorAll('.modal-permiso-cb').forEach(cb => cb.checked = false);

    // Marcar únicamente los permisos asignados a este usuario
    if (Array.isArray(activePerms)) {
        activePerms.forEach(pId => {
            const cb = document.getElementById('m_permiso_' + pId);
            if (cb) cb.checked = true;
        });
    }

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

function confirmarEliminar(id) {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        document.getElementById('idUsuarioEliminar').value = id;
        document.getElementById('formEliminar').submit();
    }
}

$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        responsive: true
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>