<?php
/**
 * Layout - Header (Tailwind CSS v3 Dashboard con Búsqueda Contextual, Filtro y Auditoría)
 */
$nombreCompleto = ($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '');
$tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
$emailUsuario = $_SESSION['usuario_correo'] ?? $_SESSION['usuario_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'SGCEA') ?> - <?= config('app.nombre_sistema') ?></title>
    
    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0284c7',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/custom.css') ?>" rel="stylesheet">
    <!-- Print CSS -->
    <link href="<?= asset('css/print.css') ?>" rel="stylesheet" media="all">
</head>
<body class="h-full antialiased font-sans flex flex-col bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 transition-colors duration-200">

    <!-- NAVBAR SUPERIOR -->
    <header class="sticky top-0 z-40 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur border-b border-slate-800 text-white shadow-md no-print">
        <div class="px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            
            <!-- Logo & Title -->
            <div class="flex items-center space-x-3 shrink-0">
                <button id="mobileSidebarToggle" type="button" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Abrir Menú">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                <a href="<?= url('/') ?>" class="flex items-center space-x-2 text-xl font-bold tracking-tight text-white hover:text-blue-400 transition-colors">
                    <span class="p-2 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30 text-white flex items-center justify-center w-9 h-9">
                        <i class="bi bi-mortarboard-fill text-lg"></i>
                    </span>
                    <span class="hidden sm:inline">SGCEA</span>
                </a>
            </div>

            <!-- Global Contextual Search & Filter Bar -->
            <div class="flex-1 max-w-lg mx-4 hidden sm:flex items-center space-x-2 relative">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchGlobal" oninput="filtrarModuloContextual(this.value)" placeholder="Buscar en este módulo..." 
                           class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm bg-slate-800/80 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Contextual Filter Trigger Button -->
                <div class="relative">
                    <button type="button" id="btnFilterHeader" onclick="toggleHeaderFilterMenu()" 
                            class="px-3 py-2 bg-slate-800/80 hover:bg-slate-700/90 border border-slate-700/80 rounded-xl text-slate-300 hover:text-white transition-all text-xs font-semibold flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Filtrar registros del módulo actual">
                        <i class="bi bi-funnel-fill text-blue-400"></i>
                        <span class="hidden md:inline">Filtro</span>
                        <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Filter Options Dropdown -->
                    <div id="headerFilterMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl z-50 p-2 space-y-1 animate-fade-in">
                        <span class="block px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-700/60 mb-1">
                            Filtros del Módulo
                        </span>
                        <button type="button" onclick="aplicarFiltroPredefinido('')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 flex items-center justify-between">
                            <span>Ver Todos</span>
                            <i class="bi bi-check2 text-blue-500 opacity-0 filter-check-all"></i>
                        </button>
                        <button type="button" onclick="aplicarFiltroPredefinido('Activo')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 flex items-center justify-between">
                            <span>Solo Activos</span>
                            <i class="bi bi-check2 text-emerald-500 opacity-0 filter-check-activo"></i>
                        </button>
                        <button type="button" onclick="aplicarFiltroPredefinido('Inactivo')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-between">
                            <span>Solo Inactivos</span>
                            <i class="bi bi-check2 text-rose-500 opacity-0 filter-check-inactivo"></i>
                        </button>
                        <button type="button" onclick="aplicarFiltroPredefinido('UPDATE')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 flex items-center justify-between">
                            <span>Modificaciones / Update</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle & User Dropdown -->
            <div class="flex items-center space-x-3 relative shrink-0">
                <!-- Theme Toggle Button -->
                <button id="toggleTheme" type="button" class="p-2 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none transition-colors" title="Cambiar Tema (Oscuro/Claro)">
                    <i class="bi bi-moon-stars text-lg dark:hidden"></i>
                    <i class="bi bi-sun text-lg hidden dark:block text-amber-400"></i>
                </button>

                <!-- USER BADGE DROPDOWN TRIGGER -->
                <div class="relative">
                    <button type="button" id="userMenuBtn" onclick="toggleUserDropdown()"
                            class="flex items-center space-x-3 px-3 py-1.5 bg-slate-800/80 hover:bg-slate-700/90 rounded-xl border border-slate-700/80 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shadow-inner">
                            <?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="text-left text-xs leading-tight hidden sm:block">
                            <p class="font-bold text-slate-100 truncate max-w-[120px]"><?= e($nombreCompleto) ?></p>
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase bg-blue-500/20 text-blue-300 border border-blue-400/20">
                                <?= e($tipoUsuario) ?>
                            </span>
                        </div>
                        <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform duration-200" id="userDropdownChevron"></i>
                    </button>

                    <!-- USER DROPDOWN MENU -->
                    <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/90 shadow-2xl z-50 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700/50 animate-fade-in">
                        
                        <!-- User Header Info -->
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-900/50">
                            <p class="font-bold text-slate-900 dark:text-white text-sm truncate"><?= e($nombreCompleto) ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5"><?= e($emailUsuario) ?></p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                Rol: <?= e(ucfirst($tipoUsuario)) ?>
                            </span>
                        </div>

                        <!-- Menu Options -->
                        <div class="py-2">
                            <?php if ($tipoUsuario === 'admin'): ?>
                                <a href="<?= url('/admin/configuracion') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-gear text-base text-blue-500"></i>
                                    <span>Configuración del Sistema</span>
                                </a>
                                <a href="<?= url('/admin/backup') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-database-gear text-base text-emerald-500"></i>
                                    <span>Copias de Seguridad & Danger Zone</span>
                                </a>
                                <a href="<?= url('/admin/usuarios') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-people text-base text-indigo-500"></i>
                                    <span>Gestión de Usuarios y Roles</span>
                                </a>
                                <a href="<?= url('/admin/auditoria') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-journal-text text-base text-amber-500"></i>
                                    <span>Logs de Auditoría</span>
                                </a>
                            <?php elseif ($tipoUsuario === 'estudiante'): ?>
                                <a href="<?= url('/estudiante/perfil') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/50 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-person-badge text-base text-blue-500"></i>
                                    <span>Perfil</span>
                                </a>
                            <?php elseif ($tipoUsuario === 'docente'): ?>
                                <a href="<?= url('/docente/perfil') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-700/50 hover:text-purple-600 transition-colors">
                                    <i class="bi bi-person-badge text-base text-purple-500"></i>
                                    <span>Perfil</span>
                                </a>
                            <?php endif; ?>

                            <button type="button" onclick="abrirModalCambiarPassword()" class="w-full text-left flex items-center space-x-3 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-slate-700/50 hover:text-amber-600 transition-colors">
                                <i class="bi bi-key-fill text-base text-amber-500"></i>
                                <span>Cambiar Contraseña</span>
                            </button>
                        </div>

                        <!-- Logout Link -->
                        <div class="py-2">
                            <a href="<?= url('/logout') ?>" class="flex items-center space-x-3 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                                <i class="bi bi-box-arrow-right text-base me-1"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <script>
    function toggleUserDropdown() {
        const menu = document.getElementById('userDropdownMenu');
        const chevron = document.getElementById('userDropdownChevron');
        if (menu) {
            menu.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('rotate-180');
        }
    }
    function toggleHeaderFilterMenu() {
        const menu = document.getElementById('headerFilterMenu');
        if (menu) menu.classList.toggle('hidden');
    }

    // Búsqueda y Filtro Contextual en Tiempo Real por Módulo
    function filtrarModuloContextual(query) {
        if (window.jQuery && $.fn.dataTable) {
            const tables = $.fn.dataTable.tables({ api: true });
            if (tables.length > 0) {
                tables.search(query).draw();
                return;
            }
        }
        // Fallback para tablas HTML sin DataTables
        const searchVal = query.toLowerCase();
        document.querySelectorAll('table tbody tr').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchVal) ? '' : 'none';
        });
    }

    function aplicarFiltroPredefinido(filtro) {
        const searchInput = document.getElementById('searchGlobal');
        if (searchInput) searchInput.value = filtro;
        filtrarModuloContextual(filtro);
        toggleHeaderFilterMenu();
    }

    // Cerrar desplegables al hacer clic fuera
    document.addEventListener('click', function(e) {
        const userBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userDropdownMenu');
        if (userBtn && userMenu && !userBtn.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.add('hidden');
            const chevron = document.getElementById('userDropdownChevron');
            if (chevron) chevron.classList.remove('rotate-180');
        }

        const filterBtn = document.getElementById('btnFilterHeader');
        const filterMenu = document.getElementById('headerFilterMenu');
        if (filterBtn && filterMenu && !filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
            filterMenu.classList.add('hidden');
        }
    });

    function abrirModalCambiarPassword() {
        const modal = document.getElementById('modalCambiarPassword');
        const menu = document.getElementById('userDropdownMenu');
        if (menu) menu.classList.add('hidden');
        if (modal) modal.classList.remove('hidden');
    }

    function cerrarModalCambiarPassword() {
        const modal = document.getElementById('modalCambiarPassword');
        if (modal) modal.classList.add('hidden');
    }
    </script>

    <!-- MODAL: CAMBIAR CONTRASEÑA -->
    <div id="modalCambiarPassword" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-key-fill text-amber-400 text-lg"></i>
                    <h3 class="font-bold text-base">Cambiar Contraseña</h3>
                </div>
                <button type="button" onclick="cerrarModalCambiarPassword()" class="text-slate-400 hover:text-white transition-colors">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>

            <form method="POST" action="<?= url('/cambiar-password') ?>" class="p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Contraseña Actual</label>
                    <input type="password" name="clave_actual" required placeholder="••••••••"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Nueva Contraseña</label>
                    <input type="password" name="clave_nueva" required minlength="6" placeholder="Mínimo 6 caracteres"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-300 mb-1">Confirmar Nueva Contraseña</label>
                    <input type="password" name="clave_confirmar" required minlength="6" placeholder="Repita la nueva contraseña"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="cerrarModalCambiarPassword()" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold rounded-xl text-xs shadow-md shadow-amber-500/20 transition-all flex items-center space-x-1.5">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Actualizar Clave</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">

