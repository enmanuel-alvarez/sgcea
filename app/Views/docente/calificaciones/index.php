<?php
/**
 * Vista: Docente - Gestión de Calificaciones y Planes de Evaluación (Modal Interactivo)
 */
$titulo = 'Gestión de Calificaciones y Planes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';
?>

<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Gestión de Calificaciones y Planes</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure los planes de evaluación (1 o varias actividades a la vez) y cargue calificaciones.</p>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-sm p-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="tablaAsignaciones" class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-500 dark:text-slate-400">
                    <th class="py-3 px-3">Materia</th>
                    <th class="py-3 px-3">Grado y Sección</th>
                    <th class="py-3 px-3">Año Académico</th>
                    <th class="py-3 px-3">Estado del Plan</th>
                    <th class="py-3 px-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                <?php foreach ($asignaciones as $asn): 
                    $tienePlan = !empty($asn['tiene_plan']);
                    $countAct = (int)($asn['actividades_count'] ?? 0);
                    $totalPond = (float)($asn['total_ponderacion'] ?? 0);
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                        <?= htmlspecialchars($asn['materia_nombre'] ?? '') ?>
                    </td>
                    <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                        <span class="font-medium"><?= htmlspecialchars($asn['grado_nombre'] ?? 'N/A') ?></span>
                        <span class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-bold ml-1"><?= htmlspecialchars($asn['seccion_nombre'] ?? 'N/A') ?></span>
                    </td>
                    <td class="py-3 px-3 font-mono text-xs text-slate-500 dark:text-slate-400">
                        <?= htmlspecialchars($asn['ano_academico'] ?? '2024-2025') ?>
                    </td>
                    <td class="py-3 px-3">
                        <?php if ($tienePlan): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                <i class="bi bi-check-circle-fill"></i>
                                <span><?= $countAct ?> ev. (<?= $totalPond ?>%)</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>Sin Plan Registrado</span>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-3 flex items-center justify-end space-x-2">
                        <button type="button" 
                                onclick='abrirModalPlan(<?= json_encode($asn, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                class="inline-flex items-center space-x-1 px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors">
                            <i class="bi bi-journal-plus"></i>
                            <span>Plan de Evaluación</span>
                        </button>

                        <?php if ($tienePlan): ?>
                            <a href="<?= url('/docente/calificaciones/registrar/' . $asn['id']) ?>" 
                               class="inline-flex items-center space-x-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg text-xs shadow-sm transition-colors">
                                <i class="bi bi-pencil-square"></i>
                                <span>Registrar Notas</span>
                            </a>
                        <?php else: ?>
                            <button type="button" disabled 
                                    class="inline-flex items-center space-x-1 px-3 py-1.5 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 font-semibold rounded-lg text-xs cursor-not-allowed opacity-75"
                                    title="Debe establecer al menos una evaluación en el plan antes de registrar notas">
                                <i class="bi bi-lock-fill"></i>
                                <span>Registrar Notas</span>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Registro de Plan de Evaluación (1 o varias actividades) -->
<div id="modalPlan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2" id="modalPlanTitulo">
                    <i class="bi bi-journal-text text-amber-500"></i> Plan de Evaluación
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" id="modalPlanSubtitulo">Asignatura</p>
            </div>
            <button type="button" onclick="cerrarModalPlan()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form method="POST" action="<?= url('/docente/planevaluacion/guardar-lote') ?>" id="formModalPlan">
            <input type="hidden" name="csrf_token" value="<?= \Src\Core\Security::generarTokenCSRF() ?>">
            <input type="hidden" name="id_asignacion" id="modalAsignacionId" value="0">

            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">

                <!-- Actividades Existentes -->
                <div id="seccionExistentes" class="hidden">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center justify-between">
                        <span>Actividades Existentes</span>
                        <span id="badgeTotalExistente" class="font-mono text-blue-600 dark:text-blue-400">Total: 0%</span>
                    </h4>
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-100 dark:bg-slate-900/60 font-semibold text-slate-600 dark:text-slate-300">
                                <tr>
                                    <th class="py-2 px-3">Nombre</th>
                                    <th class="py-2 px-3">Tipo</th>
                                    <th class="py-2 px-3">Ponderación</th>
                                    <th class="py-2 px-3">Fecha</th>
                                    <th class="py-2 px-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaExistentesBody" class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                <!-- Filas dinámicas existentes -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario para agregar 1 o varias evaluaciones al mismo tiempo -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Agregar Nuevas Actividades Evaluativas</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Puede agregar 1 o varias evaluaciones al mismo tiempo.</p>
                        </div>
                        <button type="button" onclick="agregarFilaActividad()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                            <i class="bi bi-plus-lg"></i>
                            <span>+ Agregar Otra Evaluación</span>
                        </button>
                    </div>

                    <!-- Lista de nuevas filas dinámicas -->
                    <div id="contenedorFilasNuevas" class="space-y-3">
                        <!-- Se insertan dinámicamente con JS -->
                    </div>

                    <!-- Indicador de total acumulado -->
                    <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Ponderación Total Acumulada:</span>
                        <span id="indicadorTotalPonderacion" class="font-mono font-bold text-sm text-blue-600 dark:text-blue-400">0% / 100%</span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700">
                <button type="button" onclick="cerrarModalPlan()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition-all">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs shadow-md shadow-amber-600/20 transition-all flex items-center space-x-1.5">
                    <i class="bi bi-check2-circle text-sm"></i>
                    <span>Guardar Plan de Evaluación</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let contadorFilas = 0;
