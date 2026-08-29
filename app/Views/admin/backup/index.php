<?php
/**
 * Vista: Admin - Copias de Seguridad y Zona de Peligro (Tailwind CSS v3)
 */
$titulo = 'Copias de Seguridad & Mantenimiento';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Copias de Seguridad y Mantenimiento</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Exportación, importación de datos en formato JSON y Zona de Peligro del sistema.</p>
    </div>
</div>

<!-- MAIN ACTION HERO CARD WITH 3 BUTTONS -->
<div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 shadow-md p-8 sm:p-10 space-y-8 max-w-5xl mx-auto">
    <div class="text-center space-y-2">
        <div class="inline-flex p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-3xl mb-2">
            <i class="bi bi-database-gear"></i>
        </div>
        <h2 class="text-xl font-black text-slate-900 dark:text-white">Respaldo, Restauración y Reinicio de Datos</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xl mx-auto">
            Seleccione una opción para desplegar la ventana de exportación, importación o reinicio controlado de la base de datos.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
        <!-- Button 1: Export -->
        <button type="button" onclick="openExportModal()" 
                class="group p-6 rounded-2xl bg-gradient-to-b from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold shadow-lg shadow-blue-500/25 transition-all text-left flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between w-full">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl">
                    <i class="bi bi-download"></i>
                </div>
                <i class="bi bi-arrow-right text-xl opacity-70 group-hover:translate-x-1 transition-transform"></i>
            </div>
            <div>
                <h3 class="text-base font-bold">Exportar Data (JSON)</h3>
                <p class="text-xs text-blue-100 mt-1 font-normal">Generar archivo descargable seleccionando módulos.</p>
            </div>
        </button>

        <!-- Button 2: Import -->
        <button type="button" onclick="openImportModal()" 
                class="group p-6 rounded-2xl bg-gradient-to-b from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold shadow-lg shadow-emerald-500/25 transition-all text-left flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between w-full">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl">
                    <i class="bi bi-upload"></i>
                </div>
                <i class="bi bi-arrow-right text-xl opacity-70 group-hover:translate-x-1 transition-transform"></i>
            </div>
            <div>
                <h3 class="text-base font-bold">Importar Data (JSON)</h3>
                <p class="text-xs text-emerald-100 mt-1 font-normal">Subir archivo de respaldo previamente exportado.</p>
            </div>
        </button>

        <!-- Button 3: Danger Zone -->
        <button type="button" onclick="openDangerModal()" 
                class="group p-6 rounded-2xl bg-gradient-to-b from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-semibold shadow-lg shadow-rose-600/25 transition-all text-left flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between w-full">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <i class="bi bi-arrow-right text-xl opacity-70 group-hover:translate-x-1 transition-transform"></i>
            </div>
            <div>
                <h3 class="text-base font-bold">Zona de Peligro</h3>
                <p class="text-xs text-rose-100 mt-1 font-normal">Reiniciar datos operacionales a su estado inicial.</p>
            </div>
        </button>
    </div>
</div>

<!-- ════════════════ MODAL 1: EXPORTAR DATA ════════════════ -->
<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <div class="flex items-center space-x-3 text-blue-600 dark:text-blue-400">
                <i class="bi bi-download text-2xl"></i>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Exportar Datos a JSON</h3>
            </div>
            <button type="button" onclick="closeExportModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="<?= url('/admin/backup/exportar') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Seleccione las tablas a incluir:</span>
                <button type="button" onclick="toggleAllExport()" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Marcar / Desmarcar Todas
                </button>
            </div>

            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach ($tablas as $key => $label): ?>
                    <label for="exp_<?= $key ?>" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:bg-blue-50/50 dark:hover:bg-slate-700/40 transition-colors cursor-pointer select-none">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="tablas[]" value="<?= $key ?>" id="exp_<?= $key ?>" checked
                                   class="export-cb h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($label) ?></span>
                        </div>
                        <span class="font-mono text-[11px] text-slate-400 dark:text-slate-500 me-1"><?= htmlspecialchars($key) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i>
                    <span>Generar y Descargar JSON</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════ MODAL 2: IMPORTAR DATA ════════════════ -->
