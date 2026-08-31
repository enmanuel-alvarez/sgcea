<?php
/**
 * Vista: Centro de Reportes y Análisis Académico (Tailwind CSS v3 + Chart.js)
 */
$titulo = 'Centro de Reportes y Análisis';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

$tipoReporte = $tipoReporte ?? 'general';
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5 no-print">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Centro de Reportes y Análisis Académico</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Generación de informes de rendimiento, alertas tempranas, asistencia e historial 360°.</p>
    </div>
    <?php if ($tipoReporte !== 'general'): ?>
        <div class="flex items-center space-x-2">
            <a href="<?= url('/reportes/exportar/' . $tipoReporte) ?>" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                <i class="bi bi-file-earmark-excel-fill"></i>
                <span>Exportar CSV</span>
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-1.5">
                <i class="bi bi-printer-fill"></i>
                <span>Imprimir PDF</span>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- CATEGORY CARDS SELECTOR -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 category-cards-grid no-print">
    
    <!-- CARD 1: ACADÉMICOS -->
    <a href="<?= url('/reportes?tipo=cuadro_honor') ?>" 
       class="p-5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-3 <?= in_array($tipoReporte, ['cuadro_honor', 'riesgo_academico', 'sabana_seccion']) ? 'bg-gradient-to-br from-blue-600 to-indigo-700 text-white border-blue-600 shadow-lg shadow-blue-500/20' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-blue-500/50 hover:shadow-md' ?>">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-xl <?= in_array($tipoReporte, ['cuadro_honor', 'riesgo_academico', 'sabana_seccion']) ? 'text-white' : 'text-blue-600 dark:text-blue-400' ?>">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= in_array($tipoReporte, ['cuadro_honor', 'riesgo_academico', 'sabana_seccion']) ? 'bg-white/20 text-white' : 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300' ?>">Académico</span>
        </div>
        <div>
            <h3 class="font-bold text-sm">Cuadro de Honor & Riesgo</h3>
            <p class="text-xs opacity-80 mt-0.5">Top promedios y estudiantes en riesgo.</p>
        </div>
    </a>

    <!-- CARD 2: ASISTENCIA -->
    <a href="<?= url('/reportes?tipo=ausentismo_critico') ?>" 
       class="p-5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-3 <?= $tipoReporte === 'ausentismo_critico' ? 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white border-emerald-600 shadow-lg shadow-emerald-500/20' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-emerald-500/50 hover:shadow-md' ?>">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-xl <?= $tipoReporte === 'ausentismo_critico' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400' ?>">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $tipoReporte === 'ausentismo_critico' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' ?>">Asistencia</span>
        </div>
        <div>
            <h3 class="font-bold text-sm">Ausentismo Crítico</h3>
            <p class="text-xs opacity-80 mt-0.5">Alertas de inasistencias acumuladas.</p>
        </div>
    </a>

    <!-- CARD 3: CARGA DOCENTE -->
    <a href="<?= url('/reportes?tipo=carga_docente') ?>" 
       class="p-5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-3 <?= $tipoReporte === 'carga_docente' ? 'bg-gradient-to-br from-indigo-600 to-purple-700 text-white border-indigo-600 shadow-lg shadow-indigo-500/20' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-500/50 hover:shadow-md' ?>">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-xl <?= $tipoReporte === 'carga_docente' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400' ?>">
                <i class="bi bi-person-workspace"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $tipoReporte === 'carga_docente' ? 'bg-white/20 text-white' : 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-300' ?>">Docentes</span>
        </div>
        <div>
            <h3 class="font-bold text-sm">Carga Horaria Docente</h3>
            <p class="text-xs opacity-80 mt-0.5">Asignaciones y secciones por profesor.</p>
        </div>
    </a>

    <!-- CARD 4: FICHA 360° -->
    <a href="<?= url('/reportes?tipo=ficha_360') ?>" 
       class="p-5 rounded-2xl border transition-all text-left flex flex-col justify-between space-y-3 <?= $tipoReporte === 'ficha_360' ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white border-amber-500 shadow-lg shadow-amber-500/20' : 'bg-white dark:bg-slate-800 border-slate-200/80 dark:border-slate-700/80 hover:border-amber-500/50 hover:shadow-md' ?>">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center text-xl <?= $tipoReporte === 'ficha_360' ? 'text-white' : 'text-amber-600 dark:text-amber-400' ?>">
                <i class="bi bi-person-vcard-fill"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $tipoReporte === 'ficha_360' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' ?>">Expediente</span>
        </div>
        <div>
            <h3 class="font-bold text-sm">Ficha 360° Estudiante</h3>
            <p class="text-xs opacity-80 mt-0.5">Expediente imprimible por alumno.</p>
        </div>
    </a>
