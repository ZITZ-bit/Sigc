const doctorPage = document.querySelector('.doctor-page');

if (doctorPage) {
    const views = [...doctorPage.querySelectorAll('.doctor-view')];
    const navigation = [...doctorPage.querySelectorAll('[data-doctor-view]')];
    const dialog = doctorPage.querySelector('[data-doctor-dialog]');
    const dialogTitle = doctorPage.querySelector('#doctor-dialog-title');
    const toast = doctorPage.querySelector('.doctor-toast');
    let toastTimer;

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

    // Búsqueda y filtro sobre la tabla real de pacientes
    const patientSearch = doctorPage.querySelector('[data-patient-search]');
    const patientFilter = doctorPage.querySelector('[data-patient-filter]');

    function filterPatients() {
        const query = patientSearch.value.trim().toLocaleLowerCase('es');
        const filter = patientFilter.value;
        let visibleCount = 0;
        doctorPage.querySelectorAll('[data-patient-row]').forEach((row) => {
            const queryMatch = row.textContent.toLocaleLowerCase('es').includes(query);
            const today = row.dataset.today === 'yes';
            const pending = row.dataset.pending === 'yes';
            let filterMatch = true;
            if (filter === 'today') filterMatch = today;
            if (filter === 'pending') filterMatch = pending;
            const visible = queryMatch && filterMatch;
            row.hidden = !visible;
            if (visible) visibleCount += 1;
        });
        doctorPage.querySelector('[data-patient-empty]').hidden = visibleCount > 0;
    }

    patientSearch.addEventListener('input', filterPatients);
    patientFilter.addEventListener('change', filterPatients);

    // Dialogo de nueva nota clinica (envia a ruta real: medico.nota.crear)
    doctorPage.querySelectorAll('[data-doctor-action="new-note"]').forEach((button) => {
        button.addEventListener('click', () => {
            dialogTitle.textContent = 'Registrar nota clínica';
            dialog.showModal();
        });
    });
    doctorPage.querySelectorAll('[data-close-doctor-dialog]').forEach((button) => button.addEventListener('click', () => dialog.close()));
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });

    // Apertura de expediente (accion de interfaz sobre datos reales ya renderizados)
    doctorPage.querySelectorAll('[data-open-record]').forEach((button) => {
        button.addEventListener('click', () => {
            showToast('Expediente de ' + button.dataset.openRecord);
        });
    });
    doctorPage.querySelectorAll('[data-open-note]').forEach((button) => {
        button.addEventListener('click', () => {
            showToast('Nota de ' + button.dataset.openNote);
        });
    });
}
