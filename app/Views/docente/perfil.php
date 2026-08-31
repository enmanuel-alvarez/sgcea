<?php
/**
 * Vista: Docente - Mi Perfil (Tailwind CSS v3)
 */
$titulo = 'Mi Perfil';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Mi Perfil Docente</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Información académica, credencial de identificación y actualización de datos.</p>
    </div>
    <div class="flex items-center space-x-2">
        <button type="button" onclick="abrirModalCarnet()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-purple-500/20 transition-all">
            <i class="bi bi-person-badge"></i>
            <span>Imprimir Carnet Docente</span>
        </button>
        <a href="<?= url('/docente/dashboard') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Docente User Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 text-center space-y-4">
        <?php if (!empty($docente['foto']) && file_exists(__DIR__ . '/../../public/' . $docente['foto'])): ?>
            <img src="<?= url('/' . $docente['foto']) ?>" alt="Foto Perfil" class="w-28 h-32 mx-auto rounded-2xl object-cover border-2 border-purple-500/50 shadow-md">
        <?php else: ?>
            <div class="w-24 h-24 mx-auto rounded-full bg-purple-600/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-3xl border-2 border-purple-500/20">
                <i class="bi bi-person-workspace"></i>
            </div>
        <?php endif; ?>
        <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white"><?= htmlspecialchars(($docente['nombres'] ?? $docente['nombre'] ?? '') . ' ' . ($docente['apellidos'] ?? $docente['apellido'] ?? '')) ?></h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">Cédula: <?= htmlspecialchars($docente['cedula'] ?? '') ?></p>
        </div>

        <div class="pt-2 flex flex-col items-center space-y-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200/50">
                <?= htmlspecialchars($docente['especialidad'] ?? 'Docente de Aula') ?>
            </span>
            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 me-1"></span> Personal Activo
            </span>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/50">
            <button type="button" onclick="abrirModalCarnet()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="bi bi-card-heading text-base"></i>
                <span>Ver / Imprimir Carnet</span>
            </button>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3 mb-6">
            <i class="bi bi-sliders text-purple-500"></i>
            <span>Actualizar Datos Académicos y Contacto</span>
        </h3>

        <form action="<?= url('/docente/perfil/actualizar') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

            <div>
                <label for="foto" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Foto de Perfil / Carnet (Opcional)</label>
                <input type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/webp" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="telefono" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Teléfono de Contacto</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($docente['telefono'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label for="especialidad" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Especialidad / Asignatura Principal</label>
                    <input type="text" id="especialidad" name="especialidad" value="<?= htmlspecialchars($docente['especialidad'] ?? '') ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="<?= url('/docente/dashboard') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-purple-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-save"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════ MODAL FLOTANTE: CARNET DOCENTE ════════════════ -->
<div id="modalCarnet" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-6 space-y-4 max-h-[92vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3 shrink-0">
            <div class="flex items-center space-x-2 text-purple-600 dark:text-purple-400">
                <i class="bi bi-person-badge text-2xl"></i>
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Carnet Docente Oficial</h3>
            </div>
            <button type="button" onclick="cerrarModalCarnet()" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 w-full bg-slate-100 dark:bg-slate-900 rounded-2xl overflow-hidden min-h-[420px]">
            <iframe id="iframeCarnet" src="" class="w-full h-full border-0 min-h-[420px]"></iframe>
        </div>

        <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
            <button type="button" onclick="cerrarModalCarnet()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs hover:bg-slate-200">
                Cerrar
            </button>
            <button type="button" onclick="imprimirIframeCarnet()" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs shadow-md shadow-purple-500/20 flex items-center space-x-2">
                <i class="bi bi-printer-fill text-sm"></i>
                <span>Imprimir / Exportar PDF</span>
            </button>
        </div>
    </div>
</div>

<script>
function abrirModalCarnet() {
    const iframe = document.getElementById('iframeCarnet');
    iframe.src = '<?= url('/carnet/docente') ?>';
    document.getElementById('modalCarnet').classList.remove('hidden');
}

function cerrarModalCarnet() {
    document.getElementById('modalCarnet').classList.add('hidden');
    document.getElementById('iframeCarnet').src = '';
}

function imprimirIframeCarnet() {
    const iframe = document.getElementById('iframeCarnet');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
