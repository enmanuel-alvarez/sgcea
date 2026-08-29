<?php
/**
 * Vista: Estudiante - Solicitar Constancia (Tailwind CSS v3)
 */
$titulo = 'Solicitar Constancia';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Solicitud de Constancias</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Emisión de documentos oficiales de estudio, notas y conducta.</p>
    </div>
    <div>
        <a href="<?= url('/estudiante/dashboard') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver al Panel</span>
        </a>
    </div>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <i class="bi bi-file-earmark-text text-purple-500"></i>
            <span>Formulario de Solicitud</span>
        </h3>

        <form action="<?= url('/estudiante/constancias/guardar') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label for="tipo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Tipo de Constancia *</label>
                <select id="tipo" name="tipo" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    <option value="">Seleccione un tipo de documento</option>
                    <option value="estudio">Constancia de Estudio</option>
                    <option value="conducta">Constancia de Buena Conducta</option>
                    <option value="notas">Constancia / Certificación de Notas</option>
                </select>
            </div>

            <div>
                <label for="motivo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Motivo / Institución Destino *</label>
                <textarea id="motivo" name="motivo" rows="4" required placeholder="Indique brevemente el trámite, beca o institución donde presentará el documento..."
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="<?= url('/estudiante/dashboard') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-purple-500/20 transition-all flex items-center space-x-2">
                    <i class="bi bi-send"></i>
                    <span>Enviar Solicitud</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

