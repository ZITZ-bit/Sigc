const views = [...document.querySelectorAll('.admin-view')];
const navItems = [...document.querySelectorAll('[data-admin-view]')];
const modal = document.querySelector('.admin-modal');
const modalTitle = document.querySelector('#admin-modal-title');
const modalFields = document.querySelector('[data-modal-fields]');
const modalForm = document.querySelector('[data-admin-form]');
const modalMethod = document.querySelector('[data-admin-method]');
const toast = document.querySelector('.admin-toast');
const globalSearch = document.querySelector('.admin-search input');
const formOptions = JSON.parse(document.querySelector('#admin-form-options').textContent);
let toastTimer;
let globalQuery = '';

const modalConfigs = {
    'paciente-invite': {
        title: 'Invitar paciente', action: document.body.dataset.invitePatientUrl, method: 'POST',
        fields: [{ label: 'Correo electrónico', name: 'email', type: 'email' }],
    },
    paciente: {
        title: 'Editar paciente', action: null, method: 'PUT',
        fields: [
            { label: 'Nombre', name: 'name', type: 'text' },
            { label: 'Apellido', name: 'apellido', type: 'text', required: false },
            { label: 'Correo electrónico', name: 'email', type: 'email' },
            { label: 'Teléfono', name: 'telefono', type: 'tel', required: false },
        ],
    },
    medico: {
        title: 'Invitar médico', action: document.body.dataset.inviteDoctorUrl, method: 'POST',
        fields: [
            { label: 'Correo electrónico', name: 'email', type: 'email' },
            { label: 'Clínica', name: 'clinica_id', type: 'select', options: formOptions.clinics },
            { label: 'Especialidad', name: 'especialidad_id', type: 'select', options: formOptions.specialties },
        ],
    },
    especialidad: {
        title: 'Nueva especialidad', action: document.body.dataset.createSpecialtyUrl, method: 'POST',
        fields: [
            { label: 'Nombre', name: 'nombre', type: 'text' },
            { label: 'Descripción', name: 'descripcion', type: 'textarea', required: false },
        ],
    },
    consulta: {
        title: 'Nueva consulta', action: document.body.dataset.createAppointmentUrl, method: 'POST',
        fields: [
            { label: 'Paciente', name: 'paciente_id', type: 'select', options: formOptions.patients },
            { label: 'Médico', name: 'medico_id', type: 'select', options: formOptions.doctors },
            { label: 'Fecha y hora', name: 'fecha_hora', type: 'datetime-local' },
            { label: 'Motivo', name: 'motivo', type: 'text', required: false },
        ],
    },
    horario: {
        title: 'Agregar horario médico', action: document.body.dataset.createScheduleUrl, method: 'POST',
        fields: [
            { label: 'Médico', name: 'medico_id', type: 'select', options: formOptions.doctors },
            { label: 'Día de la semana', name: 'dia_semana', type: 'select', options: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map((label) => ({ id: label, label })) },
            { label: 'Hora inicial', name: 'hora_inicio', type: 'time' },
            { label: 'Hora final', name: 'hora_fin', type: 'time' },
            { label: 'Duración por consulta (minutos)', name: 'duracion_minutos', type: 'number', value: '30', required: false },
        ],
    },
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

function openAdminModal(config, values = {}) {
    modalTitle.textContent = config.title;
    modalForm.action = config.action;
    modalMethod.value = config.method;
    modalFields.replaceChildren(...config.fields.map((field) => {
        const wrapper = document.createElement('label');
        const caption = document.createElement('span');
        caption.textContent = field.label;
        let control;
        if (field.type === 'select') {
            control = document.createElement('select');
            control.add(new Option('Seleccionar...', ''));
            field.options.forEach((option) => control.add(new Option(option.label, option.id)));
        } else if (field.type === 'textarea') {
            control = document.createElement('textarea');
            control.rows = 3;
        } else {
            control = document.createElement('input');
            control.type = field.type;
        }
        control.name = field.name;
        control.required = field.required !== false;
        if (field.value) control.value = field.value;
        if (values[field.name] !== undefined) control.value = values[field.name] ?? '';
        wrapper.append(caption, control);
        return wrapper;
    }));
    modal.showModal();
}

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => openAdminModal(modalConfigs[button.dataset.openModal]));
});

