const views = [...document.querySelectorAll('.admin-view')];
const navItems = [...document.querySelectorAll('[data-admin-view]')];
const modal = document.querySelector('.admin-modal');
const modalTitle = document.querySelector('#admin-modal-title');
const modalFields = document.querySelector('[data-modal-fields]');
const toast = document.querySelector('.admin-toast');
const globalSearch = document.querySelector('.admin-search input');
let toastTimer;
let calendarOffset = 0;
let globalQuery = '';

const modalConfigs = {
    paciente: { title: 'Nuevo paciente de muestra', fields: [['Nombre completo', 'text'], ['Correo electrónico', 'email'], ['Teléfono', 'tel']] },
    medico: { title: 'Nuevo médico de muestra', fields: [['Nombre completo', 'text'], ['Especialidad', 'text'], ['Consultorio', 'text']] },
    especialidad: { title: 'Nueva especialidad de muestra', fields: [['Nombre de especialidad', 'text'], ['Descripción', 'text']] },
    consulta: { title: 'Nueva consulta de muestra', fields: [['Nombre del paciente', 'text'], ['Médico', 'text'], ['Fecha y hora', 'datetime-local']] },
    horario: { title: 'Nuevo horario de muestra', fields: [['Nombre del médico', 'text'], ['Días de atención', 'text'], ['Horario', 'text']] },
};

function showToast(message) {
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
}

function activateView(viewName) {
    views.forEach((view) => view.classList.toggle('is-visible', view.id === `admin-view-${viewName}`));
    navItems.forEach((item) => {
        const active = item.dataset.adminView === viewName;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-current', active ? 'page' : 'false');
    });
    const activeView = document.querySelector(`#admin-view-${viewName}`);
    if (activeView) document.title = `${activeView.dataset.viewTitle} | Administración SIGC`;
    applyGlobalSearch();
}

navItems.forEach((item) => item.addEventListener('click', () => activateView(item.dataset.adminView)));
document.querySelectorAll('[data-go-view]').forEach((button) => button.addEventListener('click', () => activateView(button.dataset.goView)));

document.querySelectorAll('[data-local-tab]').forEach((tab) => {
    tab.addEventListener('click', () => {
        const scope = tab.closest('.admin-view');
        scope.querySelectorAll('[data-local-tab]').forEach((item) => item.classList.toggle('is-active', item === tab));
        scope.querySelectorAll('[data-tab-panel]').forEach((panel) => panel.classList.toggle('is-visible', panel.dataset.tabPanel === tab.dataset.localTab));
    });
});

function applyTableFilters(tableName) {
    const table = document.querySelector(`[data-filter-table="${tableName}"]`);
    if (!table) return;
    const query = (document.querySelector(`[data-filter-input="${tableName}"]`)?.value ?? '').trim().toLocaleLowerCase('es');
    const selectedStatus = document.querySelector(`[data-filter-select="${tableName}"]`)?.value ?? 'all';
    const activeStatus = document.querySelector(`[data-status-filter="${tableName}"] .is-active`)?.dataset.status ?? 'all';
    let visibleRows = 0;
    table.querySelectorAll('tbody tr[data-status]').forEach((row) => {
        const visible = row.textContent.toLocaleLowerCase('es').includes(query)
            && row.textContent.toLocaleLowerCase('es').includes(globalQuery)
            && (selectedStatus === 'all' || row.dataset.status === selectedStatus)
            && (activeStatus === 'all' || row.dataset.status === activeStatus);
        row.hidden = !visible;
        if (visible) visibleRows += 1;
    });
    const emptyRow = table.querySelector('.admin-empty-row');
    if (emptyRow) emptyRow.hidden = visibleRows > 0;
}

document.querySelectorAll('[data-filter-input]').forEach((input) => input.addEventListener('input', () => applyTableFilters(input.dataset.filterInput)));
document.querySelectorAll('[data-filter-select]').forEach((select) => select.addEventListener('change', () => applyTableFilters(select.dataset.filterSelect)));
const doctorSearch = document.querySelector('[data-filter-input="doctors"]');
const doctorSpecialty = document.querySelector('[data-doctor-specialty]');

