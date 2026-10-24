const page = document.querySelector('.patient-page');

if (page) {
    const views = [...page.querySelectorAll('.panel-view')];
    const navigationButtons = [...page.querySelectorAll('.dashboard-sidebar [data-view]')];
    const toast = page.querySelector('.patient-toast');
    let toastTimer;

    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
    }

    function openView(viewName) {
        views.forEach((view) => view.classList.toggle('active', view.id === `view-${viewName}`));
        navigationButtons.forEach((button) => {
            const active = button.dataset.view === viewName;
            button.classList.toggle('side-icon-active', active);
            if (active) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
    }

    navigationButtons.forEach((button) => button.addEventListener('click', () => openView(button.dataset.view)));
    page.querySelectorAll('.patient-profile-link').forEach((button) => button.addEventListener('click', () => openView(button.dataset.view)));

    page.querySelector('.profile-edit').addEventListener('click', () => {
        const firstField = page.querySelector('.profile-form input');
        firstField.focus();
        firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Médicos: filtrar horarios por médico al pulsar "Ver horario"
    page.querySelectorAll('[data-view-horarios]').forEach((button) => {
        button.addEventListener('click', () => {
            openView('horarios');
            const medicoId = button.dataset.medicoId;
            page.querySelectorAll('.slot').forEach((slot) => {
                slot.hidden = medicoId && slot.dataset.medicoId !== medicoId;
            });
        });
    });

    // Diálogo de reserva de consulta (envía a ruta real)
    const reservationDialog = page.querySelector('[data-reservation-dialog]');
    const reservationMedico = page.querySelector('[data-reservation-medico]');
    const reservationMedicoName = page.querySelector('[data-reservation-medico-name]');

    page.querySelectorAll('[data-reserve]').forEach((button) => {
        button.addEventListener('click', () => {
            const slot = button.closest('.slot');
            reservationMedico.value = slot.dataset.medicoId;
            reservationMedicoName.value = slot.querySelector('span').textContent;
            reservationDialog.showModal();
        });
    });

    page.querySelector('[data-close-reservation]').addEventListener('click', () => reservationDialog.close());
    reservationDialog.addEventListener('click', (event) => {
        if (event.target === reservationDialog) reservationDialog.close();
    });

    // Filtro de pestañas de consultas (estado real de cada consulta)
    page.querySelectorAll('[data-consultation-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.consultationFilter;
            page.querySelectorAll('[data-consultation-filter]').forEach((item) => item.classList.toggle('active', item === button));
            let visibleCount = 0;
            page.querySelectorAll('[data-consultation-status]').forEach((card) => {
                const visible = filter === 'all' || card.dataset.consultationStatus === filter;
                card.hidden = !visible;
                if (visible) visibleCount += 1;
            });
            page.querySelector('[data-consultations-empty]').hidden = visibleCount > 0;
        });
    });

    // Búsqueda y filtro de especialidad de médicos (datos reales renderizados desde el servidor)
    let activeSpecialty = '';

    function filterDoctors() {
        const query = page.querySelector('[data-doctor-search]').value.trim().toLocaleLowerCase('es');
        let visibleCount = 0;
        page.querySelectorAll('.doctor-card').forEach((card) => {
            const matchesQuery = card.textContent.toLocaleLowerCase('es').includes(query);
            const matchesSpecialty = !activeSpecialty || card.dataset.specialty === activeSpecialty;
            const visible = matchesQuery && matchesSpecialty;
            card.hidden = !visible;
            if (visible) visibleCount += 1;
        });
        page.querySelector('[data-doctor-empty]').hidden = visibleCount > 0;
        page.querySelector('[data-clear-doctor-filter]').hidden = !activeSpecialty && !query;
    }

    page.querySelector('[data-doctor-search]').addEventListener('input', filterDoctors);
    page.querySelectorAll('[data-specialty-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            activeSpecialty = activeSpecialty === button.dataset.specialtyFilter ? '' : button.dataset.specialtyFilter;
            page.querySelectorAll('[data-specialty-filter]').forEach((item) => {
                const selected = item.dataset.specialtyFilter === activeSpecialty;
                item.classList.toggle('is-selected', selected);
                item.setAttribute('aria-pressed', String(selected));
            });
            filterDoctors();
        });
    });
    page.querySelector('[data-clear-doctor-filter]').addEventListener('click', () => {
        activeSpecialty = '';
        page.querySelector('[data-doctor-search]').value = '';
        page.querySelectorAll('[data-specialty-filter]').forEach((button) => {
            button.classList.remove('is-selected');
            button.setAttribute('aria-pressed', 'false');
        });
        filterDoctors();
    });

    // Notificaciones: marcar como leída (estado de interfaz únicamente)
    page.querySelectorAll('.notification-item').forEach((item) => {
        item.addEventListener('click', () => {
            item.classList.remove('notification-unread');
        });
    });
}