document.querySelectorAll('[data-edit-specialty]').forEach((button) => {
    button.addEventListener('click', () => openAdminModal({
        title: 'Editar especialidad',
        action: `${document.body.dataset.updateSpecialtyUrl}/${button.dataset.editSpecialty}`,
        method: 'PUT',
        fields: [
            { label: 'Nombre', name: 'nombre', type: 'text' },
            { label: 'Descripción', name: 'descripcion', type: 'textarea', required: false },
        ],
    }, { nombre: button.dataset.specialtyName, descripcion: button.dataset.specialtyDescription }));
});

document.querySelectorAll('[data-edit-patient]').forEach((button) => {
    button.addEventListener('click', () => openAdminModal({
        ...modalConfigs.paciente,
        action: `${document.body.dataset.updatePatientUrl}/${button.dataset.editPatient}`,
    }, {
        name: button.dataset.name,
        apellido: button.dataset.surname,
        email: button.dataset.email,
        telefono: button.dataset.phone,
    }));
});

document.querySelectorAll('[data-close-admin-modal]').forEach((button) => button.addEventListener('click', () => modal.close()));

const calendarGrid = document.querySelector('[data-calendar-grid]');
const calendarWeek = document.querySelector('[data-calendar-week]');
const calendarHeading = document.querySelector('[data-calendar-heading]');
const weekdayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
const weekdayShortNames = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'];
const agendaData = JSON.parse(document.querySelector('#admin-agenda-data').textContent);
const agendaDateInitial = new Date(`${document.body.dataset.initialDate}T00:00:00`);
let selectedAgendaDate = new Date(agendaDateInitial);
let calendarMode = 'day';