let ponderacionExistenteTotal = 0;

$(document).ready(function() {
    $('#tablaAsignaciones').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'asc']]
    });
});

function abrirModalPlan(asn) {
    $('#modalAsignacionId').val(asn.id);
    $('#modalPlanTitulo').html('<i class="bi bi-journal-text text-amber-500"></i> Plan de Evaluación - ' + (asn.materia_nombre || ''));
    $('#modalPlanSubtitulo').text((asn.grado_nombre || '') + ' - Sección ' + (asn.seccion_nombre || '') + ' (' + (asn.ano_academico || '') + ')');
    
    // Limpiar tabla de existentes y contenedor de nuevas
    $('#tablaExistentesBody').empty();
    $('#contenedorFilasNuevas').empty();
    contadorFilas = 0;
    ponderacionExistenteTotal = 0;

    const actividades = asn.actividades || [];
    if (actividades.length > 0) {
        $('#seccionExistentes').removeClass('hidden');
        let htmlExist = '';
        actividades.forEach(function(act) {
            const pond = parseFloat(act.ponderacion) || 0;
            ponderacionExistenteTotal += pond;
            htmlExist += `
                <tr>
                    <td class="py-2 px-3 font-semibold text-slate-900 dark:text-white">${act.nombre}</td>
                    <td class="py-2 px-3 capitalize text-slate-600 dark:text-slate-300">${act.tipo || 'examen'}</td>
                    <td class="py-2 px-3 font-mono font-bold text-blue-600 dark:text-blue-400">${pond}%</td>
                    <td class="py-2 px-3 text-slate-500 font-mono">${act.fecha_programada || ''}</td>
                    <td class="py-2 px-3 text-right">
                        <a href="<?= url('/docente/planevaluacion/eliminar/') ?>${act.id}" 
                           onclick="return confirm('¿Eliminar esta actividad evaluativa?')"
                           class="text-rose-600 hover:text-rose-500 font-semibold" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
        $('#tablaExistentesBody').html(htmlExist);
        $('#badgeTotalExistente').text('Total: ' + ponderacionExistenteTotal + '%');
    } else {
        $('#seccionExistentes').addClass('hidden');
    }

    // Agregar la primera fila por defecto para nuevas evaluaciones
    agregarFilaActividad();
    calcularPonderacionTotal();

    $('#modalPlan').removeClass('hidden');
}

function cerrarModalPlan() {
    $('#modalPlan').addClass('hidden');
}

function agregarFilaActividad() {
    contadorFilas++;
    const idFila = 'fila_act_' + contadorFilas;
    const htmlFila = `
        <div id="${idFila}" class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700/80 relative grid grid-cols-1 sm:grid-cols-12 gap-3 items-end transition-all">
            <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Nombre Evaluación</label>
                <input type="text" name="actividades[${contadorFilas}][nombre]" required placeholder="Ej: Examen I, Taller 2"
                       class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Tipo</label>
                <select name="actividades[${contadorFilas}][tipo]" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs">
                    <option value="examen">Examen</option>
                    <option value="taller">Taller / Trabajo</option>
                    <option value="tarea">Tarea</option>
                    <option value="proyecto">Proyecto</option>
                    <option value="exposicion">Exposición</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Ponderación (%)</label>
                <input type="number" name="actividades[${contadorFilas}][ponderacion]" min="1" max="100" step="0.5" required placeholder="1-100"
                       oninput="calcularPonderacionTotal()"
                       class="input-ponderacion w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-center font-mono font-bold focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Fecha</label>
                <input type="date" name="actividades[${contadorFilas}][fecha_programada]" value="<?= date('Y-m-d') ?>"
                       class="w-full px-2 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono">
            </div>
            <div class="sm:col-span-1 text-right">
                <button type="button" onclick="eliminarFilaActividad('${idFila}')" class="p-1.5 text-rose-600 hover:text-rose-500 rounded-lg transition-colors" title="Quitar fila">
                    <i class="bi bi-trash text-base"></i>
                </button>
            </div>
        </div>
    `;
    $('#contenedorFilasNuevas').append(htmlFila);
    calcularPonderacionTotal();
}

function eliminarFilaActividad(idFila) {
    if ($('#contenedorFilasNuevas > div').length <= 1) {
        alert('Debe haber al menos 1 fila de evaluación.');
        return;
    }
    $('#' + idFila).remove();
    calcularPonderacionTotal();
}

function calcularPonderacionTotal() {
    let sumaNuevas = 0;
    $('.input-ponderacion').each(function() {
        const val = parseFloat($(this).val()) || 0;
        sumaNuevas += val;
    });
    const total = ponderacionExistenteTotal + sumaNuevas;
    $('#indicadorTotalPonderacion').text(total.toFixed(1) + '% / 100%');
    
    if (total > 100) {
        $('#indicadorTotalPonderacion').removeClass('text-blue-600 text-emerald-600 dark:text-blue-400 dark:text-emerald-400').addClass('text-rose-600 dark:text-rose-400');
    } else if (total === 100) {
        $('#indicadorTotalPonderacion').removeClass('text-blue-600 text-rose-600 dark:text-blue-400 dark:text-rose-400').addClass('text-emerald-600 dark:text-emerald-400');
    } else {
        $('#indicadorTotalPonderacion').removeClass('text-emerald-600 text-rose-600 dark:text-emerald-400 dark:text-rose-400').addClass('text-blue-600 dark:text-blue-400');
    }
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
