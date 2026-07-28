<?php
/**
 * Layout - Sidebar dinámico según permisos
 */
$tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
$permisos = $_SESSION['usuario_permisos'] ?? [];
$nombreCompleto = ($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '');
?>

<div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="bi bi-mortarboard-fill me-2"></i>
        SGCEA
    </div>
    
    <div class="list-group list-group-flush">
        <?php if ($tipoUsuario === 'admin' || in_array('admin.dashboard', $permisos)): ?>
            <!-- Menú Administrador -->
            <a href="<?= url('/admin') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') === false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            <?php if (in_array('admin.usuarios.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/usuarios') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') !== false ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i> Usuarios
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.estudiantes.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/estudiantes') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/estudiantes') !== false ? 'active' : '' ?>">
                    <i class="bi bi-person-badge me-2"></i> Estudiantes
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.docentes.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/docentes') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/docentes') !== false ? 'active' : '' ?>">
                    <i class="bi bi-chalkboard-teacher me-2"></i> Docentes
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.materias.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/materias') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/materias') !== false ? 'active' : '' ?>">
                    <i class="bi bi-book me-2"></i> Materias
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.secciones.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/secciones') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/secciones') !== false ? 'active' : '' ?>">
                    <i class="bi bi-collection me-2"></i> Secciones
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.asignaciones.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/asignaciones') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/asignaciones') !== false ? 'active' : '' ?>">
                    <i class="bi bi-clipboard-check me-2"></i> Asignaciones
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.constancias.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/constancias') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/constancias') !== false ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text me-2"></i> Constancias
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.permisos.asignar', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/permisos/asignar') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/permisos') !== false ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock me-2"></i> Permisos
                </a>
            <?php endif; ?>
            
            <?php if (in_array('admin.configuracion.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/admin/configuracion') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/configuracion') !== false ? 'active' : '' ?>">
                    <i class="bi bi-gear me-2"></i> Configuración
                </a>
            <?php endif; ?>
            
            <?php if (in_array('reportes.ver', $permisos) || $tipoUsuario === 'admin'): ?>
                <a href="<?= url('/reportes') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/reportes') !== false ? 'active' : '' ?>">
                    <i class="bi bi-graph-up me-2"></i> Reportes
                </a>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($tipoUsuario === 'docente' || in_array('docente.dashboard', $permisos)): ?>
            <!-- Menú Docente -->
            <a href="<?= url('/docente') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/docente') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            <?php if (in_array('docente.calificaciones.ver', $permisos) || $tipoUsuario === 'docente'): ?>
                <a href="<?= url('/docente/calificaciones') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/docente/calificaciones') !== false ? 'active' : '' ?>">
                    <i class="bi bi-pencil-square me-2"></i> Calificaciones
                </a>
            <?php endif; ?>
            
            <?php if (in_array('docente.asistencia.ver', $permisos) || $tipoUsuario === 'docente'): ?>
                <a href="<?= url('/docente/asistencia') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/docente/asistencia') !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> Asistencia
                </a>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($tipoUsuario === 'estudiante' || in_array('estudiante.dashboard', $permisos)): ?>
            <!-- Menú Estudiante -->
            <a href="<?= url('/estudiante') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/estudiante') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            <?php if (in_array('estudiante.boletin.ver', $permisos) || $tipoUsuario === 'estudiante'): ?>
                <a href="<?= url('/estudiante/boletin') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/estudiante/boletin') !== false ? 'active' : '' ?>">
                    <i class="bi bi-journal-check me-2"></i> Boletín
                </a>
            <?php endif; ?>
            
            <?php if (in_array('estudiante.asistencia.ver', $permisos) || $tipoUsuario === 'estudiante'): ?>
                <a href="<?= url('/estudiante/asistencia') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/estudiante/asistencia') !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> Asistencia
                </a>
            <?php endif; ?>
            
            <?php if (in_array('estudiante.constancias.solicitar', $permisos) || $tipoUsuario === 'estudiante'): ?>
                <a href="<?= url('/estudiante/constancias/solicitar') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/estudiante/constancias') !== false ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text me-2"></i> Constancias
                </a>
            <?php endif; ?>
            
            <a href="<?= url('/estudiante/perfil') ?>" class="list-group-item <?= strpos($_SERVER['REQUEST_URI'], '/estudiante/perfil') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-circle me-2"></i> Mi Perfil
            </a>
        <?php endif; ?>
        
        <!-- Logout -->
        <a href="<?= url('/logout') ?>" class="list-group-item text-danger mt-3">
            <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
        </a>
    </div>
</div>

<!-- Contenido principal -->
<div id="page-content-wrapper">
    <!-- Topbar -->
    <nav class="navbar navbar-expand-lg topbar border-bottom">
        <div class="container-fluid">
            <button class="btn btn-link sidebar-toggler" id="sidebar-toggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
                    <li class="nav-item me-3">
                        <button class="btn btn-link nav-link" id="theme-toggle" title="Cambiar tema">
                            <i class="bi bi-moon-fill"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                <?= strtoupper(substr($nombreCompleto, 0, 1)) ?>
                            </div>
                            <span><?= e($nombreCompleto) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text text-muted small"><?= e($tipoUsuario) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenido de la página -->
    <div class="container-fluid px-4 py-4">