function localDateKey(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function statusTone(status) {
    if (status === 'pendiente') return 'event-coral';
    if (status === 'completada') return 'event-yellow';
    return 'event-green';
}

function createCalendarEvent(appointment, weekly = false) {
    const event = document.createElement(weekly ? 'span' : 'article');
    event.className = `${weekly ? 'admin-week-event' : 'calendar-event'} ${statusTone(appointment.status)}`;
    event.dataset.doctorId = appointment.doctorId;
    if (weekly) {
        event.textContent = `${appointment.time} ${appointment.patient}`;
        event.title = `${appointment.patient} · ${appointment.doctor} · ${appointment.specialty}`;
    } else {
        const patient = document.createElement('strong');
        patient.textContent = appointment.patient;
        const doctor = document.createElement('span');
        doctor.textContent = `${appointment.doctor} · ${appointment.specialty}`;
        const time = document.createElement('small');
        time.textContent = `${appointment.time} – ${appointment.end} · Consultorio ${appointment.room}`;
        event.append(patient, doctor, time);
    }
    return event;
}

function renderCalendarDay() {
    calendarGrid.replaceChildren();
    const header = document.createElement('div');
    header.className = 'calendar-day-head';
    const timeHeader = document.createElement('span');
    timeHeader.textContent = 'HORA';
    const dayHeader = document.createElement('strong');
    const dayNumber = document.createElement('b');
    dayNumber.textContent = selectedAgendaDate.getDate();
    dayHeader.append(`${weekdayNames[selectedAgendaDate.getDay()].toUpperCase()} `, dayNumber);
    const eventHeader = document.createElement('span');
    eventHeader.textContent = 'CONSULTAS';
    header.append(timeHeader, dayHeader, eventHeader);
    calendarGrid.append(header);

    const dateKey = localDateKey(selectedAgendaDate);
    for (let hour = 8; hour <= 18; hour += 1) {
        const row = document.createElement('div');
        row.className = 'calendar-day-row';
        const label = document.createElement('time');
        label.textContent = `${String(hour).padStart(2, '0')}:00`;
        const events = document.createElement('div');
        agendaData.filter((appointment) => appointment.date === dateKey && Number(appointment.time.slice(0, 2)) === hour)
            .forEach((appointment) => events.append(createCalendarEvent(appointment)));
        row.append(label, events);
        calendarGrid.append(row);
    }
}

function renderCalendarWeek() {
    calendarWeek.replaceChildren();
    const monday = new Date(selectedAgendaDate);
    monday.setDate(selectedAgendaDate.getDate() - ((selectedAgendaDate.getDay() + 6) % 7));
    const corner = document.createElement('div');
    corner.className = 'admin-week-head admin-week-corner';
    corner.textContent = 'HORA';
    calendarWeek.append(corner);

    const weekDates = [];
    for (let dayIndex = 0; dayIndex < 7; dayIndex += 1) {
        const date = new Date(monday);
        date.setDate(monday.getDate() + dayIndex);
        weekDates.push(date);
        const heading = document.createElement('div');
        heading.className = `admin-week-head${localDateKey(date) === localDateKey(selectedAgendaDate) ? ' is-selected' : ''}`;
        const number = document.createElement('strong');
        number.textContent = date.getDate();
        heading.append(`${weekdayShortNames[dayIndex]} `, number);
        calendarWeek.append(heading);
    }

    for (let hour = 8; hour <= 18; hour += 1) {
        const time = document.createElement('time');
        time.className = 'admin-week-time';
        time.textContent = `${String(hour).padStart(2, '0')}:00`;
        calendarWeek.append(time);
        weekDates.forEach((date) => {
            const cell = document.createElement('div');
            cell.className = 'admin-week-cell';
            agendaData.filter((appointment) => appointment.date === localDateKey(date) && Number(appointment.time.slice(0, 2)) === hour)
                .forEach((appointment) => cell.append(createCalendarEvent(appointment, true)));
            calendarWeek.append(cell);
        });
    }
}

function renderCalendarDate() {
    calendarHeading.textContent = `${weekdayNames[selectedAgendaDate.getDay()]}, ${selectedAgendaDate.toLocaleDateString('es-MX', { day: 'numeric', month: 'long' })}`;
    document.querySelector('.calendar-month-mark span').textContent = selectedAgendaDate.toLocaleDateString('es-MX', { month: 'short' }).replace('.', '').toUpperCase();
    document.querySelector('.calendar-month-mark strong').textContent = selectedAgendaDate.getDate();
    renderCalendarDay();
    renderCalendarWeek();
    applyCalendarDoctorFilter();
}

function applyCalendarDoctorFilter() {
    const selectedDoctor = document.querySelector('[data-calendar-doctor-filter]').value;
    document.querySelectorAll('.calendar-event,.admin-week-event').forEach((event) => {
        const matchesDoctor = selectedDoctor === 'all' || event.dataset.doctorId === selectedDoctor;
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
    selectedAgendaDate.setDate(selectedAgendaDate.getDate() + Number(button.dataset.calendarStep) * (calendarMode === 'week' ? 7 : 1));
    renderCalendarDate();
}));
document.querySelector('[data-calendar-today]').addEventListener('click', () => {
    selectedAgendaDate = new Date(agendaDateInitial);
    renderCalendarDate();
    showToast('Mostrando la fecha actual.');
});

renderCalendarDate();
applyCalendarDoctorFilter();

const initialParams = new URLSearchParams(window.location.search);
const requestedView = initialParams.get('view');
if (requestedView && document.querySelector(`#admin-view-${CSS.escape(requestedView)}`)) {
    activateView(requestedView);
    const requestedTab = initialParams.get('tab');
    if (requestedTab) {
        document.querySelector(`#admin-view-${CSS.escape(requestedView)} [data-local-tab="${CSS.escape(requestedTab)}"]`)?.click();
    }
}

document.querySelectorAll('.admin-pagination button:not(:disabled)').forEach((button) => button.addEventListener('click', () => showToast('Paginación visual de demostración.')));
// Las acciones reales del panel están dadas por handlers específicos de cada formulario/modal; no se usan toasts de demo.
