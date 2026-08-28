<?php
/**
 * Vista: Admin - Gestión de Asignaciones con Modales Flotantes y Doble Factor (Tailwind CSS v3)
 */
$titulo = 'Gestión de Asignaciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Asignaciones Académicas</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Carga docente, distribución de materias y secciones por período.</p>
    </div>
    <?php if (in_array('admin.asignaciones.crear', $_SESSION['usuario_permisos'] ?? []) || in_array('asignaciones.crear', $_SESSION['usuario_permisos'] ?? []) || $_SESSION['usuario_tipo'] === 'admin'): ?>
        <button type="button" onclick="abrirModalCrearAsignacion()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all">
            <i class="bi bi-diagram-3"></i>
            <span>Nueva Asignación</span>
        </button>
    <?php endif; ?>
</div>

<!-- Tabla Principal -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaAsignaciones" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <th class="pb-3">Docente</th>
                    <th class="pb-3">Materia</th>
                    <th class="pb-3">Grado</th>
                    <th class="pb-3">Sección</th>
                    <th class="pb-3">Año Académico</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($asignaciones as $asn): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($asn['docente_nombre'] ?? $asn['profesor_nombre'] ?? '') ?></td>
                    <td class="text-blue-600 dark:text-blue-400 font-medium"><?= htmlspecialchars($asn['materia_nombre'] ?? '') ?></td>
                    <td class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars($asn['grado_nombre'] ?? 'N/A') ?></td>
                    <td class="text-slate-600 dark:text-slate-300">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            <?= htmlspecialchars($asn['seccion_nombre'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td class="font-mono text-xs text-slate-500 dark:text-slate-400"><?= htmlspecialchars($asn['ano_academico'] ?? '2024-2025') ?></td>
                    <td>
                        <?php if (($asn['activo'] ?? $asn['estado'] ?? 1) == 1): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Activa
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 me-1"></span> Inactiva
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" onclick='abrirModalEditarAsignacion(<?= json_encode($asn) ?>)' class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors" title="Editar Asignación">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" onclick="confirmarEliminar2FA('<?= url('/admin/asignaciones/eliminar/' . $asn['id']) ?>', '<?= htmlspecialchars(($asn['docente_nombre'] ?? '') . ' - ' . ($asn['materia_nombre'] ?? '')) ?>')" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:hover:bg-rose-900/50 transition-colors" title="Eliminar Asignación">
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

<!-- ════════════════ MODAL FLOTANTE: FORMULARIO ASIGNACIÓN ════════════════ -->
<div id="modalFormAsignacion" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6 max-h-[90vh] flex flex-col">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-4 shrink-0">
            <div class="flex items-center space-x-3 text-blue-600 dark:text-blue-400">
                <i class="bi bi-diagram-3 text-2xl"></i>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalAsignacionTitulo">Nueva Asignación Académica</h3>
            </div>
            <button type="button" onclick="cerrarModalAsignacion()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formAsignacion" method="POST" action="" onsubmit="validarEnvioAsignacion(event)" class="flex-1 overflow-y-auto pr-1 space-y-4 custom-scrollbar">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div>
                <label for="asn_id_profesor" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Docente / Profesor *</label>
                <select id="asn_id_profesor" name="id_profesor" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Seleccionar Docente --</option>
                    <?php foreach ($docentes as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars(($d['nombres'] ?? $d['nombre'] ?? '') . ' ' . ($d['apellidos'] ?? $d['apellido'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="asn_id_materia" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Materia / Asignatura *</label>
                <select id="asn_id_materia" name="id_materia" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Seleccionar Materia --</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars(($m['codigo'] ?? '') . ' - ' . ($m['nombre'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="asn_id_seccion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Sección / Aula *</label>
                <select id="asn_id_seccion" name="id_seccion" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Seleccionar Sección --</option>
                    <?php foreach ($secciones as $sec): ?>
                        <option value="<?= $sec['id'] ?>">Sección <?= htmlspecialchars($sec['nombre'] ?? '') ?> (<?= htmlspecialchars($sec['grado_nombre'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="asn_ano_academico" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Año Académico *</label>
                    <input type="text" id="asn_ano_academico" name="ano_academico" value="<?= date('Y') ?>" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm font-mono">
                </div>
                <div>
                    <label for="asn_periodo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Período / Lapso *</label>
                    <select id="asn_periodo" name="periodo" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="1">1er Lapso / Período</option>
                        <option value="2">2do Lapso / Período</option>
                        <option value="3">3er Lapso / Período</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" id="asn_activo" name="activo" value="1" checked class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="asn_activo" class="text-xs font-semibold text-slate-800 dark:text-slate-200">Asignación Activa</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
                <button type="button" onclick="cerrarModalAsignacion()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span id="btnAsignacionSubmitTexto">Guardar Asignación</span>
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
let esEdicionAsignacion = false;
let formPendienteSubmit = null;

function abrirModalCrearAsignacion() {
    esEdicionAsignacion = false;
    const form = document.getElementById('formAsignacion');
    form.reset();
    form.action = '<?= url('/admin/asignaciones/guardar') ?>';
    document.getElementById('modalAsignacionTitulo').innerText = 'Nueva Asignación Académica';
    document.getElementById('btnAsignacionSubmitTexto').innerText = 'Guardar Asignación';
    document.getElementById('modalFormAsignacion').classList.remove('hidden');
}

function abrirModalEditarAsignacion(asn) {
    esEdicionAsignacion = true;
    const form = document.getElementById('formAsignacion');
    form.reset();
    form.action = '<?= url('/admin/asignaciones/actualizar/') ?>' + asn.id;
    
    document.getElementById('modalAsignacionTitulo').innerText = 'Editar Asignación Académica';
    document.getElementById('btnAsignacionSubmitTexto').innerText = 'Actualizar Asignación';

    document.getElementById('asn_id_profesor').value = asn.id_profesor || asn.id_docente || asn.profesor_id || '';
    document.getElementById('asn_id_materia').value = asn.id_materia || asn.materia_id || '';
    document.getElementById('asn_id_seccion').value = asn.id_seccion || asn.seccion_id || '';
    document.getElementById('asn_ano_academico').value = asn.ano_academico || '<?= date("Y") ?>';
    document.getElementById('asn_periodo').value = asn.periodo || '1';
    document.getElementById('asn_activo').checked = (asn.activo ?? asn.estado ?? 1) == 1;

    document.getElementById('modalFormAsignacion').classList.remove('hidden');
}

function cerrarModalAsignacion() {
    document.getElementById('modalFormAsignacion').classList.add('hidden');
}

function validarEnvioAsignacion(e) {
    if (esEdicionAsignacion && !formPendienteSubmit) {
        e.preventDefault();
        formPendienteSubmit = document.getElementById('formAsignacion');
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
    document.getElementById('delModalTexto').innerText = '¿Está seguro de eliminar la asignación "' + nombre + '"? Esta operación no se puede deshacer.';
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
    $('#tablaAsignaciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
