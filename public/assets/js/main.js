/**
 * JavaScript principal del sistema SGCEA
 * Sidebar toggle, búsqueda global, toasts y utilidades
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    initTooltips();
    
    // Inicializar toasts desde flash messages
    initToasts();
    
    // Búsqueda global en tablas
    initGlobalSearch();
    
    // Confirmación de eliminaciones
    initDeleteConfirmations();
    
    // Auto-ocultar alertas
    initAutoHideAlerts();
});

/**
 * Inicializar tooltips de Bootstrap
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Búsqueda global — filtra filas en la primera tabla visible de la página
 */
function initGlobalSearch() {
    const searchInput = document.getElementById('searchGlobal');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        
        // Buscar la tabla de contenido principal (excluir DataTables que tiene su propio buscador)
        const tables = document.querySelectorAll('main table tbody');
        
        tables.forEach(function(tbody) {
            const filas = tbody.getElementsByTagName('tr');
            for (let i = 0; i < filas.length; i++) {
                const textoFila = filas[i].textContent.toLowerCase();
                filas[i].style.display = textoFila.includes(filtro) ? '' : 'none';
            }
        });
        
        // También filtrar en DataTables si existe
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('table.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().search(filtro).draw();
                }
            });
        }
    });
}

/**
 * Mostrar toast notification
 */
function showToast(mensaje, tipo) {
    tipo = tipo || 'info';
    var toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    var toastId = 'toast-' + Date.now();
    var bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-primary'
    }[tipo] || 'bg-primary';
    
    var iconClass = {
        'success': 'bi-check-circle',
        'error': 'bi-exclamation-triangle',
        'warning': 'bi-exclamation-circle',
        'info': 'bi-info-circle'
    }[tipo] || 'bi-info-circle';
    
    var toastHtml = '<div id="' + toastId + '" class="toast align-items-center text-white ' + bgClass + ' border-0" role="alert">' +
        '<div class="d-flex">' +
        '<div class="toast-body"><i class="bi ' + iconClass + ' me-2"></i>' + mensaje + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
        '</div></div>';
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    var toastElement = document.getElementById(toastId);
    var toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Crear contenedor de toasts si no existe
 */
function createToastContainer() {
    var container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

/**
 * Inicializar toasts desde mensajes flash (PHP)
 */
function initToasts() {
    var flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(flash) {
        var tipo = flash.dataset.tipo || 'info';
        var mensaje = flash.textContent.trim();
        if (mensaje) {
            showToast(mensaje, tipo);
        }
        flash.remove();
    });
}

/**
 * Confirmación para acciones de eliminación
 */
function initDeleteConfirmations() {
    var deleteButtons = document.querySelectorAll('.btn-delete, [data-confirm-delete]');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            var message = this.dataset.confirmMessage || '¿Está seguro de eliminar este registro?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

/**
 * Auto-ocultar alertas después de unos segundos
 */
function initAutoHideAlerts() {
    var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Validar formulario antes de enviar
 */
function validateForm(formId) {
    var form = document.getElementById(formId);
    if (!form) return false;
    
    var inputs = form.querySelectorAll('[required]');
    var isValid = true;
    
    inputs.forEach(function(input) {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Limpiar campos de formulario
 */
function clearForm(formId) {
    var form = document.getElementById(formId);
    if (form) {
        form.reset();
        var invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(function(field) {
            field.classList.remove('is-invalid');
        });
    }
}

/**
 * Mostrar overlay de carga
 */
function showLoading(message) {
    message = message || 'Cargando...';
    var overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'spinner-overlay';
    overlay.innerHTML = '<div class="text-center text-white">' +
        '<div class="spinner-border text-light mb-3" role="status">' +
        '<span class="visually-hidden">Cargando...</span></div>' +
        '<p>' + message + '</p></div>';
    document.body.appendChild(overlay);
}

/**
 * Ocultar overlay de carga
 */
function hideLoading() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
}

/**
 * Exportar tabla a CSV
 */
function exportTableToCSV(tableId, filename) {
    filename = filename || 'export.csv';
    var table = document.getElementById(tableId);
    if (!table) return;
    
    var csv = [];
    var rows = table.querySelectorAll('tr');
    
    rows.forEach(function(row) {
        var cols = row.querySelectorAll('td, th');
        var rowData = [];
        
        cols.forEach(function(col) {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        
        csv.push(rowData.join(','));
    });
    
    downloadCSV(csv.join('\n'), filename);
}

/**
 * Descargar CSV
 */
function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}


