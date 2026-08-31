<?php
/**
 * Vista: Carnet Estudiantil Oficial SGCEA (CR80 Standard Print Ready)
 */
$nomComp = htmlspecialchars(($estudiante['nombres'] ?? $estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellidos'] ?? $estudiante['apellido'] ?? ''));
$cedula = htmlspecialchars($estudiante['cedula'] ?? 'N/A');
$gradoSec = htmlspecialchars(($inscripcion['grado_nombre'] ?? $estudiante['grado_nombre'] ?? 'Sin Asignar') . ' - ' . ($inscripcion['seccion_nombre'] ?? $estudiante['seccion_nombre'] ?? 'A'));
$anoLectivo = htmlspecialchars($config['ano_academico_actual'] ?? date('Y'));
$nombreInstitucion = htmlspecialchars($config['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA');
$codigoValidacion = 'SGCEA-EST-' . str_pad((string)$estudiante['id'], 5, '0', STR_PAD_LEFT);
$iniciales = strtoupper(substr($estudiante['nombres'] ?? $estudiante['nombre'] ?? 'E', 0, 1) . substr($estudiante['apellidos'] ?? $estudiante['apellido'] ?? 'S', 0, 1));
?>
<!DOCTYPE html>
<html lang="es" class="bg-slate-100 dark:bg-slate-900 min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet Estudiantil - <?= $nomComp ?></title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            .no-print { display: none !important; }
            body {
                background-color: #ffffff !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .carnet-wrapper {
                box-shadow: none !important;
                border: 2px solid #0f172a !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased p-4 sm:p-8 flex flex-col items-center justify-center min-h-screen">

    <!-- Top Action Bar (No Print) -->
    <div class="w-full max-w-xl mb-6 flex items-center justify-between no-print">
        <button onclick="history.back()" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver</span>
        </button>
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 flex items-center space-x-2 transition-all">
            <i class="bi bi-printer-fill text-sm"></i>
            <span>Imprimir Carnet Estudiantil</span>
        </button>
    </div>

    <!-- CARNET CONTAINER (FRENTE Y REVERSO) -->
    <div class="space-y-8">

        <!-- ═══ FRENTE DEL CARNET ═══ -->
        <div class="carnet-wrapper w-[85.6mm] h-[53.98mm] bg-white rounded-2xl border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between p-3 select-none">
            <!-- Header Institucional -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 -mx-3 -mt-3 px-3 py-1.5 text-white text-center">
                <p class="text-[6.5pt] font-extrabold uppercase tracking-widest leading-none text-blue-200">REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
                <p class="text-[6pt] font-semibold uppercase tracking-wider leading-tight opacity-90">MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</p>
                <h1 class="text-[8.5pt] font-black uppercase tracking-tight mt-0.5"><?= $nombreInstitucion ?></h1>
            </div>

            <!-- Body: Foto y Datos -->
            <div class="flex items-center space-x-3 my-auto">
                <!-- Foto / Avatar -->
                <?php if (!empty($estudiante['foto']) && file_exists(__DIR__ . '/../../public/' . $estudiante['foto'])): ?>
                    <img src="<?= url('/' . $estudiante['foto']) ?>" alt="Foto" class="w-[20mm] h-[24mm] rounded-xl object-cover border-2 border-blue-500/50 shadow-md shrink-0">
                <?php else: ?>
                    <div class="w-[20mm] h-[24mm] rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex flex-col items-center justify-center font-black text-xl border-2 border-blue-400/50 shadow-md shrink-0">
                        <span><?= $iniciales ?></span>
                        <span class="text-[6pt] font-mono font-bold tracking-widest text-blue-200 mt-0.5">EST</span>
                    </div>
                <?php endif; ?>

                <!-- Personal Info -->
                <div class="flex-1 space-y-0.5 overflow-hidden">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[6pt] font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                        ESTUDIANTE REGULAR
                    </span>
                    <h2 class="text-[9pt] font-black text-slate-900 leading-tight truncate uppercase" title="<?= $nomComp ?>"><?= $nomComp ?></h2>
                    <p class="text-[7.5pt] font-mono font-bold text-slate-700">C.I: <?= $cedula ?></p>
                    <p class="text-[7pt] font-semibold text-slate-600">Grado/Sección: <strong class="text-blue-700"><?= $gradoSec ?></strong></p>
                    <p class="text-[6.5pt] font-mono text-slate-500">Año Lectivo: <?= $anoLectivo ?></p>
                </div>
            </div>

            <!-- Footer: Código QR & Barcode -->
            <div class="border-t border-slate-200 -mx-3 -mb-3 px-3 py-1 bg-slate-50 flex items-center justify-between text-[6.5pt] font-mono text-slate-600">
                <span class="font-bold text-slate-900"><i class="bi bi-qr-code me-1"></i><?= $codigoValidacion ?></span>
                <span class="text-[6pt] text-slate-400">VÁLIDO CON SELLO</span>
            </div>
        </div>

        <!-- ═══ REVERSO DEL CARNET ═══ -->
        <div class="carnet-wrapper w-[85.6mm] h-[53.98mm] bg-slate-900 text-white rounded-2xl border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between p-3 select-none">
            <!-- Header Reverso -->
            <div class="border-b border-slate-700 pb-1 flex items-center justify-between text-[6.5pt] font-mono text-slate-400">
                <span>DATOS DE CONTACTO Y EMERGENCIA</span>
                <span class="text-blue-400 font-bold">REVERSO</span>
            </div>

            <!-- Representante & Informes -->
            <div class="space-y-1 my-auto text-[7pt]">
                <div>
                    <span class="text-[6pt] uppercase tracking-wider text-slate-400 font-bold block">Representante Legal</span>
                    <p class="font-bold text-white leading-tight"><?= htmlspecialchars($estudiante['nombre_representante'] ?? 'No registrado') ?></p>
                </div>
                <div>
                    <span class="text-[6pt] uppercase tracking-wider text-slate-400 font-bold block">Teléfono de Emergencia</span>
                    <p class="font-mono text-emerald-400 font-bold"><?= htmlspecialchars($estudiante['telefono_representante'] ?? $estudiante['telefono'] ?? 'N/A') ?></p>
                </div>
                <p class="text-[5.5pt] text-slate-400 leading-tight italic pt-0.5">
                    * Este carnet es personal e transferible. Debe presentarse obligatoriamente para acceder a la institución.
                </p>
            </div>

            <!-- Firmas -->
            <div class="border-t border-slate-700 -mx-3 -mb-3 px-3 py-1.5 bg-slate-950 flex items-end justify-between text-center text-[5.5pt]">
                <div class="w-1/2">
                    <div class="border-t border-slate-400 pt-0.5">
                        <p class="font-bold text-slate-200">Director / Subdirector</p>
                        <p class="text-[5pt] text-slate-400">Firma Autorizada</p>
                    </div>
                </div>
                <div class="w-1/3 text-right font-mono text-[5.5pt] text-blue-400">
                    <p>SGCEA VERIFIED</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

