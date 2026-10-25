import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Bell,
    CalendarDays,
    ChartNoAxesCombined,
    ClipboardPlus,
    Clock3,
    createIcons,
    Eye,
    EyeOff,
    FileHeart,
    HeartPulse,
    House,
    Inbox,
    LogOut,
    LockKeyhole,
    Menu,
    PanelLeftClose,
    Settings2,
    ShieldCheck,
    Stethoscope,
    UserRound,
    UsersRound,
    X,
} from 'lucide';

createIcons({
    icons: {
        ArrowLeft,
        ArrowRight,
        ArrowUpRight,
        Bell,
        CalendarDays,
        ChartNoAxesCombined,
        ClipboardPlus,
        Clock3,
        Eye,
        EyeOff,
        FileHeart,
        HeartPulse,
        House,
        Inbox,
        LogOut,
        LockKeyhole,
        Menu,
        PanelLeftClose,
        Settings2,
        ShieldCheck,
        Stethoscope,
        UserRound,
        UsersRound,
        X,
    },
    attrs: { 'stroke-width': 1.8 },
});

document.querySelectorAll('[data-toggle-password]').forEach((toggle) => {
    const input = document.getElementById(toggle.dataset.togglePassword);

    if (!input) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isVisible = input.type === 'text';
        const icon = document.createElement('i');
        const label = isVisible ? 'Mostrar contraseña' : 'Ocultar contraseña';

        input.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-label', label);
        toggle.title = label;
        icon.dataset.lucide = isVisible ? 'eye' : 'eye-off';
        icon.setAttribute('aria-hidden', 'true');
        toggle.replaceChildren(icon);
        createIcons({ icons: { Eye, EyeOff }, attrs: { 'stroke-width': 1.8 }, root: toggle });
    });
});

const sidebar = document.querySelector('[data-dashboard-sidebar]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

if (sidebar && sidebarToggle) {
    const storageKey = `dashboard-sidebar-collapsed-${sidebar.dataset.dashboardSidebar}`;

    const setCollapsed = (collapsed) => {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        sidebarToggle.setAttribute('aria-pressed', String(collapsed));
        sidebarToggle.setAttribute('aria-label', collapsed ? 'Expandir menú' : 'Plegar menú');
        sidebarToggle.setAttribute('title', collapsed ? 'Expandir menú' : 'Plegar menú');

        sidebar.querySelectorAll('a[aria-label]').forEach((link) => {
            link.setAttribute('title', link.getAttribute('aria-label'));
        });
    };

    setCollapsed(localStorage.getItem(storageKey) === 'true');

    sidebarToggle.addEventListener('click', () => {
        const collapsed = !document.body.classList.contains('sidebar-collapsed');
        setCollapsed(collapsed);
        localStorage.setItem(storageKey, String(collapsed));
    });
}

const menuTriggers = document.querySelectorAll('[data-mobile-nav-toggle]');
const overlay = document.querySelector('.paciente-sidebar-overlay, .medico-sidebar-overlay, .coordinacion-sidebar-overlay');

function getMenu(trigger) {
    return document.getElementById(trigger.getAttribute('aria-controls'));
}

function setMenuState(menu, trigger, isOpen) {
    menu.classList.toggle('open', isOpen);
    menu.inert = !isOpen;
    menu.setAttribute('aria-hidden', String(!isOpen));
    trigger.setAttribute('aria-expanded', String(isOpen));
    overlay?.classList.toggle('open', isOpen);

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
