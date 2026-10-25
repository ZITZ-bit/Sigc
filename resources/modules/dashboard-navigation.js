const menuTriggers = document.querySelectorAll('[data-mobile-nav-toggle]');

function getMenu(trigger) {
    return document.getElementById(trigger.getAttribute('aria-controls'));
}

function setMenuState(menu, trigger, isOpen) {
    menu.classList.toggle('open', isOpen);
    menu.inert = !isOpen;
    menu.setAttribute('aria-hidden', String(!isOpen));
    trigger.setAttribute('aria-expanded', String(isOpen));

    document.querySelectorAll('[data-mobile-nav-close]').forEach((closeButton) => {
        if (closeButton.dataset.mobileNavClose === menu.id) {
            closeButton.classList.toggle('open', isOpen);
        }
    });
}

menuTriggers.forEach((trigger) => {
    const menu = getMenu(trigger);

    if (!menu) {
        return;
    }

    menu.inert = true;
    menu.setAttribute('aria-hidden', 'true');

    trigger.addEventListener('click', () => {
        const isOpen = trigger.getAttribute('aria-expanded') === 'true';
        setMenuState(menu, trigger, !isOpen);

        if (!isOpen) {
            menu.querySelector('button, a')?.focus();
        }
    });

    menu.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }

        const focusableItems = menu.querySelectorAll('a[href], button:not([disabled])');
        const firstItem = focusableItems[0];
        const lastItem = focusableItems[focusableItems.length - 1];

        if (event.shiftKey && document.activeElement === firstItem) {
            event.preventDefault();
            lastItem.focus();
        } else if (!event.shiftKey && document.activeElement === lastItem) {
            event.preventDefault();
            firstItem.focus();
        }
    });

    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setMenuState(menu, trigger, false);
            trigger.focus();
        }
    });

    document.querySelectorAll('[data-mobile-nav-close]').forEach((closeButton) => {
        if (closeButton.dataset.mobileNavClose !== menu.id) {
            return;
        }

        closeButton.addEventListener('click', () => {
            setMenuState(menu, trigger, false);
            trigger.focus();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
            setMenuState(menu, trigger, false);
            trigger.focus();
        }
    });
});
