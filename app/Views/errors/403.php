<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="h-full font-sans flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-rose-500/10 text-rose-500 border border-rose-500/20 mb-2">
            <i class="bi bi-shield-x text-5xl"></i>
        </div>
        <h1 class="text-6xl font-extrabold text-white tracking-tight">403</h1>
        <h2 class="text-xl font-bold text-slate-200">Acceso No Autorizado</h2>
        <p class="text-sm text-slate-400">No posees los permisos necesarios en el sistema para acceder a este módulo o recurso.</p>
        <div>
            <a href="<?= url('/') ?>" class="inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg transition-all">
                <i class="bi bi-house-door-fill"></i>
                <span>Volver al Inicio</span>
            </a>
        </div>
    </div>
</body>
</html>
