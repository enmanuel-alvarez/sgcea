<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($solicitud['tipo'] ?? 'Constancia Oficial') ?> - SGCEA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: letter portrait;
            margin: 12mm 15mm;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            overflow-x: hidden;
            background-color: #f8fafc;
        }
        
        /* Estilos estrictos para impresión formal limpia en 1 hoja */
        @media print {
            .no-print, header, nav, aside, footer { 
                display: none !important; 
            }
            html, body { 
                background: #ffffff !important; 
                color: #000000 !important; 
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                box-shadow: none !important;
                -webkit-print-color-adjust: exact;
            }
            .print-container { 
                box-shadow: none !important; 
                -webkit-box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important; 
                margin: 0 !important; 
                width: 100% !important;
                max-width: 100% !important;
                background: transparent !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            *, *::before, *::after {
                box-shadow: none !important;
                -webkit-box-shadow: none !important;
                text-shadow: none !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body class="text-slate-900 min-h-screen p-4 sm:p-8 flex justify-center items-start" onload="window.print()">

    <div class="max-w-4xl w-full bg-white rounded-2xl border border-slate-200/80 shadow-xl p-8 sm:p-14 print-container relative">
        
        <!-- Barra de Acciones (No Imprimible) -->
        <div class="no-print mb-6 pb-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-file-earmark-check text-blue-600"></i>
                Vista Previa de Documento Oficial
            </span>
            <div class="flex items-center space-x-3">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md transition-all flex items-center space-x-1.5">
                    <i class="bi bi-printer"></i>
                    <span>Imprimir Constancia</span>
                </button>
                <button onclick="history.length > 1 ? history.back() : window.location.href = '<?= url('/') ?>'" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all flex items-center space-x-1.5">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver</span>
                </button>
            </div>
        </div>

        <!-- MEMBRETE OFICIAL DE LA INSTITUCIÓN -->
        <div class="border-b-2 border-slate-900 pb-4 mb-6 text-center space-y-1">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-600">República Bolivariana de Venezuela</p>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-600">Ministerio del Poder Popular para la Educación</p>
            <h1 class="text-2xl sm:text-3xl font-black uppercase text-slate-900 tracking-wide my-1.5">
                <?= htmlspecialchars($institucion['nombre'] ?? $configuraciones['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA') ?>
            </h1>
            
            <div class="flex flex-wrap items-center justify-center gap-x-4 text-xs sm:text-sm font-mono text-slate-700">
                <?php if (!empty($institucion['codigo_dependencia'])): ?>
                    <span><strong>Código / RIF:</strong> <?= htmlspecialchars($institucion['codigo_dependencia']) ?></span>
                <?php endif; ?>
                <?php if (!empty($institucion['telefono'])): ?>
                    <span><strong>Teléfono:</strong> <?= htmlspecialchars($institucion['telefono']) ?></span>
                <?php endif; ?>
                <?php if (!empty($institucion['email'])): ?>
                    <span><strong>Email:</strong> <?= htmlspecialchars($institucion['email']) ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($institucion['direccion'])): ?>
                <p class="text-xs sm:text-sm text-slate-700 font-sans mt-0.5">
                    <strong>Ubicación:</strong> <?= htmlspecialchars($institucion['direccion']) ?>
                </p>
            <?php endif; ?>

            <p class="text-xs sm:text-sm font-bold text-slate-900 font-mono pt-1">
                Año Lectivo / Académico: <?= htmlspecialchars($configuraciones['ano_academico_actual'] ?? date('Y')) ?>
            </p>
        </div>

        <!-- TÍTULO DE LA CONSTANCIA -->
        <div class="text-center my-6">
            <h2 class="text-lg sm:text-xl font-black uppercase tracking-widest text-slate-900 border-b-2 border-slate-900 inline-block pb-1 px-6">
                CONSTANCIA DE <?= htmlspecialchars(strtoupper($solicitud['tipo'] ?? 'ESTUDIO')) ?>
            </h2>
            <p class="text-xs font-mono text-slate-500 mt-1">
                N° de Control: CST-<?= htmlspecialchars($configuraciones['ano_academico_actual'] ?? date('Y')) ?>-<?= (int)($solicitud['id'] ?? 1) ?>
            </p>
        </div>

        <?php
            $nombreEstudiante = trim(($estudiante['nombre'] ?? $estudiante['nombres'] ?? '') . ' ' . ($estudiante['apellido'] ?? $estudiante['apellidos'] ?? ''));
            $cedulaEstudiante = $estudiante['cedula'] ?? 'N/A';
            $gradoNombre = $inscripcion['grado_nombre'] ?? $solicitud['grado'] ?? 'Grado no especificado';
            $seccionNombre = $inscripcion['seccion_nombre'] ?? $solicitud['seccion'] ?? 'A';
        ?>

        <!-- CUERPO Y REDACCIÓN PARRAFADA CONTINUA Y FLUIDA -->
        <div class="py-6 space-y-6 text-justify text-base sm:text-lg leading-relaxed text-slate-900">
            <p>
                Quien suscribe, la Dirección de la <strong><?= htmlspecialchars($institucion['nombre'] ?? $configuraciones['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA') ?></strong>, por medio de la presente hace constar que el(la) estudiante <strong><?= htmlspecialchars($nombreEstudiante) ?></strong>, titular de la Cédula de Identidad N° <strong><?= htmlspecialchars($cedulaEstudiante) ?></strong><?php if (!empty($estudiante['fecha_nacimiento'])): ?>, nacido(a) el <strong><?= date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) ?></strong><?php endif; ?>, se encuentra debidamente inscrito(a) y cursa estudios como estudiante regular en el <strong><?= htmlspecialchars($gradoNombre) ?></strong>, Sección <strong>"<?= htmlspecialchars($seccionNombre) ?>"</strong> durante el año escolar <strong><?= htmlspecialchars($configuraciones['ano_academico_actual'] ?? date('Y')) ?></strong>.
            </p>

            <?php if (!empty($estudiante['nombre_representante'])): ?>
                <p>
                    Asimismo, se hace constar que su representante legal registrado es el(la) ciudadano(a) <strong><?= htmlspecialchars($estudiante['nombre_representante']) ?></strong><?= !empty($estudiante['telefono_representante']) ? ', contacto telefónico: <strong>' . htmlspecialchars($estudiante['telefono_representante']) . '</strong>' : '' ?>.
                </p>
            <?php endif; ?>

            <?php if (!empty($solicitud['motivo'])): ?>
                <p>
                    Constancia expedida bajo el siguiente motivo declarado: <em>"<?= htmlspecialchars($solicitud['motivo']) ?>"</em>.
                </p>
            <?php endif; ?>

            <p>
                Se expide la presente constancia a solicitud de la parte interesada en la sede de la institución, a los <?= date('d') ?> días del mes de <?= date('m') ?> del año <?= date('Y') ?>.
            </p>
        </div>

        <!-- FIRMA Y SELLO OFICIAL DE DIRECCIÓN -->
        <div class="pt-16 sm:pt-20 text-center space-y-1">
            <div class="w-64 mx-auto border-t-2 border-slate-900 pt-2"></div>
            <p class="font-extrabold text-slate-900 text-sm sm:text-base uppercase">
                <?= htmlspecialchars($institucion['director_nombre'] ?? 'Prof. Director General') ?>
            </p>
            <?php if (!empty($institucion['director_cedula'])): ?>
                <p class="text-xs font-mono text-slate-600">C.I. N° <?= htmlspecialchars($institucion['director_cedula']) ?></p>
            <?php endif; ?>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Director(a) / Autoridad Institucional</p>
            <p class="text-xs text-slate-500 font-semibold"><?= htmlspecialchars($institucion['nombre'] ?? $configuraciones['nombre_institucion'] ?? '') ?></p>
        </div>

        <!-- PIE DE PÁGINA DIGITAL -->
        <div class="mt-12 pt-3 border-t border-slate-200 text-center flex items-center justify-between text-xs text-slate-400 font-mono">
            <span>SGCEA - Emisión Digital Oficial</span>
            <span>Fecha/Hora Impr.: <?= date('d/m/Y H:i:s') ?></span>
        </div>
    </div>

</body>
</html>

