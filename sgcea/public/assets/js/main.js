/**
 * JavaScript principal del sistema
 * Manejo de modo oscuro, toasts y utilidades
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar modo oscuro/claro
    initDarkMode();
    
    // Inicializar tooltips de Bootstrap
    initTooltips();
    
    // Inicializar toasts
    initToasts();
    
    // Toggle sidebar en móviles
    initSidebarToggle();
    
    // Confirmación de eliminaciones
    initDeleteConfirmations();
    
    // Auto-ocultar alertas
    initAutoHideAlerts();
});

/**
 * Modo oscuro/claro
 */
function initDarkMode() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    
    // Cargar preferencia guardada
    const savedTheme = localStorage.getItem('theme') || 'light';
    body.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Actualizar ícono
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        });
    }
}

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
 * Mostrar toast notification
 */
function showToast(mensaje, tipo = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toastId = 'toast-' + Date.now();
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-primary'
    }[tipo] || 'bg-primary';
    
    const iconClass = {
        'success': 'bi-check-circle',
        'error': 'bi-exclamation-triangle',
        'warning': 'bi-exclamation-circle',
        'info': 'bi-info-circle'
    }[tipo] || 'bi-info-circle';
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconClass} me-2"></i>${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();
    
    // Eliminar después de ocultar
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Crear contenedor de toasts si no existe
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

/**
 * Inicializar toasts desde mensajes flash (PHP)
 */
function initToasts() {
    // Los mensajes flash se renderizan desde PHP en el footer
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(flash) {
        const tipo = flash.dataset.tipo || 'info';
        const mensaje = flash.textContent.trim();
        if (mensaje) {
            showToast(mensaje, tipo);
        }
        flash.remove();
    });
}

/**
 * Toggle sidebar en dispositivos móviles
 */
function initSidebarToggle() {
    const toggleButton = document.getElementById('sidebar-toggle');
    const wrapper = document.getElementById('wrapper');
    
    if (toggleButton && wrapper) {
        toggleButton.addEventListener('click', function() {
            wrapper.classList.toggle('toggled');
        });
    }
}

/**
 * Confirmación para acciones de eliminación
 */
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.btn-delete, [data-confirm-delete]');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirmMessage || '¿Está seguro de eliminar este registro?';
            
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
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Validar formulario antes de enviar
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
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
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(function(field) {
            field.classList.remove('is-invalid');
        });
    }
}

/**
 * Mostrar overlay de carga
 */
function showLoading(message = 'Cargando...') {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'spinner-overlay';
    overlay.innerHTML = `
        <div class="text-center text-white">
            <div class="spinner-border text-light mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

/**
 * Ocultar overlay de carga
 */
function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
}

/**
 * Formatear moneda
 */
function formatCurrency(amount, currency = 'VES') {
    return new Intl.NumberFormat('es-VE', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Exportar tabla a CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(function(row) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
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
    const csvFile = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
