<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tipo_constancia ?? 'Constancia Oficial') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-card { border: none !important; shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-6 sm:p-12 flex justify-center items-start" onload="window.print()">

    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl p-8 sm:p-14 print-card relative">
        <!-- Action bar -->
        <div class="no-print mb-8 pb-4 border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-500">Vista previa de impresión oficial</span>
            <div class="flex items-center space-x-3">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md transition-all flex items-center space-x-1.5">
                    <i class="bi bi-printer"></i>
                    <span>Imprimir Documento</span>
                </button>
                <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all">
                    Cerrar
                </button>
            </div>
        </div>

        <!-- Document Header -->
        <div class="text-center pb-8 border-b-2 border-slate-900 space-y-1">
            <h2 class="text-xl font-black uppercase tracking-wider text-slate-900"><?= htmlspecialchars($config['nombre_institucion'] ?? 'INSTITUCIÓN EDUCATIVA') ?></h2>
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Sistema de Gestión y Control Escolar-Académico</p>
            <p class="text-xs font-mono text-slate-600">Año Lectivo: <?= htmlspecialchars($config['ano_lectivo'] ?? date('Y')) ?></p>
        </div>

        <!-- Body -->
        <div class="py-10 space-y-6 text-justify text-base leading-relaxed text-slate-800">
            <h1 class="text-xl font-extrabold text-center uppercase tracking-wide text-slate-900 mb-8 border-b border-slate-100 pb-2">
                <?= htmlspecialchars($tipo_constancia ?? 'CONSTANCIA ESTUDIANTIL') ?>
            </h1>

            <p>Quien suscribe, la Dirección de la institución académica <strong><?= htmlspecialchars($config['nombre_institucion'] ?? 'Institución Educativa') ?></strong>, por medio de la presente hace constar que:</p>

            <div class="my-6 p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1 text-center">
                <p class="text-lg font-black text-slate-900 uppercase tracking-wide"><?= htmlspecialchars(($estudiante['nombres'] ?? '') . ' ' . ($estudiante['apellidos'] ?? '')) ?></p>
                <p class="font-mono text-xs text-slate-600 font-bold">Titular de la Cédula de Identidad: N° <?= htmlspecialchars($estudiante['cedula'] ?? '') ?></p>
            </div>

            <p>
                Es estudiante regular de este plantel educativo, encontrándose debidamente inscrito(a) en el <strong><?= htmlspecialchars($grado ?? 'Grado Académico') ?></strong>, Sección <strong><?= htmlspecialchars($seccion ?? 'Única') ?></strong> correspondiente al período escolar en vigencia.
            </p>

            <?php if (!empty($motivo)): ?>
                <p class="italic text-slate-600 text-sm bg-slate-50 p-3 rounded-lg border border-slate-100">
                    Motivo declarado: "<?= htmlspecialchars($motivo) ?>"
                </p>
            <?php endif; ?>

            <p>Se expide la presente constancia a solicitud de la parte interesada, en la ciudad institucional a los <?= date('d') ?> días del mes de <?= date('m') ?> de <?= date('Y') ?>.</p>
        </div>

        <!-- Signature -->
        <div class="pt-16 text-center space-y-2">
            <div class="w-64 mx-auto border-t-2 border-slate-900 pt-2"></div>
            <p class="font-bold text-slate-900 text-sm">Dirección / Secretaría Académica</p>
            <p class="text-xs text-slate-500"><?= htmlspecialchars($config['nombre_institucion'] ?? '') ?></p>
        </div>

        <!-- Document Footer -->
        <div class="mt-16 pt-4 border-t border-slate-100 text-center flex items-center justify-between text-[10px] text-slate-400 font-mono">
            <span>SGCEA - Emisión Digital</span>
            <span>Fecha de Emisión: <?= date('d/m/Y H:i:s') ?></span>
        </div>
    </div>

</body>
</html>
