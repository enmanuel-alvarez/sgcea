<?php
/**
 * Layout - Footer (Tailwind CSS v3 Dashboard)
 */
?>
</main> <!-- /main -->
</div> <!-- /flex-1 flex -->

<!-- TOAST NOTIFICATIONS (TAILWIND ALERTS) -->
<?php if (isset($_SESSION['flash'])): 
    $flashType = $_SESSION['flash']['type'] ?? 'info';
    $flashMsg = $_SESSION['flash']['message'] ?? '';
    $bgColor = 'bg-blue-600 text-white';
    $icon = 'bi-info-circle-fill';
    if ($flashType === 'success') { $bgColor = 'bg-emerald-600 text-white'; $icon = 'bi-check-circle-fill'; }
    if ($flashType === 'danger' || $flashType === 'error') { $bgColor = 'bg-rose-600 text-white'; $icon = 'bi-exclamation-triangle-fill'; }
    if ($flashType === 'warning') { $bgColor = 'bg-amber-500 text-white'; $icon = 'bi-exclamation-circle-fill'; }
?>
<div id="toast-container" class="fixed bottom-5 right-5 z-50 animate-fade-in max-w-sm">
    <div class="flex items-center space-x-3 px-4 py-3 rounded-2xl shadow-xl border border-white/10 <?= $bgColor ?>">
        <i class="bi <?= $icon ?> text-xl shrink-0"></i>
        <div class="flex-1 text-sm font-medium leading-snug"><?= e($flashMsg) ?></div>
        <button type="button" onclick="document.getElementById('toast-container').remove()" class="p-1 rounded-lg hover:bg-white/20 text-white/80 hover:text-white transition-colors">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
</div>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- SCRIPTS DE COMPATIBILIDAD -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.php" error-handler="fallback"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    // 1. Dark Mode Toggle
    const themeBtn = document.getElementById('toggleTheme');
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    }
    
    // Check localstorage or system preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.contains('dark');
            applyTheme(isDark ? 'light' : 'dark');
        });
    }

    // 2. Mobile Sidebar Toggle
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');
    const backdrop = document.getElementById('mobileSidebarBackdrop');

    function toggleMobileMenu() {
        if (sidebar && backdrop) {
            const isHidden = sidebar.classList.contains('-translate-x-full');
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    }

    if (mobileToggle) mobileToggle.addEventListener('click', toggleMobileMenu);
    if (backdrop) backdrop.addEventListener('click', toggleMobileMenu);

    // 3. Auto dismiss toasts after 5s
    setTimeout(function() {
        const toast = document.getElementById('toast-container');
        if (toast) toast.remove();
    }, 5000);
</script>

</body>
</html>

