<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Login') ?> - <?= config('app.nombre_sistema') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/custom.css') ?>" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .login-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-body {
            padding: 2rem;
            background: white;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        
        .logo-container {
            margin-bottom: 1rem;
        }
        
        .logo-container i {
            font-size: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="logo-container">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <h2>SGCEA</h2>
                        <p class="mb-0">Sistema de Gestión Escolar</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if (isset($_SESSION['flash'])): ?>
                            <div class="alert alert-<?= e($_SESSION['flash']['tipo']) ?> alert-dismissible fade show" role="alert">
                                <?= e($_SESSION['flash']['mensaje']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['flash']); ?>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?= url('/login') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="login" class="form-label">
                                    <i class="bi bi-person-circle me-1"></i>
                                    Correo o Cédula
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="login" 
                                       name="login" 
                                       placeholder="correo@ejemplo.com"
                                       required 
                                       autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Iniciar Sesión
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Credenciales demo: admin@sgcea.com / admin123
                            </small>
                        </div>
                    </div>
                </div>
                
                <p class="text-center text-white mt-3">
                    <small>&copy; <?= date('Y') ?> <?= config('app.nombre_sistema') ?></small>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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
