const doctorPage = document.querySelector('.doctor-page');

if (doctorPage) {
    const views = [...doctorPage.querySelectorAll('.doctor-view')];
    const navigation = [...doctorPage.querySelectorAll('[data-doctor-view]')];
    const dialog = doctorPage.querySelector('[data-doctor-dialog]');
    const dialogTitle = doctorPage.querySelector('#doctor-dialog-title');
    const recordSummary = doctorPage.querySelector('[data-record-summary]');
    const noteText = doctorPage.querySelector('[data-note-text]');
    const toast = doctorPage.querySelector('.doctor-toast');
    let selectedPatient = '';
    let toastTimer;
    let dayOffset = 0;

    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
    }

    function showView(viewName) {
        views.forEach((view) => view.classList.toggle('is-active', view.id === `doctor-view-${viewName}`));
        navigation.forEach((button) => {
            const active = button.dataset.doctorView === viewName;
            button.classList.toggle('is-active', active);
            if (active) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
        const activeView = document.querySelector(`#doctor-view-${viewName}`);
        if (activeView) document.title = `${activeView.dataset.title} | Consultorio SIGC`;
    }

    navigation.forEach((button) => button.addEventListener('click', () => showView(button.dataset.doctorView)));
    doctorPage.querySelectorAll('[data-doctor-view-link]').forEach((button) => button.addEventListener('click', () => showView(button.dataset.doctorViewLink)));

    const patientSearch = doctorPage.querySelector('[data-patient-search]');
    const patientFilter = doctorPage.querySelector('[data-patient-filter]');

    function filterPatients() {
        const query = patientSearch.value.trim().toLocaleLowerCase('es');
        const filter = patientFilter.value;
        let visibleCount = 0;
        doctorPage.querySelectorAll('[data-patient-row]').forEach((row) => {
            const queryMatch = row.textContent.toLocaleLowerCase('es').includes(query);
            const filterMatch = filter === 'all' || row.dataset[filter === 'follow-up' ? 'followUp' : 'today'] === 'yes';
            row.hidden = !queryMatch || !filterMatch;
            if (!row.hidden) visibleCount += 1;
        });
        doctorPage.querySelector('[data-patient-empty]').hidden = visibleCount > 0;
    }

    patientSearch.addEventListener('input', filterPatients);
    patientFilter.addEventListener('change', filterPatients);

    function openPatientDialog(patientName, mode) {
        selectedPatient = patientName;
        const isNewNote = mode === 'new-note';
        dialogTitle.textContent = isNewNote ? 'Nueva nota clínica' : patientName;
        recordSummary.replaceChildren();
        if (isNewNote) {
            const label = document.createElement('label');
            label.className = 'doctor-note-field';
            const caption = document.createElement('span');
            const select = document.createElement('select');
            caption.textContent = 'Paciente';
            select.setAttribute('data-note-patient', '');
            select.innerHTML = '<option value="">Seleccionar paciente de muestra</option><option>Elena Vargas</option><option>Roberto Silva</option><option>Camila Ortega</option><option>Mariana López</option><option>Lucía Torres</option>';
            select.addEventListener('change', () => { selectedPatient = select.value; });
            label.append(caption, select);
            recordSummary.append(label);
            selectedPatient = '';
            noteText.placeholder = 'Escribe una nota de demostración...';
        } else {
            const detail = document.createElement('p');
            detail.textContent = `Expediente clínico de muestra · ${patientName}. La información mostrada es ficticia y solo está disponible en esta vista.`;
            recordSummary.append(detail);
            noteText.placeholder = `Nota clínica para ${patientName}...`;
        }
        noteText.value = '';
        dialog.showModal();
    }

    doctorPage.querySelectorAll('[data-open-record]').forEach((button) => {
        button.addEventListener('click', () => openPatientDialog(button.dataset.openRecord, 'record'));
    });
    doctorPage.querySelectorAll('[data-open-note]').forEach((button) => {
        button.addEventListener('click', () => openPatientDialog(button.dataset.openNote, 'record'));
    });
    doctorPage.querySelectorAll('[data-doctor-action="new-note"]').forEach((button) => {
        button.addEventListener('click', () => openPatientDialog('', 'new-note'));
    });
    doctorPage.querySelectorAll('[data-close-doctor-dialog]').forEach((button) => button.addEventListener('click', () => dialog.close()));

    doctorPage.querySelector('[data-doctor-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        const patientName = selectedPatient || 'Paciente sin seleccionar';
        dialog.close();
        showToast(`Nota de muestra para ${patientName} guardada solo en esta pantalla.`);
    });

    doctorPage.querySelectorAll('[data-complete-appointment]').forEach((button) => {
        button.addEventListener('click', () => {
            const appointment = button.closest('.doctor-appointment');
            appointment.classList.add('appointment-complete');
            appointment.dataset.appointmentStatus = 'completed';
            const status = appointment.querySelector('.doctor-status');
            status.textContent = 'Atendida';
            status.className = 'doctor-status status-done';
            button.disabled = true;
            button.textContent = 'Completada';
            showToast('Consulta marcada como atendida en esta vista de demostración.');
        });
    });

    doctorPage.querySelectorAll('[data-doctor-day]').forEach((button) => {
        button.addEventListener('click', () => {
            dayOffset += Number(button.dataset.doctorDay);
            const date = new Date(2026, 9, 23 + dayOffset);
            const text = date.toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'short' });
            const heading = doctorPage.querySelector('[data-doctor-date]');
            heading.textContent = text.charAt(0).toLocaleUpperCase('es') + text.slice(1);
        });
    });

    doctorPage.querySelector('[data-doctor-action="hours"]').addEventListener('click', () => {
        showView('horario');
        showToast('Mostrando tu disponibilidad individual de muestra.');
    });
}
