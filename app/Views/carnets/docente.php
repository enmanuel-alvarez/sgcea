<?php
/**
 * Vista: Carnet Docente Oficial SGCEA (CR80 Standard Print Ready)
 */
$nomComp = htmlspecialchars(($docente['nombres'] ?? $docente['nombre'] ?? '') . ' ' . ($docente['apellidos'] ?? $docente['apellido'] ?? ''));
$cedula = htmlspecialchars($docente['cedula'] ?? 'N/A');
$especialidad = htmlspecialchars($docente['especialidad'] ?? 'Docente de Aula');
$telefono = htmlspecialchars($docente['telefono'] ?? 'N/A');
$correo = htmlspecialchars($docente['correo'] ?? $docente['email'] ?? 'N/A');
$nombreInstitucion = htmlspecialchars($config['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA');
$codigoValidacion = 'SGCEA-DOC-' . str_pad((string)$docente['id'], 5, '0', STR_PAD_LEFT);
$iniciales = strtoupper(substr($docente['nombres'] ?? $docente['nombre'] ?? 'D', 0, 1) . substr($docente['apellidos'] ?? $docente['apellido'] ?? 'O', 0, 1));
?>
<!DOCTYPE html>
<html lang="es" class="bg-slate-100 dark:bg-slate-900 min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet Docente - <?= $nomComp ?></title>
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
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Credencial Docente e Informe de Políticas</span>
        </div>
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-500/20 flex items-center space-x-2 transition-all">
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
                    <span class="text-[9pt] font-extrabold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 print:text-indigo-700 block">REPÚBLICA BOLIVARIANA DE VENEZUELA • MPPE</span>
                    <h1 class="text-base sm:text-lg font-black uppercase tracking-tight text-slate-900 dark:text-white print:text-slate-900"><?= $nombreInstitucion ?></h1>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 print:text-slate-600">Sistema de Gestión y Control Educativo - Académico (SGCEA)</p>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300 text-[10pt] font-mono font-black rounded-lg border border-purple-200 dark:border-purple-700">
                        <?= $codigoValidacion ?>
                    </span>
                    <p class="text-[8pt] text-slate-400 font-mono mt-1">Identificador Docente</p>
                </div>
            </div>

            <!-- Enunciado de Políticas de Uso -->
            <div class="space-y-3 text-xs leading-relaxed text-slate-700 dark:text-slate-300 print:text-slate-800">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-shield-lock-fill text-indigo-600 dark:text-indigo-400 print:text-indigo-700 text-base"></i>
                    <h2 class="font-extrabold text-xs uppercase tracking-wider text-slate-900 dark:text-white print:text-slate-900">DECLARACIÓN Y NORMATIVA DE USO DEL SISTEMA Y CREDENCIAL DOCENTE</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[8.5pt] bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 print:bg-slate-50 print:border-slate-200">
                    <div class="space-y-2">
                        <p><strong>1. Credencial de Identificación Académica:</strong> Acredita al titular como docente activo en la institución. Otorga responsabilidad sobre registros de evaluación y asistencia.</p>
                        <p><strong>2. Resguardo de Credenciales y 2FA:</strong> El acceso a SGCEA mediante usuario, contraseña y Autenticación de Doble Factor (2FA) es estrictamente personal e intransferible.</p>
                    </div>
                    <div class="space-y-2">
                        <p><strong>3. Responsabilidad Pedagógica:</strong> Las calificaciones registradas poseen carácter de declaración jurada institucional. Las modificaciones deben estar debidamente justificadas.</p>
                        <p><strong>4. Confidencialidad y Portación:</strong> Es obligatorio portar la credencial en el plantel y resguardar la privacidad de los expedientes estudiantiles (LOPNNA).</p>
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
                    <div class="bg-gradient-to-r from-purple-800 to-indigo-900 -mx-3 -mt-3 px-3 py-1.5 text-white text-center">
                        <p class="text-[6.5pt] font-extrabold uppercase tracking-widest leading-none text-purple-200">REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
                        <p class="text-[6pt] font-semibold uppercase tracking-wider leading-tight opacity-90">MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</p>
                        <h1 class="text-[8.5pt] font-black uppercase tracking-tight mt-0.5"><?= $nombreInstitucion ?></h1>
                    </div>

                    <!-- Body: Foto y Datos -->
                    <div class="flex items-center space-x-3 my-auto">
                        <!-- Foto / Avatar -->
                        <?php if (!empty($docente['foto']) && file_exists(__DIR__ . '/../../public/' . $docente['foto'])): ?>
                            <img src="<?= url('/' . $docente['foto']) ?>" alt="Foto" class="w-[20mm] h-[24mm] rounded-xl object-cover border-2 border-purple-500/50 shadow-md shrink-0">
                        <?php else: ?>
                            <div class="w-[20mm] h-[24mm] rounded-xl bg-gradient-to-br from-purple-700 to-indigo-800 text-white flex flex-col items-center justify-center font-black text-xl border-2 border-purple-400/50 shadow-md shrink-0">
                                <span><?= $iniciales ?></span>
                                <span class="text-[6pt] font-mono font-bold tracking-widest text-purple-200 mt-0.5">DOC</span>
                            </div>
                        <?php endif; ?>

                        <!-- Personal Info -->
                        <div class="flex-1 space-y-0.5 overflow-hidden">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[6pt] font-black uppercase tracking-wider bg-purple-100 text-purple-800">
                                CARA A • FRENTE
                            </span>
                            <h2 class="text-[9pt] font-black text-slate-900 leading-tight truncate uppercase" title="<?= $nomComp ?>"><?= $nomComp ?></h2>
                            <p class="text-[7.5pt] font-mono font-bold text-slate-700">C.I: <?= $cedula ?></p>
                            <p class="text-[7pt] font-semibold text-slate-600 truncate">Especialidad: <strong class="text-purple-800"><?= $especialidad ?></strong></p>
                            <p class="text-[6.5pt] font-mono text-slate-500 truncate"><?= $correo ?></p>
                        </div>
                    </div>

                    <!-- Footer: Código QR & Barcode -->
                    <div class="border-t border-slate-200 -mx-3 -mb-3 px-3 py-1 bg-slate-50 flex items-center justify-between text-[6.5pt] font-mono text-slate-600">
                        <span class="font-bold text-slate-900"><i class="bi bi-qr-code me-1"></i><?= $codigoValidacion ?></span>
                        <span class="text-[6pt] text-purple-700 font-bold">DOCENTE ACTIVO</span>
                    </div>
                </div>

                <!-- ═══ CARA B (REVERSO DEL CARNET) ═══ -->
                <div class="carnet-wrapper w-[85.6mm] h-[53.98mm] bg-slate-900 text-white rounded-2xl border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between p-3 select-none shrink-0">
                    <!-- Header Reverso -->
                    <div class="border-b border-slate-700 pb-1 flex items-center justify-between text-[6.5pt] font-mono text-slate-400">
                        <span>CREDENCIAL ACADÉMICA OFICIAL</span>
                        <span class="text-purple-400 font-bold">CARA B • REVERSO</span>
                    </div>

                    <!-- Datos Adicionales -->
                    <div class="space-y-1 my-auto text-[7pt]">
                        <div>
                            <span class="text-[6pt] uppercase tracking-wider text-slate-400 font-bold block">Teléfono de Contacto</span>
                            <p class="font-mono text-emerald-400 font-bold"><?= $telefono ?></p>
                        </div>
                        <p class="text-[5.5pt] text-slate-400 leading-tight italic pt-0.5">
                            * Credencial de identificación institucional oficial del personal docente. Autoriza el ejercicio académico en el plantel.
                        </p>
                    </div>

                    <!-- Firmas -->
                    <div class="border-t border-slate-700 -mx-3 -mb-3 px-3 py-1.5 bg-slate-950 flex items-end justify-between text-center text-[5.5pt]">
                        <div class="w-1/2">
                            <div class="border-t border-slate-400 pt-0.5">
                                <p class="font-bold text-slate-200">Director / Dirección General</p>
                                <p class="text-[5pt] text-slate-400">Firma Autorizada</p>
                            </div>
                        </div>
                        <div class="w-1/3 text-right font-mono text-[5.5pt] text-purple-400">
                            <p>SGCEA DOCENTE</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>

