<?php
/**
 * Layout para impresión de documentos (Tailwind CSS v3)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Documento SGCEA') ?></title>
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-start">
    
    <!-- Print Container Sheet -->
    <div class="w-full max-w-4xl bg-white p-8 sm:p-12 rounded-2xl shadow-lg print:shadow-none print:p-0 border border-slate-200 print:border-none">
        <?= $contenido ?? '' ?>
    </div>
    
    <?php if (isset($autoPrint) && $autoPrint): ?>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>
    <?php endif; ?>
</body>
</html>
