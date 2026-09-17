/**
 * SISTEMA DE MESA DE PARTES VIRTUAL — IESP TÚPAC AMARU CUSCO
 * JavaScript de Soporte del Panel Administrativo
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle del Sidebar en dispositivos móviles
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    let backdrop = null;

    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');

            if (adminSidebar.classList.contains('show')) {
                backdrop = document.createElement('div');
                backdrop.className = 'sidebar-backdrop';
                document.body.appendChild(backdrop);
                backdrop.addEventListener('click', closeSidebar);
            } else {
                closeSidebar();
            }
        });
    }

    function closeSidebar() {
        if (adminSidebar) {
            adminSidebar.classList.remove('show');
        }
        if (backdrop && backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
            backdrop = null;
        }
    }

    // 2. Inicializar Tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // 3. Confirmaciones de acciones críticas (Borrar, Anular, Archivar)
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || '¿Está seguro de realizar esta acción?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
