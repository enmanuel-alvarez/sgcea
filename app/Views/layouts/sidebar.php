<?php
/**
 * Layout - Sidebar (Tailwind CSS v3)
 * Menú lateral dinámico de navegación operacional
 */
$tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
$permisos_sesion = $_SESSION['usuario_permisos'] ?? [];
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

// Helper para verificar permiso individual
$tienePermiso = function(string $permiso) use ($tipoUsuario, $permisos_sesion) {
    if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === 1) return true; // Superadmin principal ID 1
    return in_array($permiso, $permisos_sesion);
};
?>

<!-- SIDEBAR BACKDROP FOR MOBILE -->
<div id="mobileSidebarBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity no-print"></div>

<!-- SIDEBAR NAVIGATION -->
<aside id="sidebarMenu" class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700/80 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out shrink-0 overflow-y-auto no-print">
    <div class="p-4 space-y-6 flex-1">
        
        <?php if ($tipoUsuario === 'admin' || (!in_array($tipoUsuario, ['docente', 'estudiante']))): ?>
        <!-- ═══ Menú Administrador / Rol Personalizado ═══ -->
        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Principal</h3>
            <nav class="space-y-1">
                <?php if ($tienePermiso('admin.dashboard')): ?>
                <?php $isActive = $currentUri === '/admin' || $currentUri === '/admin/' || $currentUri === '/sgcea/public/admin'; ?>
                <a href="<?= url('/admin') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-speedometer2 text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('reportes.ver')): ?>
                <?php $isAct = strpos($currentUri, '/reportes') !== false; ?>
                <a href="<?= url('/reportes') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-bar-chart-line text-lg"></i>
                    <span>Reportes</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Gestión Escolar</h3>
            <nav class="space-y-1">
                <?php if ($tienePermiso('admin.estudiantes.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/estudiantes') !== false; ?>
                <a href="<?= url('/admin/estudiantes') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-person-badge text-lg"></i>
                    <span>Estudiantes</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('admin.docentes.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/docentes') !== false; ?>
                <a href="<?= url('/admin/docentes') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-person-workspace text-lg"></i>
                    <span>Docentes</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('admin.materias.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/materias') !== false; ?>
                <a href="<?= url('/admin/materias') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-book text-lg"></i>
                    <span>Materias</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('admin.secciones.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/secciones') !== false; ?>
                <a href="<?= url('/admin/secciones') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-door-open text-lg"></i>
                    <span>Secciones</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('admin.asignaciones.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/asignaciones') !== false; ?>
                <a href="<?= url('/admin/asignaciones') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-diagram-3 text-lg"></i>
                    <span>Asignaciones</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('admin.constancias.ver')): ?>
                <?php $isAct = strpos($currentUri, '/admin/constancias') !== false; ?>
                <a href="<?= url('/admin/constancias') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-file-earmark-text text-lg"></i>
                    <span>Constancias</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

        <?php if ($tipoUsuario === 'docente'): ?>
        <!-- ═══ Menú Docente ═══ -->
        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Principal</h3>
            <nav class="space-y-1">
                <?php $isActive = $currentUri === '/docente' || $currentUri === '/docente/' || $currentUri === '/sgcea/public/docente'; ?>
                <a href="<?= url('/docente') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-speedometer2 text-lg"></i>
                    <span>Dashboard</span>
                </a>
            </nav>
        </div>

        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Académico</h3>
            <nav class="space-y-1">
                <?php if ($tienePermiso('docente.calificaciones.ver')): ?>
                <?php $isAct = strpos($currentUri, '/docente/calificaciones') !== false || strpos($currentUri, '/docente/planevaluacion') !== false || strpos($currentUri, '/docente/plan-evaluacion') !== false; ?>
                <a href="<?= url('/docente/calificaciones') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-journal-check text-lg"></i>
                    <span>Calificaciones / Planes</span>
                </a>

                <?php $isRev = strpos($currentUri, '/docente/revisiones') !== false; ?>
                <a href="<?= url('/docente/revisiones') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isRev ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-chat-square-text text-lg"></i>
                    <span>Revisiones de Notas</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('docente.asistencia.ver')): ?>
                <?php $isAct = strpos($currentUri, '/docente/asistencia') !== false; ?>
                <a href="<?= url('/docente/asistencia') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-calendar-check text-lg"></i>
                    <span>Asistencia</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

        <?php if ($tipoUsuario === 'estudiante'): ?>
        <!-- ═══ Menú Estudiante ═══ -->
        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Principal</h3>
            <nav class="space-y-1">
                <?php $isActive = $currentUri === '/estudiante' || $currentUri === '/estudiante/' || $currentUri === '/sgcea/public/estudiante'; ?>
                <a href="<?= url('/estudiante') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isActive ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-speedometer2 text-lg"></i>
                    <span>Dashboard</span>
                </a>
            </nav>
        </div>

        <div>
            <h3 class="px-3 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Académico</h3>
            <nav class="space-y-1">
                <?php if ($tienePermiso('estudiante.boletin.ver')): ?>
                <?php $isAct = strpos($currentUri, '/estudiante/boletin') !== false; ?>
                <a href="<?= url('/estudiante/boletin') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-file-earmark-spreadsheet text-lg"></i>
                    <span>Boletín de Notas</span>
                </a>

                <?php $isRev = strpos($currentUri, '/estudiante/revisiones') !== false; ?>
                <a href="<?= url('/estudiante/revisiones') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isRev ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-chat-left-dots text-lg"></i>
                    <span>Solicitudes de Revisión</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('estudiante.asistencia.ver')): ?>
                <?php $isAct = strpos($currentUri, '/estudiante/asistencia') !== false; ?>
                <a href="<?= url('/estudiante/asistencia') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-calendar-check text-lg"></i>
                    <span>Asistencia</span>
                </a>
                <?php endif; ?>

                <?php if ($tienePermiso('estudiante.constancias.solicitar')): ?>
                <?php $isAct = strpos($currentUri, '/estudiante/constancias') !== false; ?>
                <a href="<?= url('/estudiante/constancias/solicitar') ?>" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $isAct ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold border-l-4 border-blue-600' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white' ?>">
                    <i class="bi bi-file-earmark-text text-lg"></i>
                    <span>Constancias</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

    </div>
</aside>

<!-- MAIN CONTENT CANVAS -->
<main class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto animate-fade-in min-w-0">