</div>

<!-- DYNAMIC FILTER BAR -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-4 mb-8 no-print">
    <form action="<?= url('/reportes') ?>" method="GET" class="flex flex-col sm:flex-row items-end sm:items-center justify-between gap-4">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte) ?>">

        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <!-- Filter by Section -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Filtrar por Sección</label>
                <select name="seccion_id" class="px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las secciones</option>
                    <?php foreach ($secciones as $sec): ?>
                        <option value="<?= $sec['id'] ?>" <?= ($seccionId == $sec['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($sec['grado_nombre'] ?? 'Grado') . ' - Sección ' . $sec['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter by Student (for Ficha 360) -->
            <?php if ($tipoReporte === 'ficha_360'): ?>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Seleccionar Estudiante *</label>
                <select name="estudiante_id" required class="px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Seleccionar alumno --</option>
                    <?php foreach ($estudiantes as $est): ?>
                        <?php
                            $nom = $est['nombres'] ?? $est['nombre'] ?? '';
                            $ape = $est['apellidos'] ?? $est['apellido'] ?? '';
                            $ced = $est['cedula'] ?? '';
                        ?>
                        <option value="<?= $est['id'] ?>" <?= (isset($estudianteId) && $estudianteId == $est['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim("$ced - $nom $ape", ' -')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center space-x-1.5">
            <i class="bi bi-funnel"></i>
            <span>Generar Reporte</span>
        </button>
    </form>
</div>

<!-- REPORT DISPLAY SECTION -->
<div id="reporteContainer" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-6 space-y-6 printable-report">

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
                    Control Escolar-Académico • Año Lectivo <?= htmlspecialchars($config['ano_academico_actual'] ?? date('Y')) ?>
                </p>
            </div>
        </div>
        <div class="text-left sm:text-right text-[11px] font-mono text-slate-600 dark:text-slate-300 shrink-0 border-l sm:border-l-0 sm:border-r border-slate-200 dark:border-slate-700 pl-3 sm:pl-0 sm:pr-3">
            <p class="font-bold text-slate-900 dark:text-white uppercase">Reporte Institucional</p>
            <p><i class="bi bi-clock me-1"></i>Emisión: <?= date('d/m/Y h:i A') ?></p>
            <p><i class="bi bi-person me-1"></i>Emisor: <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador') ?></p>
            <p class="text-blue-600 dark:text-blue-400 font-bold mt-0.5">CÓDIGO: SGCEA-REP-<?= date('Ymd') ?></p>
        </div>
    </div>

    <?php if ($tipoReporte === 'cuadro_honor'): ?>
        <!-- CUADRO DE HONOR -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-trophy-fill text-amber-500"></i>
                <span>Cuadro de Honor - Mejores Promedios</span>
            </h3>
            <span class="text-xs font-mono text-slate-400">Top <?= is_array($datosReporte) ? count($datosReporte) : 0 ?> Alumnos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="tablaReporte">
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Cédula</th>
                        <th>Estudiante</th>
                        <th>Grado & Sección</th>
                        <th>Promedio Acumulado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php if (!empty($datosReporte)): ?>
                        <?php foreach ($datosReporte as $idx => $r): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="font-bold text-xs text-slate-500">
                                    <?php if ($idx == 0): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 font-bold">🥇 1° Lugar</span>
                                    <?php elseif ($idx == 1): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 font-bold">🥈 2° Lugar</span>
                                    <?php elseif ($idx == 2): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 font-bold">🥉 3° Lugar</span>
                                    <?php else: ?>
                                        #<?= $idx + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td class="font-mono text-xs text-slate-600 dark:text-slate-300"><?= htmlspecialchars($r['cedula']) ?></td>
                                <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></td>
                                <td class="text-xs text-slate-600 dark:text-slate-400"><?= htmlspecialchars($r['grado'] . ' - ' . $r['seccion']) ?></td>
                                <td>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-mono font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <?= number_format((float)$r['promedio_general'], 2) ?> pts
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('/reportes/ficha360/' . $r['estudiante_id']) ?>" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-semibold" title="Ver Ficha 360°">
                                        Ficha 360°
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tipoReporte === 'riesgo_academico'): ?>
        <!-- RIESGO ACADÉMICO -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-exclamation-triangle-fill text-rose-500"></i>
                <span>Alerta Temprana - Estudiantes en Riesgo Académico</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="tablaReporte">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Estudiante</th>
                        <th>Grado & Sección</th>
                        <th>Materias Reprobadas</th>
                        <th>Promedio</th>
                        <th>Representante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php if (!empty($datosReporte)): ?>
                        <?php foreach ($datosReporte as $r): ?>
                            <tr class="hover:bg-rose-50/50 dark:hover:bg-rose-950/20 transition-colors">
                                <td class="font-mono text-xs"><?= htmlspecialchars($r['cedula']) ?></td>
                                <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></td>
                                <td class="text-xs"><?= htmlspecialchars($r['grado'] . ' - ' . $r['seccion']) ?></td>
                                <td>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">
                                        <?= $r['materias_reprobadas'] ?> asignaturas
                                    </span>
                                </td>
                                <td class="font-mono font-bold text-rose-600 dark:text-rose-400"><?= number_format((float)$r['promedio_general'], 2) ?> pts</td>
                                <td class="text-xs font-mono text-slate-600 dark:text-slate-400"><?= htmlspecialchars($r['telefono_representante'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tipoReporte === 'ausentismo_critico'): ?>
        <!-- AUSENTISMO CRÍTICO -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-calendar-x-fill text-rose-500"></i>
                <span>Alerta de Ausentismo Crítico (> 20% Inasistencias)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="tablaReporte">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Estudiante</th>
                        <th>Grado & Sección</th>
                        <th>Clases Impartidas</th>
                        <th>Inasistencias</th>
                        <th>% Ausentismo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php if (!empty($datosReporte)): ?>
                        <?php foreach ($datosReporte as $r): ?>
                            <tr class="hover:bg-rose-50/50 dark:hover:bg-rose-950/20 transition-colors">
                                <td class="font-mono text-xs"><?= htmlspecialchars($r['cedula']) ?></td>
                                <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></td>
                                <td class="text-xs"><?= htmlspecialchars($r['grado'] . ' - ' . $r['seccion']) ?></td>
                                <td class="font-mono text-xs"><?= $r['total_clases'] ?> clases</td>
                                <td class="font-mono text-xs text-rose-600 font-bold"><?= $r['ausencias'] ?> inasistencias</td>
                                <td>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300">
                                        <?= $r['pct_ausencia'] ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tipoReporte === 'carga_docente'): ?>
        <!-- CARGA DOCENTE -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <i class="bi bi-person-workspace text-indigo-500"></i>
                <span>Carga Horaria y Asignaciones Académicas Docentes</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm" id="tablaReporte">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Docente</th>
                        <th>Especialidad</th>
                        <th>Materias Impartidas</th>
                        <th>Secciones Atendidas</th>
                        <th>Total Asignaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <?php if (!empty($datosReporte)): ?>
                        <?php foreach ($datosReporte as $r): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="font-mono text-xs"><?= htmlspecialchars($r['cedula']) ?></td>
                                <td class="font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></td>
                                <td class="text-xs text-slate-600 dark:text-slate-300"><?= htmlspecialchars($r['especialidad'] ?? 'General') ?></td>
                                <td class="font-mono text-xs me-2"><?= $r['total_materias'] ?> materias</td>
                                <td class="font-mono text-xs me-2"><?= $r['total_secciones'] ?> secciones</td>
                                <td>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        <?= $r['total_asignaciones'] ?> asignaciones
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- VISTA GENERAL / ESTADÍSTICAS DEL SISTEMA -->
        <div class="text-center py-10 space-y-3">
            <i class="bi bi-bar-chart-line-fill text-5xl text-blue-500/40"></i>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Seleccione una categoría de reporte arriba</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Elija entre Cuadro de Honor, Alerta Temprana de Riesgo, Ausentismo Crítico, Carga Docente o Ficha 360° del Estudiante.
            </p>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN DE FIRMAS Y SELLOS INSTITUCIONALES PARA IMPRESIÓN -->
    <div class="firmas-impresion pt-8 border-t border-slate-200 dark:border-slate-700 hidden print:flex justify-between items-center text-center text-xs">
        <div class="firma-box w-2/5">
            <div class="h-12"></div>
            <div class="firma-linea border-t border-slate-900 dark:border-slate-100 pt-1">
                <p class="font-bold text-slate-900 dark:text-white uppercase">Director / Subdirector Académico</p>
                <p class="text-[10px] text-slate-500">Firma y Sello Oficial del Plantel</p>
            </div>
        </div>
        <div class="firma-box w-2/5">
            <div class="h-12"></div>
            <div class="firma-linea border-t border-slate-900 dark:border-slate-100 pt-1">
                <p class="font-bold text-slate-900 dark:text-white uppercase">Coordinación de Control de Estudios</p>
                <p class="text-[10px] text-slate-500">Verificación y Validación Institucional</p>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    if ($('#tablaReporte').length) {
        $('#tablaReporte').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: true
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>


