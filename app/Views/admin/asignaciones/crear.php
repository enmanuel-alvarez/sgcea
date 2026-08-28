<?php
/**
 * Vista: Admin - Crear Asignación (Tailwind CSS v3)
 */
$titulo = 'Nueva Asignación';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Nueva Asignación</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Asignación de carga académica a docente por materia y sección.</p>
    </div>
    <div>
        <a href="<?= url('/admin/asignaciones') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </a>
    </div>
</div>

<?php if (isset($errores)): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 text-sm">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('/admin/asignaciones/guardar') ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <i class="bi bi-diagram-3 text-blue-500"></i>
            <span>Parámetros de la Asignación</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="id_profesor" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Docente / Profesor *</label>
                <select id="id_profesor" name="id_profesor" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione Docente</option>
                    <?php if (!empty($docentes)): ?>
                        <?php foreach ($docentes as $doc): ?>
                            <option value="<?= $doc['id'] ?>"><?= htmlspecialchars(($doc['nombres'] ?? '') . ' ' . ($doc['apellidos'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="id_materia" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Materia *</label>
                <select id="id_materia" name="id_materia" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione Materia</option>
                    <?php if (!empty($materias)): ?>
                        <?php foreach ($materias as $mat): ?>
                            <option value="<?= $mat['id'] ?>"><?= htmlspecialchars($mat['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="id_seccion" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Sección Académica *</label>
                <select id="id_seccion" name="id_seccion" required
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Seleccione Sección</option>
                    <?php if (!empty($secciones)): ?>
                        <?php foreach ($secciones as $sec): ?>
                            <option value="<?= $sec['id'] ?>"><?= htmlspecialchars(($sec['grado_nombre'] ?? '') . ' - ' . $sec['nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="ano_academico" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Año Académico *</label>
                <input type="text" id="ano_academico" name="ano_academico" value="2024-2025" required maxlength="9"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 pt-4">
        <a href="<?= url('/admin/asignaciones') ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition-all">Cancelar</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm shadow-md shadow-blue-500/20 transition-all flex items-center space-x-2">
            <i class="bi bi-save"></i>
            <span>Guardar Asignación</span>
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
