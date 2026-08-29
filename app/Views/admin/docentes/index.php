<?php
/**
 * Vista: Admin - Gestión de Docentes con Modales Flotantes y Doble Factor (Tailwind CSS v3)
 */
$titulo = 'Gestión de Docentes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Docentes</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Directorio de profesores y personal académico institucional.</p>
    </div>
    <?php if (in_array('admin.docentes.crear', $_SESSION['usuario_permisos'] ?? []) || in_array('docentes.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
        <button type="button" onclick="abrirModalCrearDocente()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-person-workspace"></i>
            <span>Nuevo Docente</span>
        </button>
    <?php endif; ?>
</div>

<!-- Tabla Principal -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaDocentes" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <th class="pb-3">Cédula</th>
                    <th class="pb-3">Nombres</th>
                    <th class="pb-3">Apellidos</th>
                    <th class="pb-3">Especialidad</th>
                    <th class="pb-3">Teléfono</th>
                    <th class="pb-3">Correo</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($docentes as $doc): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-medium text-slate-800 dark:text-slate-200"><?= htmlspecialchars($doc['cedula']) ?></td>
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($doc['nombres'] ?? $doc['nombre'] ?? '') ?></td>
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($doc['apellidos'] ?? $doc['apellido'] ?? '') ?></td>
                    <td class="text-slate-600 dark:text-slate-300">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">
                            <?= htmlspecialchars($doc['especialidad'] ?? 'General') ?>
                        </span>
                    </td>
                    <td class="text-slate-500 dark:text-slate-400 text-xs"><?= htmlspecialchars($doc['telefono'] ?? 'N/A') ?></td>
                    <td class="text-slate-600 dark:text-slate-300 font-mono text-xs"><?= htmlspecialchars($doc['correo'] ?? $doc['email'] ?? 'N/A') ?></td>
                    <td>
                        <?php if (($doc['activo'] ?? $doc['estado'] ?? 1) == 1): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activo
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactivo
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" onclick='abrirModalEditarDocente(<?= json_encode($doc) ?>)' class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Editar Docente">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" onclick="confirmarEliminar2FA('<?= url('/admin/docentes/eliminar/' . $doc['id']) ?>', '<?= htmlspecialchars(($doc['nombres'] ?? $doc['nombre'] ?? '') . ' ' . ($doc['apellidos'] ?? $doc['apellido'] ?? '')) ?>')" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar Docente">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ════════════════ MODAL FLOTANTE: FORMULARIO DOCENTE ════════════════ -->
<div id="modalFormDocente" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-4 shrink-0">
            <div class="flex items-center space-x-3 text-blue-600 dark:text-blue-400">
                <i class="bi bi-person-workspace text-2xl"></i>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalDocenteTitulo">Registrar Docente</h3>
            </div>
            <button type="button" onclick="cerrarModalDocente()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formDocente" method="POST" action="" onsubmit="validarEnvioDocente(event)" class="flex-1 overflow-y-auto pr-1 space-y-5 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="doc_cedula" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Cédula *</label>
                    <input type="text" id="doc_cedula" name="cedula" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="doc_correo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Correo Electrónico *</label>
                    <input type="email" id="doc_correo" name="correo" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="doc_nombre" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nombres *</label>
                    <input type="text" id="doc_nombre" name="nombre" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="doc_apellido" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Apellidos *</label>
                    <input type="text" id="doc_apellido" name="apellido" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="doc_telefono" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Teléfono</label>
                    <input type="text" id="doc_telefono" name="telefono" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="doc_especialidad" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Especialidad</label>
                    <input type="text" id="doc_especialidad" name="especialidad" placeholder="Ej: Ciencias, Matemáticas" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="doc_titulo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Título Académico</label>
                    <input type="text" id="doc_titulo" name="titulo" placeholder="Ej: Lcdo. en Educación" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label for="doc_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5" id="lblDocPassword">Contraseña *</label>
                    <input type="password" id="doc_password" name="password" minlength="6" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div>
                <label for="doc_direccion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Dirección de Habitación</label>
                <input type="text" id="doc_direccion" name="direccion" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" id="doc_activo" name="activo" value="1" checked class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="doc_activo" class="text-xs font-semibold text-slate-800 dark:text-slate-200">Usuario Activo en el Sistema</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
                <button type="button" onclick="cerrarModalDocente()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span id="btnDocenteSubmitTexto">Guardar Docente</span>
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
let esEdicionDocente = false;
let formPendienteSubmit = null;

function abrirModalCrearDocente() {
    esEdicionDocente = false;
    const form = document.getElementById('formDocente');
    form.reset();
    form.action = '<?= url('/admin/docentes/guardar') ?>';
    document.getElementById('modalDocenteTitulo').innerText = 'Registrar Docente';
    document.getElementById('btnDocenteSubmitTexto').innerText = 'Guardar Docente';
    document.getElementById('lblDocPassword').innerText = 'Contraseña *';
    document.getElementById('doc_password').required = true;
    document.getElementById('modalFormDocente').classList.remove('hidden');
}

function abrirModalEditarDocente(doc) {
    esEdicionDocente = true;
    const form = document.getElementById('formDocente');
    form.reset();
    form.action = '<?= url('/admin/docentes/actualizar/') ?>' + doc.id;
    
    document.getElementById('modalDocenteTitulo').innerText = 'Editar Docente: ' + (doc.nombres || doc.nombre || '');
    document.getElementById('btnDocenteSubmitTexto').innerText = 'Actualizar Docente';
    document.getElementById('lblDocPassword').innerText = 'Nueva Contraseña (Opcional)';
    document.getElementById('doc_password').required = false;

    document.getElementById('doc_cedula').value = doc.cedula || '';
    document.getElementById('doc_correo').value = doc.correo || doc.email || '';
    document.getElementById('doc_nombre').value = doc.nombres || doc.nombre || '';
    document.getElementById('doc_apellido').value = doc.apellidos || doc.apellido || '';
    document.getElementById('doc_telefono').value = doc.telefono || '';
    document.getElementById('doc_especialidad').value = doc.especialidad || '';
    document.getElementById('doc_titulo').value = doc.titulo || '';
    document.getElementById('doc_direccion').value = doc.direccion || '';
    document.getElementById('doc_activo').checked = (doc.activo ?? doc.estado ?? 1) == 1;

    document.getElementById('modalFormDocente').classList.remove('hidden');
}

function cerrarModalDocente() {
    document.getElementById('modalFormDocente').classList.add('hidden');
}

function validarEnvioDocente(e) {
    if (esEdicionDocente && !formPendienteSubmit) {
        e.preventDefault();
        formPendienteSubmit = document.getElementById('formDocente');
        document.getElementById('modalConfirmarModificacion').classList.remove('hidden');
    }
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
    document.getElementById('delModalTexto').innerText = '¿Está seguro de eliminar a "' + nombre + '"? Esta operación no se puede deshacer.';
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
    $('#tablaDocentes').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

