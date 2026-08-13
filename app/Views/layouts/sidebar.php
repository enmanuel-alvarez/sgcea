<?php
/**
 * Layout - Sidebar (Bootstrap 5 Dashboard Template)
 * Sidebar dinámico según permisos y tipo de usuario
 */
$tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
$permisos_sesion = $_SESSION['usuario_permisos'] ?? [];
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>

            <!-- SIDEBAR -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    
                    <?php if ($tipoUsuario === 'admin'): ?>
                    <!-- ═══ Menú Administrador ═══ -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-1 mb-1 text-muted">
                        <span>Principal</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $currentUri === '/admin' || $currentUri === '/admin/' ? 'active' : '' ?>" href="<?= url('/admin') ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Gestión</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/usuarios') !== false ? 'active' : '' ?>" href="<?= url('/admin/usuarios') ?>">
                                <i class="bi bi-people me-2"></i> Usuarios
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/estudiantes') !== false ? 'active' : '' ?>" href="<?= url('/admin/estudiantes') ?>">
                                <i class="bi bi-person-badge me-2"></i> Estudiantes
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/docentes') !== false ? 'active' : '' ?>" href="<?= url('/admin/docentes') ?>">
                                <i class="bi bi-person-workspace me-2"></i> Docentes
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/materias') !== false ? 'active' : '' ?>" href="<?= url('/admin/materias') ?>">
                                <i class="bi bi-book me-2"></i> Materias
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/secciones') !== false ? 'active' : '' ?>" href="<?= url('/admin/secciones') ?>">
                                <i class="bi bi-door-open me-2"></i> Secciones
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/asignaciones') !== false ? 'active' : '' ?>" href="<?= url('/admin/asignaciones') ?>">
                                <i class="bi bi-diagram-3 me-2"></i> Asignaciones
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/constancias') !== false ? 'active' : '' ?>" href="<?= url('/admin/constancias') ?>">
                                <i class="bi bi-file-earmark-text me-2"></i> Constancias
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Sistema</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/admin/configuracion') !== false || strpos($currentUri, '/configuracion') !== false ? 'active' : '' ?>" href="<?= url('/admin/configuracion') ?>">
                                <i class="bi bi-gear me-2"></i> Configuración
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/reportes') !== false ? 'active' : '' ?>" href="<?= url('/reportes') ?>">
                                <i class="bi bi-bar-chart-line me-2"></i> Reportes
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                    
                    <?php if ($tipoUsuario === 'docente'): ?>
                    <!-- ═══ Menú Docente ═══ -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-1 mb-1 text-muted">
                        <span>Principal</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $currentUri === '/docente' || $currentUri === '/docente/' ? 'active' : '' ?>" href="<?= url('/docente') ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Académico</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <?php if (in_array('docente.calificaciones.ver', $permisos_sesion)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/docente/calificaciones') !== false ? 'active' : '' ?>" href="<?= url('/docente/calificaciones') ?>">
                                <i class="bi bi-journal-check me-2"></i> Calificaciones
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (in_array('docente.asistencia.ver', $permisos_sesion)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/docente/asistencia') !== false ? 'active' : '' ?>" href="<?= url('/docente/asistencia') ?>">
                                <i class="bi bi-calendar-check me-2"></i> Asistencia
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <?php if ($tipoUsuario === 'estudiante'): ?>
                    <!-- ═══ Menú Estudiante ═══ -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-1 mb-1 text-muted">
                        <span>Principal</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $currentUri === '/estudiante' || $currentUri === '/estudiante/' ? 'active' : '' ?>" href="<?= url('/estudiante') ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Académico</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <?php if (in_array('estudiante.boletin.ver', $permisos_sesion)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/estudiante/boletin') !== false ? 'active' : '' ?>" href="<?= url('/estudiante/boletin') ?>">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Boletín
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (in_array('estudiante.asistencia.ver', $permisos_sesion)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/estudiante/asistencia') !== false ? 'active' : '' ?>" href="<?= url('/estudiante/asistencia') ?>">
                                <i class="bi bi-calendar-check me-2"></i> Asistencia
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (in_array('estudiante.constancias.solicitar', $permisos_sesion)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/estudiante/constancias') !== false ? 'active' : '' ?>" href="<?= url('/estudiante/constancias/solicitar') ?>">
                                <i class="bi bi-file-earmark-text me-2"></i> Constancias
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Mi Cuenta</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUri, '/estudiante/perfil') !== false ? 'active' : '' ?>" href="<?= url('/estudiante/perfil') ?>">
                                <i class="bi bi-person-circle me-2"></i> Mi Perfil
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                </div>
            </nav>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
