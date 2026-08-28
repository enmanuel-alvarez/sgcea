<?php
/**
 * Vista: Admin - Inscribir Estudiante en Sección (Tailwind CSS v3)
 */
$titulo = 'Inscribir Estudiante';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Inscribir Estudiante</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Asignación de matrícula en grado y sección para el año lectivo.</p>
    </div>
    <div>
        <a href="<?= url('/admin/estudiantes') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<!-- Student Info Box -->
<div class="bg-blue-50/50 dark:bg-slate-800/60 border border-blue-200/60 dark:border-slate-700/60 rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
            <i class="bi bi-person-fill"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-900 dark:text-white"><?= e($estudiante['nombres'] ?? $estudiante['nombre'] ?? '') ?> <?= e($estudiante['apellidos'] ?? $estudiante['apellido'] ?? '') ?></h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">Cédula: <?= e($estudiante['cedula'] ?? '') ?></p>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8">
    <form action="<?= url('/admin/estudiantes/inscribir') ?>" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="estudiante_id" value="<?= e($estudiante['id'] ?? '') ?>">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label for="grado_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Grado Academicó *</label>
                <select id="grado_id" name="grado_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione Grado</option>
                    <?php if (!empty($grados)): ?>
                        <?php foreach ($grados as $grado): ?>
                            <option value="<?= e($grado['id']) ?>"><?= e($grado['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="seccion_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Sección *</label>
                <select id="seccion_id" name="seccion_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione Sección</option>
                    <?php if (!empty($secciones)): ?>
                        <?php foreach ($secciones as $seccion): ?>
                            <option value="<?= e($seccion['id']) ?>" data-grado="<?= e($seccion['grado_id']) ?>"><?= e($seccion['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="ano_academico" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Año Académico *</label>
                <input type="text" id="ano_academico" name="ano_academico" value="2024-2025" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
            <a href="<?= url('/admin/estudiantes') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i class="bi bi-check-lg"></i>
                <span>Completar Inscripción</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradoSelect = document.getElementById('grado_id');
    const seccionSelect = document.getElementById('seccion_id');
    if (gradoSelect && seccionSelect) {
        const allSecciones = Array.from(seccionSelect.options).filter(opt => opt.value !== '');

        gradoSelect.addEventListener('change', function() {
            const gradoId = this.value;
            seccionSelect.innerHTML = '<option value="">Seleccione Sección</option>';
            if (gradoId) {
                allSecciones.forEach(opt => {
                    if (opt.dataset.grado === gradoId) {
                        seccionSelect.appendChild(opt.cloneNode(true));
                    }
                });
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
