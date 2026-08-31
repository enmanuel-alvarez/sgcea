<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Iniciar Sesión') ?> - <?= config('app.nombre_sistema') ?></title>
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-100 via-blue-50/50 to-indigo-100/60 flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-xl shadow-blue-500/25 mb-4 border border-blue-400/30">
                <i class="bi bi-mortarboard-fill text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">SGCEA</h1>
            <p class="text-slate-500 text-sm mt-1">Sistema de Gestión y Control Escolar-Académico</p>
        </div>

        <!-- Login Card (Light Theme) -->
        <div class="bg-white/90 backdrop-blur-xl border border-slate-200/80 rounded-3xl shadow-xl shadow-slate-200/60 p-6 sm:p-8">
            
            <?php if (isset($_SESSION['flash'])): 
                $fType = $_SESSION['flash']['tipo'] ?? 'info';
                $fBg = $fType === 'danger' || $fType === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700';
            ?>
                <div class="mb-6 p-4 rounded-2xl border <?= $fBg ?> text-xs font-semibold flex items-center space-x-2">
                    <i class="bi bi-info-circle-fill text-lg shrink-0"></i>
                    <span><?= e($_SESSION['flash']['mensaje']) ?></span>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="POST" action="<?= url('/login') ?>" class="space-y-5">
                <?= csrf_field() ?>
                
                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i class="bi bi-person-circle me-1 text-blue-600"></i> Correo o Cédula
                    </label>
                    <input type="text" id="login" name="login" required autofocus
                           placeholder="Ingrese su correo o cédula"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all text-sm font-medium">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        <i class="bi bi-lock-fill me-1 text-blue-600"></i> Contraseña
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-4 pr-12 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all text-sm font-medium">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="bi bi-eye text-lg" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/25 transition-all transform active:scale-[0.99] flex items-center justify-center space-x-2">
                    <i class="bi bi-box-arrow-in-right text-lg"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; <?= date('Y') ?> <?= config('app.nombre_sistema') ?>. Todos los derechos reservados.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>