<div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <div class="flex items-center space-x-3 text-emerald-600 dark:text-emerald-400">
                <i class="bi bi-upload text-2xl"></i>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Importar Datos desde JSON</h3>
            </div>
            <button type="button" onclick="closeImportModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="<?= url('/admin/backup/importar') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <!-- File Input Picker -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">1. Seleccionar Archivo JSON *</label>
                <input type="file" name="archivo_json" accept=".json,application/json" required
                       class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/50 dark:file:text-emerald-300 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900">
            </div>

            <!-- Entities selector -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">2. Entidades a Restaurar:</span>
                    <button type="button" onclick="toggleAllImport()" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                        Marcar / Desmarcar Todas
                    </button>
                </div>

                <div class="space-y-2.5 max-h-56 overflow-y-auto pr-2 custom-scrollbar">
                    <?php foreach ($tablas as $key => $label): ?>
                        <label for="imp_<?= $key ?>" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:bg-emerald-50/50 dark:hover:bg-slate-700/40 transition-colors cursor-pointer select-none">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="tablas_importar[]" value="<?= $key ?>" id="imp_<?= $key ?>" checked
                                       class="import-cb h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($label) ?></span>
                            </div>
                            <span class="font-mono text-[11px] text-slate-400 dark:text-slate-500 me-1"><?= htmlspecialchars($key) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" onclick="return confirm('¿Confirma la importación de datos desde el archivo seleccionado?')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-database-add me-1"></i>
                    <span>Iniciar Importación</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════ MODAL 3: DANGER ZONE (REINICIAR SISTEMA) ════════════════ -->
<div id="dangerModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-3xl border border-rose-200 dark:border-rose-900 shadow-2xl p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <div class="flex items-center space-x-3 text-rose-600 dark:text-rose-400">
                <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                <h3 class="font-black text-lg text-slate-900 dark:text-white">Reiniciar Data del Sistema</h3>
            </div>
            <button type="button" onclick="closeDangerModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-200 text-xs leading-relaxed space-y-2">
            <p class="font-bold">¡ADVERTENCIA CRÍTICA!</p>
            <p>Esta acción eliminará de forma irreversible:</p>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                <li>Todas las calificaciones y planes de evaluación</li>
                <li>Control de asistencias e historial de clases</li>
                <li>Solicitudes e historial de constancias</li>
                <li>Registros de estudiantes y docentes</li>
                <li>Asignaciones académicas y bitácoras de auditoría</li>
            </ul>
            <p class="font-medium pt-1">Su cuenta de administrador se mantendrá activa.</p>
        </div>

        <form action="<?= url('/admin/configuracion/reiniciar') ?>" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <!-- Checkbox obligatorio -->
            <label for="confirm_danger_cb" class="flex items-start space-x-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 cursor-pointer select-none">
                <input type="checkbox" name="confirmar_checkbox" value="1" id="confirm_danger_cb" onchange="validateDangerForm()"
                       class="mt-0.5 h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-rose-600 focus:ring-rose-500">
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Comprendo las consecuencias y deseo reiniciar permanentemente toda la data operacional del sistema.
                </span>
            </label>

            <!-- Input Frase de confirmación -->
            <div>
                <label for="confirm_danger_phrase" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Escriba la frase de confirmación: <strong class="text-rose-600 dark:text-rose-400 select-all">REINICIAR SISTEMA SGCEA</strong>
                </label>
                <input type="text" id="confirm_danger_phrase" name="frase_confirmacion" oninput="validateDangerForm()" placeholder="REINICIAR SISTEMA SGCEA" autocomplete="off"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 font-mono text-sm uppercase">
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" onclick="closeDangerModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">
                    Cancelar
                </button>
                <button type="submit" id="btn_submit_danger" disabled
                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 disabled:bg-slate-300 dark:disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl text-xs shadow-md transition-all flex items-center space-x-2">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Confirmar y Reiniciar Datos</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExportModal() { document.getElementById('exportModal').classList.remove('hidden'); }
function closeExportModal() { document.getElementById('exportModal').classList.add('hidden'); }
function openImportModal() { document.getElementById('importModal').classList.remove('hidden'); }
function closeImportModal() { document.getElementById('importModal').classList.add('hidden'); }
function openDangerModal() { document.getElementById('dangerModal').classList.remove('hidden'); }
function closeDangerModal() { document.getElementById('dangerModal').classList.add('hidden'); }

function toggleAllExport() {
    const cbs = document.querySelectorAll('.export-cb');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}
function toggleAllImport() {
    const cbs = document.querySelectorAll('.import-cb');
    const allChecked = Array.from(cbs).every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
}

function validateDangerForm() {
    const cb = document.getElementById('confirm_danger_cb');
    const phrase = document.getElementById('confirm_danger_phrase');
    const btn = document.getElementById('btn_submit_danger');
    
    if (cb && phrase && btn) {
        const isCbChecked = cb.checked;
        const isPhraseValid = phrase.value.trim().toUpperCase() === 'REINICIAR SISTEMA SGCEA';
        btn.disabled = !(isCbChecked && isPhraseValid);
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

