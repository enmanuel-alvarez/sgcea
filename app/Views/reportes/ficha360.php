<?php
/**
 * Vista: Ficha 360° Integral del Estudiante (Tailwind CSS v3 Printable)
 */
$est = $ficha['estudiante'] ?? [];
$calificaciones = $ficha['calificaciones'] ?? [];
$asistencias = $ficha['asistencias'] ?? [];
$constancias = $ficha['constancias'] ?? [];

$nombreCompleto = htmlspecialchars(($est['nombre'] ?? '') . ' ' . ($est['apellido'] ?? ''));
$cedula = htmlspecialchars($est['cedula'] ?? '');
$gradoSeccion = htmlspecialchars(($est['grado_nombre'] ?? 'N/A') . ' - ' . ($est['seccion_nombre'] ?? 'N/A'));
?>
<!DOCTYPE html>
<html lang="es" class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha 360° - <?= $nombreCompleto ?></title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            .no-print { display: none !important; }
            body {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-card {
                background: #ffffff !important;
                color: #0f172a !important;
                border: 2px solid #cbd5e1 !important;
                border-radius: 1.5rem !important;
                box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15), 0 10px 15px -5px rgba(0, 0, 0, 0.08) !important;
                padding: 2rem !important;
                margin: 0 auto !important;
            }
            th, td {
                color: #1e293b !important;
            }
            .bg-slate-50, .dark\:bg-slate-900\/50 {
                background-color: #f8fafc !important;
                border-color: #e2e8f0 !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased p-4 sm:p-8">

    <!-- Top Action Bar (No Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="<?= url('/reportes?tipo=cuadro_honor') ?>" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-all">
            <i class="bi bi-arrow-left"></i>
            <span>Volver a Reportes</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 flex items-center space-x-2 transition-all">
            <i class="bi bi-printer-fill"></i>
            <span>Imprimir Ficha 360°</span>
        </button>
    </div>

    <!-- MAIN FICHA CARD -->
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl p-8 space-y-8 print-card">
        
        <!-- ENCABEZADO / MEMBRETE OFICIAL DE LA INSTITUCIÓN -->
        <div class="membrete-oficial border-b-2 border-slate-900 dark:border-slate-100 pb-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 flex items-center justify-center text-3xl font-extrabold shadow-md shrink-0">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</p>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight mt-0.5">
                        <?= htmlspecialchars($config['nombre_institucion'] ?? 'UNIDAD EDUCATIVA SGCEA') ?>
                    </h2>
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Expediente Estudiantil Ficha 360° • Año Lectivo <?= htmlspecialchars($config['ano_academico_actual'] ?? date('Y')) ?>
                    </p>
                </div>
            </div>
            <div class="text-left sm:text-right text-[11px] font-mono text-slate-600 dark:text-slate-300 shrink-0">
                <p class="font-bold text-slate-900 dark:text-white uppercase">Ficha 360° Integral</p>
                <p><i class="bi bi-clock me-1"></i>Emisión: <?= date('d/m/Y h:i A') ?></p>
                <p class="text-blue-600 dark:text-blue-400 font-bold">EXPEDIENTE N° #<?= $est['id'] ?></p>
            </div>
        </div>

        <!-- Header Banner Student -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-6 gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg">
                    <?= strtoupper(substr($est['nombre'] ?? 'E', 0, 1) . substr($est['apellido'] ?? 'S', 0, 1)) ?>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= $nombreCompleto ?></h1>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">C.I: <?= $cedula ?> | Grado: <?= $gradoSeccion ?></p>
                </div>
            </div>
            <div class="text-left sm:text-right text-xs">
                <span class="inline-block px-3 py-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 rounded-full text-xs font-bold">
                    Estudiante Activo
                </span>
            </div>
        </div>

        <!-- Personal & Guardian Data -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/50">
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Fecha de Nacimiento</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200"><?= !empty($est['fecha_nacimiento']) ? date('d/m/Y', strtotime($est['fecha_nacimiento'])) : 'No registrada' ?></span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Representante / Contacto</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($est['nombre_representante'] ?? 'N/A') ?></span>
                <span class="block text-[11px] font-mono text-slate-500"><?= htmlspecialchars($est['telefono_representante'] ?? '') ?></span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Dirección Domiciliaria</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block truncate" title="<?= htmlspecialchars($est['direccion'] ?? '') ?>"><?= htmlspecialchars($est['direccion'] ?? 'No registrada') ?></span>
            </div>
        </div>

        <!-- Grades Section -->
        <div class="space-y-3">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                <i class="bi bi-journal-bookmark-fill text-blue-500"></i>
                <span>Rendimiento Académico por Asignatura</span>
            </h3>

            <?php if (!empty($calificaciones)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-400 uppercase font-bold text-[10px]">
                                <th class="py-2 px-3">Código</th>
                                <th class="py-2 px-3">Asignatura</th>
                                <th class="py-2 px-3">Evaluaciones</th>
                                <th class="py-2 px-3 text-right">Promedio Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <?php foreach ($calificaciones as $c): 
                                $nota = (float)($c['promedio_materia'] ?? 0);
                                $nColor = $nota >= 10 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold';
                            ?>
                                <tr>
                                    <td class="py-2.5 px-3 font-mono text-slate-400"><?= htmlspecialchars($c['codigo'] ?? 'MAT') ?></td>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($c['materia']) ?></td>
                                    <td class="py-2.5 px-3 font-mono text-slate-500"><?= $c['total_evaluaciones'] ?> registradas</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-sm <?= $nColor ?>"><?= number_format($nota, 2) ?> pts</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-400 italic">No hay calificaciones registradas para este estudiante.</p>
            <?php endif; ?>
        </div>

        <!-- Attendance Summary -->
        <div class="space-y-3">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2 border-b border-slate-100 dark:border-slate-700/50 pb-2">
                <i class="bi bi-calendar-check-fill text-emerald-500"></i>
                <span>Historial de Asistencia y Puntualidad</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-emerald-50 dark:bg-emerald-950/30 p-3 rounded-xl border border-emerald-200/60 dark:border-emerald-900/40 text-center">
                    <span class="block text-lg font-black text-emerald-700 dark:text-emerald-400"><?= $asistencias['presentes'] ?? 0 ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300">Presente</span>
                </div>
                <div class="bg-rose-50 dark:bg-rose-950/30 p-3 rounded-xl border border-rose-200/60 dark:border-rose-900/40 text-center">
                    <span class="block text-lg font-black text-rose-700 dark:text-rose-400"><?= $asistencias['ausentes'] ?? 0 ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 dark:text-rose-300">Ausente</span>
                </div>
                <div class="bg-amber-50 dark:bg-amber-950/30 p-3 rounded-xl border border-amber-200/60 dark:border-amber-900/40 text-center">
                    <span class="block text-lg font-black text-amber-700 dark:text-amber-400"><?= $asistencias['tardanzas'] ?? 0 ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-300">Tardanzas</span>
                </div>
                <div class="bg-blue-50 dark:bg-blue-950/30 p-3 rounded-xl border border-blue-200/60 dark:border-blue-900/40 text-center">
                    <span class="block text-lg font-black text-blue-700 dark:text-blue-400"><?= $asistencias['justificados'] ?? 0 ?></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-300">Justificados</span>
                </div>
            </div>
        </div>

        <!-- Footer signatures -->
        <div class="pt-12 grid grid-cols-2 gap-8 text-center text-xs text-slate-400 print:pt-16">
            <div class="border-t border-slate-300 dark:border-slate-600 pt-2">
                <p class="font-bold text-slate-700 dark:text-slate-300">Firma del Director / Subdirector</p>
                <p class="text-[10px] text-slate-400">Sello Institucional</p>
            </div>
            <div class="border-t border-slate-300 dark:border-slate-600 pt-2">
                <p class="font-bold text-slate-700 dark:text-slate-300">Firma del Docente Guía</p>
                <p class="text-[10px] text-slate-400">Control de Estudios</p>
            </div>
        </div>

    </div>

</body>
</html>


