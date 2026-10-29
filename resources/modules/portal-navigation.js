function initPortalMobileNav() {
    const toggle = document.querySelector('.portal-mobile-toggle');
    const menu = document.getElementById('portal-mobile-menu');

    if (!toggle || !menu) {
        return;
    }

    function setMenuOpen(isOpen) {
        menu.classList.toggle('open', isOpen);
        menu.inert = !isOpen;
        menu.setAttribute('aria-hidden', String(!isOpen));
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
    }

    menu.inert = true;

    toggle.addEventListener('click', function () {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        setMenuOpen(!isOpen);
    });

    menu.addEventListener('click', function (event) {
        if (event.target.closest('a')) {
            setMenuOpen(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(false);
            toggle.focus();
        }
    });

    document.addEventListener('click', function (event) {
        if (!menu.contains(event.target) && !toggle.contains(event.target)) {
            setMenuOpen(false);
        }
    });
}

document.addEventListener('DOMContentLoaded', initPortalMobileNav);
