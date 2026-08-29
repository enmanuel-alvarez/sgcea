<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-full font-sans flex flex-col items-center justify-center p-4 sm:p-6">
    <div class="max-w-2xl w-full text-center space-y-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1">
            <i class="bi bi-compass-fill text-4xl"></i>
        </div>
        <h1 class="text-6xl font-black text-white tracking-tight">404</h1>
        <h2 class="text-xl font-bold text-slate-200">Página No Encontrada</h2>
        <p class="text-sm text-slate-400 max-w-md mx-auto">La ruta o el recurso solicitado no existe o ha sido modificado.</p>
        
        <div>
            <a href="<?= url('/') ?>" class="inline-flex items-center space-x-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5">
                <i class="bi bi-house-door-fill"></i>
                <span>Volver al Inicio</span>
            </a>
        </div>

        <?php if (defined('APP_DEBUG') && APP_DEBUG && !empty($debugData)): ?>
        <!-- PANEL DE DIAGNÓSTICO DE DEPURACIÓN (DEBUG MODE) -->
        <div class="mt-8 text-left bg-slate-950/90 rounded-2xl border border-slate-800 p-5 space-y-4 font-mono text-xs text-slate-300">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="font-bold text-amber-400 flex items-center space-x-2">
                    <i class="bi bi-bug-fill"></i>
                    <span>Diagnóstico de Depuración 404</span>
                </span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-400">APP_DEBUG = ON</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                <div>
                    <span class="block text-slate-500 text-[10px] font-bold uppercase">URL Solicitada</span>
                    <span class="text-slate-100 font-semibold break-all"><?= htmlspecialchars($debugData['requestedUri'] ?? '') ?></span>
                </div>
                <div>
                    <span class="block text-slate-500 text-[10px] font-bold uppercase">Ruta Evaluada por Router</span>
                    <span class="text-amber-300 font-bold break-all"><?= htmlspecialchars($debugData['evaluatedUri'] ?? '') ?></span>
                </div>
                <div>
                    <span class="block text-slate-500 text-[10px] font-bold uppercase">Método HTTP</span>
                    <span class="text-emerald-400 font-bold"><?= htmlspecialchars($debugData['method'] ?? 'GET') ?></span>
                </div>
                <div>
                    <span class="block text-slate-500 text-[10px] font-bold uppercase">BASE_PATH Detectado</span>
                    <span class="text-blue-400 font-semibold"><?= htmlspecialchars($debugData['basePath'] ?? 'Vacio') ?></span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-800/80">
                <span class="block text-slate-500 text-[10px] font-bold uppercase mb-2">Rutas <?= htmlspecialchars($debugData['method'] ?? 'GET') ?> Registradas (<?= count($debugData['registeredRoutes'] ?? []) ?>):</span>
                <div class="max-h-48 overflow-y-auto bg-slate-900/90 rounded-xl p-3 space-y-1 text-[10px]">
                    <?php foreach ($debugData['registeredRoutes'] ?? [] as $pattern): ?>
                        <div class="hover:text-blue-400 transition-colors">
                            <span class="text-slate-500 me-2">&rarr;</span><?= htmlspecialchars($pattern) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

