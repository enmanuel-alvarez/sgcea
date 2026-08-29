/**
 * Configuración común para DataTables en español
 */

// Configuración base para todos los DataTables
const datatableConfig = {
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
    },
    responsive: true,
    autoWidth: false,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    drawCallback: function() {
        // Re-inicializar tooltips después de cada redraw
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
};

// Inicializar DataTables al cargar el documento
document.addEventListener('DOMContentLoaded', function() {
    // Buscar todas las tablas con clase 'datatable'
    const tables = document.querySelectorAll('table.datatable');
    
    tables.forEach(function(table) {
        const options = Object.assign({}, datatableConfig);
        
        // Verificar si hay configuraciones específicas en data attributes
        if (table.dataset.order) {
            options.order = JSON.parse(table.dataset.order);
        }
        
        if (table.dataset.columns) {
            options.columns = JSON.parse(table.dataset.columns);
        }
        
        // Inicializar DataTable
        new DataTable(table, options);
    });
    
    // Inicializar DataTables con configuración específica por ID
    initializeSpecificDatatables();
});

/**
 * Inicializar DataTables con configuraciones específicas
 */
function initializeSpecificDatatables() {
    // Tabla de estudiantes
    if (document.getElementById('tabla-estudiantes')) {
        new DataTable('#tabla-estudiantes', {
            ...datatableConfig,
            order: [[1, 'asc']], // Ordenar por nombre
            columnDefs: [
                { orderable: false, targets: [-1] } // No ordenar última columna (acciones)
            ]
        });
    }
    
    // Tabla de docentes
    if (document.getElementById('tabla-docentes')) {
        new DataTable('#tabla-docentes', {
            ...datatableConfig,
            order: [[1, 'asc']]
        });
    }
    
    // Tabla de materias
    if (document.getElementById('tabla-materias')) {
        new DataTable('#tabla-materias', {
            ...datatableConfig,
            order: [[1, 'asc']]
        });
    }
    
    // Tabla de secciones
    if (document.getElementById('tabla-secciones')) {
        new DataTable('#tabla-secciones', {
            ...datatableConfig
        });
    }
    
    // Tabla de asignaciones
    if (document.getElementById('tabla-asignaciones')) {
        new DataTable('#tabla-asignaciones', {
            ...datatableConfig,
            order: [[1, 'asc']]
        });
    }
    
    // Tabla de usuarios
    if (document.getElementById('tabla-usuarios')) {
        new DataTable('#tabla-usuarios', {
            ...datatableConfig,
            order: [[1, 'asc']]
        });
    }
    
    // Tabla de constancias
    if (document.getElementById('tabla-constancias')) {
        new DataTable('#tabla-constancias', {
            ...datatableConfig,
            order: [[3, 'desc']], // Ordenar por fecha
            columnDefs: [
                { orderable: false, targets: [-1] }
            ]
        });
    }
    
    // Tabla de calificaciones (no ordenable en algunas columnas)
    if (document.getElementById('tabla-calificaciones')) {
        new DataTable('#tabla-calificaciones', {
            ...datatableConfig,
            paging: false,
            info: false,
            searching: false
        });
    }
    
    // Tabla de asistencia
    if (document.getElementById('tabla-asistencia')) {
        new DataTable('#tabla-asistencia', {
            ...datatableConfig,
            paging: false,
            info: false
        });
    }
}

/**
 * Función para exportar tabla a Excel (requiere Buttons extension)
 */
function exportTableToExcel(tableId, filename = '') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    filename = filename || 'export_' + new Date().toISOString().split('T')[0];
    
    // Si DataTables tiene Buttons extension
    if ($.fn.DataTable.Buttons) {
        const dt = $(table).DataTable();
        dt.buttons([
            {
                extend: 'excelHtml5',
                filename: filename,
                title: null,
                exportOptions: {
                    columns: ':visible:not(:last-child)' // Excluir columna de acciones
                }
            }
        ]).trigger();
    }
}

/**
 * Función para imprimir tabla
 */
function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    if ($.fn.DataTable.Buttons) {
        const dt = $(table).DataTable();
        dt.buttons([
            {
                extend: 'print',
                title: null,
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            }
        ]).trigger();
    }
}

/**
 * Filtrar tabla por columna
 */
function filterTableByColumn(tableId, columnIndex, searchTerm) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const dt = DataTable.api ? DataTable.api(table) : window.LaravelDataTables?.[tableId];
    
    if (dt && typeof dt.column === 'function') {
        dt.column(columnIndex).search(searchTerm).draw();
    }
}

/**
 * Recargar DataTable
 */
function reloadDatatable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const dt = DataTable.api ? DataTable.api(table) : window.LaravelDataTables?.[tableId];
    
    if (dt && typeof dt.ajax && typeof dt.ajax.reload === 'function') {
        dt.ajax.reload(null, false); // false mantiene la paginación actual
    } else {
        location.reload();
    }
}