function filterDoctors() {
    const query = doctorSearch.value.trim().toLocaleLowerCase('es');
    const specialty = doctorSpecialty.value;
    let visibleCards = 0;
    document.querySelectorAll('.admin-doctor-card').forEach((card) => {
        const matches = card.textContent.toLocaleLowerCase('es').includes(query)
            && card.textContent.toLocaleLowerCase('es').includes(globalQuery)
            && (specialty === 'all' || card.querySelector('p').textContent === specialty);
        card.hidden = !matches;
        if (matches) visibleCards += 1;
    });
    document.querySelector('.admin-filter-empty').hidden = visibleCards > 0;
}

function applyGlobalSearch() {
    globalQuery = globalSearch.value.trim().toLocaleLowerCase('es');
    const activeView = document.querySelector('.admin-view.is-visible');
    if (!activeView) return;

    if (activeView.id === 'admin-view-pacientes') applyTableFilters('patients');
    if (activeView.id === 'admin-view-consultas') applyTableFilters('appointments');
    if (activeView.id === 'admin-view-equipo') {
        filterDoctors();
        activeView.querySelectorAll('.admin-specialty-card').forEach((card) => {
            card.hidden = !card.textContent.toLocaleLowerCase('es').includes(globalQuery);
        });
    }

    const filterableItems = activeView.querySelectorAll('.admin-timeline-row,.admin-table:not([data-filter-table]) tbody tr,.calendar-event,.admin-week-event');
    filterableItems.forEach((item) => {
        item.hidden = globalQuery.length > 0 && !item.textContent.toLocaleLowerCase('es').includes(globalQuery);
    });
    if (activeView.id === 'admin-view-agenda') applyCalendarDoctorFilter();
}

globalSearch.addEventListener('input', applyGlobalSearch);

doctorSearch.addEventListener('input', filterDoctors);
doctorSpecialty.addEventListener('change', filterDoctors);
document.querySelectorAll('[data-status-filter] button').forEach((button) => {
    button.addEventListener('click', () => {
        const filter = button.closest('[data-status-filter]');
        filter.querySelectorAll('button').forEach((item) => item.classList.toggle('is-active', item === button));
        applyTableFilters(filter.dataset.statusFilter);
    });
});

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        const config = modalConfigs[button.dataset.openModal];
        modalTitle.textContent = config.title;
        modalFields.replaceChildren(...config.fields.map(([label, type]) => {
            const wrapper = document.createElement('label');
            const caption = document.createElement('span');
            const input = document.createElement('input');
            caption.textContent = label;
            input.type = type;
            input.required = true;
            wrapper.append(caption, input);
            return wrapper;
        }));
        modal.showModal();
    });
});

document.querySelector('[data-local-form]').addEventListener('submit', (event) => {
    if (event.submitter?.value !== 'save') return;
    event.preventDefault();
    modal.close();
    showToast('Muestra guardada solo en esta sesión visual. No se envió información.');
});

const calendarGrid = document.querySelector('[data-calendar-grid]');
const calendarWeek = document.querySelector('[data-calendar-week]');
const calendarHeading = document.querySelector('[data-calendar-heading]');
const weekdayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
const weekdayShortNames = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'];
let calendarMode = 'day';

function renderCalendarDate() {
    const date = new Date(2026, 9, 23 + calendarOffset);
    calendarHeading.textContent = `${weekdayNames[date.getDay()]}, ${date.toLocaleDateString('es-MX', { day: 'numeric', month: 'long' })}`;
    document.querySelector('[data-day-label]').innerHTML = `${weekdayNames[date.getDay()].toUpperCase()} <b>${date.getDate()}</b>`;
    document.querySelector('.calendar-month-mark span').textContent = date.toLocaleDateString('es-MX', { month: 'short' }).replace('.', '').toUpperCase();
    document.querySelector('.calendar-month-mark strong').textContent = date.getDate();
    renderCalendarWeek(date);
}

