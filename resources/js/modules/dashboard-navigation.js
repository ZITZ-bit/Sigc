/**
 * Navegación del dashboard de paciente.
 * Maneja el menú móvil y los controles de accesibilidad del sidebar.
 */

function initPacienteMobileNav() {
    const toggle = document.querySelector('.paciente-mobile-toggle');
    const close = document.querySelector('.paciente-mobile-close');
    const sidebar = document.getElementById('paciente-mobile-sidebar');
    const overlay = document.querySelector('.paciente-sidebar-overlay');

    if (!toggle || !sidebar) return;

    function openNav() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closeNav() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        if (sidebar.classList.contains('open')) {
            closeNav();
        } else {
            openNav();
        }
    });

    if (close) {
        close.addEventListener('click', closeNav);
    }

    if (overlay) {
        overlay.addEventListener('click', closeNav);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initPacienteMobileNav();
});
