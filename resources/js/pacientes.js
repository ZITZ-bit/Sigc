const page = document.querySelector('.patient-page');

if (page) {
    const views = [...page.querySelectorAll('.panel-view')];
    const navigationButtons = [...page.querySelectorAll('.dashboard-sidebar [data-view]')];
    const toast = page.querySelector('.patient-toast');
    const dateHeading = page.querySelector('[data-schedule-date]');
    const weekGrid = page.querySelector('[data-week-grid]');
    let currentDate = new Date(2026, 9, 15);
    let toastTimer;
    let activeSpecialty = '';
    let selectedSlot;

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

    page.querySelector('[data-profile-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        showToast('Cambios de muestra listos. No se enviaron ni guardaron datos.');
    });

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

    page.querySelectorAll('[data-doctor-schedule]').forEach((button) => {
        button.addEventListener('click', () => {
            openView('horarios');
            page.querySelectorAll('.slot').forEach((slot) => {
                slot.hidden = slot.dataset.doctor !== button.dataset.doctorSchedule;
            });
            dateHeading.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    function renderWeek() {
        const monday = new Date(currentDate);
        monday.setDate(currentDate.getDate() - ((currentDate.getDay() + 6) % 7));
        const sampleEvents = [
            ['Dra. Andrea Méndez · 09:00', 'Dr. Diego Ramírez · 10:30'],
            ['Dra. Lucía Torres · 11:30'],
            ['Dra. Andrea Méndez · 09:00'],
            ['Dr. Diego Ramírez · 10:30'],
            ['Dra. Lucía Torres · 11:30'],
            [],
            [],
        ];
        weekGrid.replaceChildren(...sampleEvents.map((events, index) => {
            const date = new Date(monday);
            date.setDate(monday.getDate() + index);
            const column = document.createElement('article');
            column.className = 'patient-week-day';
            const heading = document.createElement('h3');
            heading.textContent = date.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric' });
            column.append(heading);
            events.forEach((event) => {
                const item = document.createElement('span');
                item.textContent = event;
                column.append(item);
            });
            return column;
        }));
    }

    function renderDate() {
        const formattedDate = currentDate.toLocaleDateString('es-MX', {
            weekday: 'long', day: 'numeric', month: 'short',
        });
        dateHeading.textContent = formattedDate.charAt(0).toLocaleUpperCase('es') + formattedDate.slice(1);
        renderWeek();
    }

    page.querySelectorAll('[data-date-step]').forEach((button) => {
        button.addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() + Number(button.dataset.dateStep));
            renderDate();
        });
    });
    page.querySelectorAll('[data-schedule-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            const weekMode = button.dataset.scheduleMode === 'week';
            page.querySelectorAll('[data-schedule-mode]').forEach((item) => {
                const active = item === button;
                item.classList.toggle('is-active', active);
                item.setAttribute('aria-pressed', String(active));
            });
            page.querySelector('.calendar-grid').hidden = weekMode;
            weekGrid.hidden = !weekMode;
        });
    });

    const reservationDialog = page.querySelector('[data-reservation-dialog]');
    const reservationDescription = page.querySelector('[data-reservation-description]');
    page.querySelectorAll('[data-reserve]').forEach((button) => {
        button.addEventListener('click', () => {
            selectedSlot = button.closest('.slot');
            reservationDescription.textContent = `${selectedSlot.querySelector('span').textContent} · ${selectedSlot.querySelector('small').textContent}. Esta solicitud solo se simulará en pantalla.`;
            reservationDialog.showModal();
        });
    });
    page.querySelector('[data-close-reservation]').addEventListener('click', () => reservationDialog.close());
    page.querySelector('[data-reservation-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        if (selectedSlot) {
            const reserveButton = selectedSlot.querySelector('[data-reserve]');
            reserveButton.textContent = 'Solicitada';
            reserveButton.disabled = true;
            selectedSlot.classList.add('slot-requested');
        }
        reservationDialog.close();
        showToast('Solicitud de muestra creada localmente. No se envió al sistema.');
    });

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

    page.querySelectorAll('.notification-item').forEach((item) => item.addEventListener('click', () => {
        item.classList.remove('notification-unread');
        showToast('Notificación marcada como leída.');
    }));
    page.querySelector('[data-mark-notifications]').addEventListener('click', () => {
        page.querySelectorAll('.notification-unread').forEach((item) => item.classList.remove('notification-unread'));
        showToast('Todas las notificaciones están marcadas como leídas.');
    });

    renderDate();
}
