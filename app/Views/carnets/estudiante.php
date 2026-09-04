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
                size: letter portrait;
                margin: 0.5in;
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
            .print-container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="font-sans antialiased p-4 sm:p-8 bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-screen flex flex-col items-center justify-start">

    <!-- Top Action Bar (No Print) -->
    <div class="w-full max-w-4xl mb-6 flex items-center justify-between no-print bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-3">
            <button onclick="history.back()" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-all">
                <i class="bi bi-arrow-left"></i>
                <span>Volver</span>
            </button>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Credencial Estudiantil e Informe de Políticas</span>
        </div>
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 flex items-center space-x-2 transition-all">
            <i class="bi bi-printer-fill text-sm"></i>
            <span>Imprimir Documento Completo</span>
        </button>
    </div>

    <!-- MAIN PRINT CONTAINER -->
    <div class="w-full max-w-4xl space-y-6 print-container">

        <!-- ═══ SECCIÓN SUPERIOR: ENUNCIADO Y POLÍTICAS DE USO DEL SISTEMA ═══ -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-700 print:shadow-none print:border-slate-300 print:bg-white print:text-slate-900">
            <!-- Header Documento -->
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4 mb-4 flex items-center justify-between">
                <div>
                    <span class="text-[9pt] font-extrabold uppercase tracking-widest text-blue-600 dark:text-blue-400 print:text-blue-700 block">REPÚBLICA BOLIVARIANA DE VENEZUELA • MPPE</span>
                    <h1 class="text-base sm:text-lg font-black uppercase tracking-tight text-slate-900 dark:text-white print:text-slate-900"><?= $nombreInstitucion ?></h1>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 print:text-slate-600">Sistema de Gestión y Control Educativo - Académico (SGCEA)</p>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 text-[10pt] font-mono font-black rounded-lg border border-blue-200 dark:border-blue-700">
                        <?= $codigoValidacion ?>
                    </span>
                    <p class="text-[8pt] text-slate-400 font-mono mt-1">Año Lectivo: <?= $anoLectivo ?></p>
                </div>
            </div>

            <!-- Enunciado de Políticas de Uso -->
            <div class="space-y-3 text-xs leading-relaxed text-slate-700 dark:text-slate-300 print:text-slate-800">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-shield-check text-blue-600 dark:text-blue-400 print:text-blue-700 text-base"></i>
                    <h2 class="font-extrabold text-xs uppercase tracking-wider text-slate-900 dark:text-white print:text-slate-900">DECLARACIÓN Y NORMATIVA DE USO DEL SISTEMA Y CREDENCIAL INSTITUCIONAL</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[8.5pt] bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 print:bg-slate-50 print:border-slate-200">
                    <div class="space-y-2">
                        <p><strong>1. Identificación Personal e Intransferible:</strong> La presente credencial certifica la condición de estudiante regular. Su uso es estrictamente personal para el ingreso al plantel y validación de actividades académicas.</p>
                        <p><strong>2. Seguridad de Credenciales Digitales:</strong> El usuario y clave asignados en la plataforma SGCEA son confidenciales. Queda prohibida la divulgación o préstamo de credenciales a terceros.</p>
                    </div>
                    <div class="space-y-2">
                        <p><strong>3. Ética e Integridad de Datos:</strong> El acceso al sistema debe reservarse para fines educativos. Toda alteración de datos académicos o suplantación será sancionada según el reglamento disciplinario.</p>
                        <p><strong>4. Portación Obligatoria y Extravío:</strong> Es obligatorio portar la credencial en las instalaciones del plantel. En caso de perdida o retiro, debe notificarse inmediatamente a la Dirección.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ SECCIÓN INFERIOR: CARNET LADO A LADO (CARA A Y CARA B) ═══ -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-700 print:shadow-none print:border-none print:p-0 print:bg-transparent">
            
            <div class="text-center mb-4 no-print">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">CREDENCIAL INSTITUCIONAL OFICIAL (RECORTE Y LAMINADO CR80)</h3>
                <p class="text-[8pt] text-slate-500 dark:text-slate-400">Presentación horizontal de la carátula frontal (Cara A) y posterior (Cara B) para su troquelado.</p>
            </div>

            <!-- LAYOUT LADO A LADO (CARA A EN UN LADO Y CARA B AL OTRO) -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 sm:gap-8 print:flex-row print:justify-center print:items-center print:gap-8">

                <!-- ═══ CARA A (FRENTE DEL CARNET) ═══ -->
                <div class="carnet-wrapper w-[85.6mm] h-[53.98mm] bg-white rounded-2xl border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between p-3 select-none shrink-0">
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
                                CARA A • FRENTE
                            </span>
                            <h2 class="text-[9pt] font-black text-slate-900 leading-tight truncate uppercase" title="<?= $nomComp ?>"><?= $nomComp ?></h2>
                            <p class="text-[7.5pt] font-mono font-bold text-slate-700">C.I: <?= $cedula ?></p>
                            <p class="text-[7pt] font-semibold text-slate-600 truncate">Grado/Sec: <strong class="text-blue-700"><?= $gradoSec ?></strong></p>
                            <p class="text-[6.5pt] font-mono text-slate-500">Año Lectivo: <?= $anoLectivo ?></p>
                        </div>
                    </div>

                    <!-- Footer: Código QR & Barcode -->
                    <div class="border-t border-slate-200 -mx-3 -mb-3 px-3 py-1 bg-slate-50 flex items-center justify-between text-[6.5pt] font-mono text-slate-600">
                        <span class="font-bold text-slate-900"><i class="bi bi-qr-code me-1"></i><?= $codigoValidacion ?></span>
                        <span class="text-[6pt] text-slate-400 font-bold">VÁLIDO CON SELLO</span>
                    </div>
                </div>

                <!-- ═══ CARA B (REVERSO DEL CARNET) ═══ -->
                <div class="carnet-wrapper w-[85.6mm] h-[53.98mm] bg-slate-900 text-white rounded-2xl border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between p-3 select-none shrink-0">
                    <!-- Header Reverso -->
                    <div class="border-b border-slate-700 pb-1 flex items-center justify-between text-[6.5pt] font-mono text-slate-400">
                        <span>DATOS DE CONTACTO Y EMERGENCIA</span>
                        <span class="text-blue-400 font-bold">CARA B • REVERSO</span>
                    </div>

                    <!-- Representante & Informes -->
                    <div class="space-y-1 my-auto text-[7pt]">
                        <div>
                            <span class="text-[6pt] uppercase tracking-wider text-slate-400 font-bold block">Representante Legal</span>
                            <p class="font-bold text-white leading-tight truncate"><?= htmlspecialchars($estudiante['nombre_representante'] ?? 'No registrado') ?></p>
                        </div>
                        <div>
                            <span class="text-[6pt] uppercase tracking-wider text-slate-400 font-bold block">Teléfono de Emergencia</span>
                            <p class="font-mono text-emerald-400 font-bold"><?= htmlspecialchars($estudiante['telefono_representante'] ?? $estudiante['telefono'] ?? 'N/A') ?></p>
                        </div>
                        <p class="text-[5.5pt] text-slate-400 leading-tight italic pt-0.5">
                            * Documento oficial personal e intransferible. Portación obligatoria en el plantel.
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

        </div>

    </div>

</body>
</html>