function renderCalendarWeek(selectedDate) {
    const mondayOffset = (selectedDate.getDay() + 6) % 7;
    const monday = new Date(selectedDate);
    monday.setDate(selectedDate.getDate() - mondayOffset);
    const cells = ['<div class="admin-week-head admin-week-corner">HORA</div>'];
    const sampleEvents = {
        0: [['08:00 Elena Vargas · Dra. Andrea Méndez', 'event-green'], ['11:00 Miguel Herrera · Dr. Pablo Ríos', 'event-yellow']],
        2: [['10:00 Camila Ortega · Dra. Lucía Torres', 'event-yellow']],
        4: [['09:00 Elena Vargas · Dra. Andrea Méndez', 'event-green'], ['09:30 Roberto Silva · Dr. Diego Ramírez', 'event-coral'], ['10:00 Camila Ortega · Dra. Lucía Torres', 'event-yellow'], ['11:00 Miguel Herrera · Dr. Pablo Ríos', 'event-green']],
        5: [['10:00 Lucía Navarro · Dra. Lucía Torres', 'event-coral']],
    };

    for (let dayIndex = 0; dayIndex < 7; dayIndex += 1) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + dayIndex);
        const selected = date.toDateString() === selectedDate.toDateString();
        cells.push(`<div class="admin-week-head${selected ? ' is-selected' : ''}">${weekdayShortNames[dayIndex]} <strong>${date.getDate()}</strong></div>`);
    }
    for (let hour = 8; hour <= 16; hour += 1) {
        cells.push(`<time class="admin-week-time">${String(hour).padStart(2, '0')}:00</time>`);
        for (let dayIndex = 0; dayIndex < 7; dayIndex += 1) {
            const events = (sampleEvents[dayIndex] ?? []).filter(([event]) => Number(event.slice(0, 2)) === hour);
            const eventMarkup = events.map(([event, tone]) => `<span class="admin-week-event ${tone}">${event.slice(6)}</span>`).join('');
            cells.push(`<div class="admin-week-cell">${eventMarkup}</div>`);
        }
    }
    calendarWeek.innerHTML = cells.join('');
}

function applyCalendarDoctorFilter() {
    const selectedDoctor = document.querySelector('[data-calendar-doctor-filter]').value;
    document.querySelectorAll('.calendar-event,.admin-week-event').forEach((event) => {
        const matchesDoctor = selectedDoctor === 'all' || event.textContent.includes(selectedDoctor);
        const matchesSearch = event.textContent.toLocaleLowerCase('es').includes(globalQuery);
        event.hidden = !matchesDoctor || !matchesSearch;
    });
}

document.querySelector('[data-calendar-doctor-filter]').addEventListener('change', applyCalendarDoctorFilter);

document.querySelectorAll('[data-calendar-mode]').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelectorAll('[data-calendar-mode]').forEach((item) => item.classList.toggle('is-active', item === button));
        calendarMode = button.dataset.calendarMode;
        calendarGrid.hidden = calendarMode === 'week';
        calendarWeek.hidden = calendarMode !== 'week';
        showToast(button.dataset.calendarMode === 'week' ? 'Vista semanal de muestra.' : 'Vista diaria de muestra.');
    });
});
document.querySelectorAll('[data-calendar-step]').forEach((button) => button.addEventListener('click', () => {
    calendarOffset += Number(button.dataset.calendarStep) * (calendarMode === 'week' ? 7 : 1);
    renderCalendarDate();
    applyCalendarDoctorFilter();
}));
document.querySelector('[data-calendar-today]').addEventListener('click', () => {
    calendarOffset = 0;
    renderCalendarDate();
    showToast('Agenda de hoy, 23 de octubre de 2026.');
});

renderCalendarDate();
applyCalendarDoctorFilter();

document.querySelectorAll('.admin-pagination button:not(:disabled)').forEach((button) => button.addEventListener('click', () => showToast('Paginación visual de demostración.')));
document.querySelectorAll('.admin-row-menu,.admin-more-button,.admin-rail-bottom,.admin-icon-button,.admin-user').forEach((button) => button.addEventListener('click', () => showToast('Acción disponible en la futura integración.')));