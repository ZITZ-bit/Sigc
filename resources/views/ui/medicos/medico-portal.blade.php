<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Consultorio | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/doctor.css', 'resources/js/doctor.js'])
</head>
<body class="doctor-page">
    <div class="doctor-shell">
        <aside class="doctor-rail" aria-label="Navegación del consultorio">
            <a class="doctor-mark" href="{{ url('/') }}" aria-label="Volver al portal principal">S</a>
            <nav class="doctor-nav" aria-label="Secciones médicas">
                <button class="doctor-nav-item is-active" type="button" data-doctor-view="agenda" aria-label="Mi agenda" title="Mi agenda">◷</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="pacientes" aria-label="Mis pacientes" title="Mis pacientes">♧</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="notas" aria-label="Notas clínicas" title="Notas clínicas">▤</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="horario" aria-label="Mi disponibilidad" title="Mi disponibilidad">◫</button>
            </nav>
            <a class="doctor-home-link" href="{{ url('/') }}" aria-label="Volver al portal principal" title="Portal principal"><span>⌂</span><small>Portal</small></a>
        </aside>

        <main class="doctor-main">
            <header class="doctor-topbar">
                <div class="doctor-brand"><span class="doctor-brand-mark">S</span><strong>SIGC</strong><span class="doctor-divider"></span><span>Consultorio médico</span></div>
                <div class="doctor-topbar-tools"><a class="doctor-return-link" href="{{ url('/') }}">← Portal principal</a><span class="doctor-account"><span class="doctor-avatar doctor-avatar-sage">AM</span><span><strong>Dra. Andrea Méndez</strong><small>Cardiología</small></span></span></div>
            </header>

            <div class="doctor-content">
                <section class="doctor-view is-active" id="doctor-view-agenda" data-title="Mi agenda">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">VIERNES, 23 DE OCTUBRE DE 2026</p><h1>Tu jornada, Andrea</h1><p class="doctor-subtitle">Agenda y seguimiento de tus pacientes.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <div class="doctor-metrics">
                        <x-doctor.metric-card label="Consultas de hoy" value="8" note="Primera cita a las 09:00" tone="mint" />
                        <x-doctor.metric-card label="Por atender" value="3" note="Siguiente: 09:30" tone="peach" />
                        <x-doctor.metric-card label="Notas pendientes" value="2" note="Expedientes por completar" tone="yellow" />
                    </div>
                    <div class="doctor-work-grid">
                        <section class="doctor-panel doctor-appointments-panel">
                            <div class="doctor-panel-heading"><div><p class="doctor-eyebrow">AGENDA PERSONAL</p><h2>Consultas de hoy</h2></div><div class="doctor-day-selector"><button type="button" data-doctor-day="-1" aria-label="Día anterior">‹</button><span data-doctor-date>Viernes, 23 oct</span><button type="button" data-doctor-day="1" aria-label="Día siguiente">›</button></div></div>
                            <div class="doctor-appointment-list">
                                <article class="doctor-appointment" data-appointment-status="upcoming" data-patient="Elena Vargas">
                                    <time>09:00</time><span class="doctor-time-line"></span><div class="doctor-appointment-person"><span class="doctor-avatar doctor-avatar-peach">EV</span><div><strong>Elena Vargas</strong><span>Seguimiento · Consultorio 03</span></div></div><span class="doctor-status status-confirmed">Confirmada</span><button class="doctor-row-action" type="button" data-open-record="Elena Vargas">Expediente</button><button class="doctor-complete-action" type="button" data-complete-appointment>Atendida</button>
                                </article>
                                <article class="doctor-appointment" data-appointment-status="upcoming" data-patient="Roberto Silva">
                                    <time>09:30</time><span class="doctor-time-line"></span><div class="doctor-appointment-person"><span class="doctor-avatar doctor-avatar-blue">RS</span><div><strong>Roberto Silva</strong><span>Primera consulta · Consultorio 03</span></div></div><span class="doctor-status status-confirmed">Confirmada</span><button class="doctor-row-action" type="button" data-open-record="Roberto Silva">Expediente</button><button class="doctor-complete-action" type="button" data-complete-appointment>Atendida</button>
                                </article>
                                <article class="doctor-appointment" data-appointment-status="upcoming" data-patient="Camila Ortega">
                                    <time>10:00</time><span class="doctor-time-line"></span><div class="doctor-appointment-person"><span class="doctor-avatar doctor-avatar-yellow">CO</span><div><strong>Camila Ortega</strong><span>Evaluación · Consultorio 03</span></div></div><span class="doctor-status status-pending">Pendiente</span><button class="doctor-row-action" type="button" data-open-record="Camila Ortega">Expediente</button><button class="doctor-complete-action" type="button" data-complete-appointment>Atendida</button>
                                </article>
                                <article class="doctor-appointment appointment-complete" data-appointment-status="completed" data-patient="Mariana López">
                                    <time>08:00</time><span class="doctor-time-line"></span><div class="doctor-appointment-person"><span class="doctor-avatar doctor-avatar-sage">ML</span><div><strong>Mariana López</strong><span>Consulta inicial · Consultorio 03</span></div></div><span class="doctor-status status-done">Atendida</span><button class="doctor-row-action" type="button" data-open-record="Mariana López">Ver nota</button><button class="doctor-complete-action" type="button" data-complete-appointment disabled>Completada</button>
                                </article>
                            </div>
                        </section>
                        <aside class="doctor-panel doctor-focus-panel">
                            <div class="doctor-panel-heading"><div><p class="doctor-eyebrow">SIGUIENTE PACIENTE</p><h2>Elena Vargas</h2></div><span class="doctor-next-mark">09:00</span></div>
                            <div class="doctor-focus-detail"><span class="doctor-avatar doctor-avatar-peach doctor-avatar-large">EV</span><div><strong>Seguimiento cardiológico</strong><span>Consultorio 03 · 30 min</span></div></div>
                            <div class="doctor-note-preview"><span>ÚLTIMA NOTA</span><p>Control de presión arterial. Refiere buena adherencia al tratamiento y ausencia de síntomas recientes.</p><small>16 oct 2026 · Consulta de seguimiento</small></div>
                            <button class="doctor-secondary-button" type="button" data-open-record="Elena Vargas">Abrir expediente <span>→</span></button>
                        </aside>
                    </div>
                    <section class="doctor-panel doctor-notes-strip"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">PARA CERRAR HOY</p><h2>Notas clínicas pendientes</h2></div><button class="doctor-link-button" type="button" data-doctor-view-link="notas">Ver notas <span>→</span></button></div><div class="doctor-pending-note"><span class="doctor-note-dot"></span><span><strong>Roberto Silva</strong><small>Primera consulta · Borrador pendiente</small></span><button type="button" data-open-note="Roberto Silva">Continuar nota</button></div><div class="doctor-pending-note"><span class="doctor-note-dot note-dot-yellow"></span><span><strong>Camila Ortega</strong><small>Evaluación · Nota sin firmar</small></span><button type="button" data-open-note="Camila Ortega">Completar nota</button></div></section>
                </section>

                <section class="doctor-view" id="doctor-view-pacientes" data-title="Mis pacientes">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">SEGUIMIENTO CLÍNICO</p><h1>Mis pacientes</h1><p class="doctor-subtitle">Pacientes atendidos por tu consultorio.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <section class="doctor-panel doctor-directory-panel"><div class="doctor-list-toolbar"><label class="doctor-search"><span>⌕</span><input type="search" data-patient-search placeholder="Buscar en mis pacientes" aria-label="Buscar en mis pacientes"></label><select data-patient-filter aria-label="Filtrar pacientes"><option value="all">Todos</option><option value="follow-up">Con seguimiento</option><option value="today">Con cita hoy</option></select></div>
                        <div class="doctor-table-wrap"><table class="doctor-table"><thead><tr><th>PACIENTE</th><th>ÚLTIMA CONSULTA</th><th>PRÓXIMA CITA</th><th>SEGUIMIENTO</th><th></th></tr></thead><tbody>
                            <tr data-patient-row data-follow-up="yes" data-today="yes"><td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-peach">EV</span><span>Elena Vargas<small>Expediente · SIGC-1048</small></span></span></td><td>16 oct 2026</td><td>Hoy · 09:00</td><td><span class="doctor-status status-confirmed">En control</span></td><td><button class="doctor-row-action" type="button" data-open-record="Elena Vargas">Expediente</button></td></tr>
                            <tr data-patient-row data-follow-up="yes" data-today="yes"><td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-blue">RS</span><span>Roberto Silva<small>Expediente · SIGC-1120</small></span></span></td><td>Primera consulta</td><td>Hoy · 09:30</td><td><span class="doctor-status status-pending">Nota pendiente</span></td><td><button class="doctor-row-action" type="button" data-open-record="Roberto Silva">Expediente</button></td></tr>
                            <tr data-patient-row data-follow-up="yes" data-today="yes"><td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-yellow">CO</span><span>Camila Ortega<small>Expediente · SIGC-1192</small></span></span></td><td>09 oct 2026</td><td>Hoy · 10:00</td><td><span class="doctor-status status-confirmed">Evaluación</span></td><td><button class="doctor-row-action" type="button" data-open-record="Camila Ortega">Expediente</button></td></tr>
                            <tr data-patient-row data-follow-up="no" data-today="no"><td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-sage">ML</span><span>Mariana López<small>Expediente · SIGC-0985</small></span></span></td><td>Hoy · 08:00</td><td>Sin cita próxima</td><td><span class="doctor-status status-done">Estable</span></td><td><button class="doctor-row-action" type="button" data-open-record="Mariana López">Expediente</button></td></tr>
                            <tr data-patient-row data-follow-up="yes" data-today="no"><td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-lilac">LT</span><span>Lucía Torres<small>Expediente · SIGC-0831</small></span></span></td><td>02 oct 2026</td><td>30 oct · 11:30</td><td><span class="doctor-status status-confirmed">En control</span></td><td><button class="doctor-row-action" type="button" data-open-record="Lucía Torres">Expediente</button></td></tr>
                            <tr data-patient-empty hidden><td colspan="5">No hay pacientes que coincidan con el filtro.</td></tr>
                        </tbody></table></div>
                    </section>
                </section>

                <section class="doctor-view" id="doctor-view-notas" data-title="Notas clínicas">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">DOCUMENTACIÓN DE CONSULTA</p><h1>Notas clínicas</h1><p class="doctor-subtitle">Borradores y notas recientes de tu consultorio.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <section class="doctor-panel doctor-notes-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">BORRADORES</p><h2>Por completar</h2></div><span class="doctor-count">2 pendientes</span></div>
                        <article class="doctor-clinical-note"><span class="doctor-avatar doctor-avatar-blue">RS</span><div><strong>Roberto Silva</strong><p>Primera consulta · Hoy, 09:30</p><small>Motivo y antecedentes por completar</small></div><button type="button" class="doctor-primary-button" data-open-note="Roberto Silva">Continuar nota</button></article>
                        <article class="doctor-clinical-note"><span class="doctor-avatar doctor-avatar-yellow">CO</span><div><strong>Camila Ortega</strong><p>Evaluación · Hoy, 10:00</p><small>Nota clínica pendiente de firma</small></div><button type="button" class="doctor-secondary-button" data-open-note="Camila Ortega">Completar nota</button></article>
                    </section>
                    <section class="doctor-panel doctor-notes-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">REGISTRO RECIENTE</p><h2>Notas guardadas de muestra</h2></div></div>
                        <article class="doctor-saved-note"><div><strong>Elena Vargas</strong><span>16 oct 2026 · Seguimiento cardiológico</span></div><p>Control de presión arterial. Buena adherencia al tratamiento, sin síntomas recientes.</p><button type="button" class="doctor-link-button" data-open-record="Elena Vargas">Ver expediente <span>→</span></button></article>
                        <article class="doctor-saved-note"><div><strong>Mariana López</strong><span>23 oct 2026 · Consulta inicial</span></div><p>Evaluación general completada. Indicaciones y seguimiento registrados en el expediente de muestra.</p><button type="button" class="doctor-link-button" data-open-record="Mariana López">Ver expediente <span>→</span></button></article>
                    </section>
                </section>

                <section class="doctor-view" id="doctor-view-horario" data-title="Mi disponibilidad">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">HORARIO INDIVIDUAL</p><h1>Mi disponibilidad</h1><p class="doctor-subtitle">Turnos de consulta del consultorio de Cardiología.</p></div><button class="doctor-secondary-button" type="button" data-doctor-action="hours">Editar disponibilidad <span>↗</span></button></div>
                    <section class="doctor-panel doctor-hours-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">SEMANA ACTUAL</p><h2>Horario de atención</h2></div><span class="doctor-status status-confirmed">Vigente · Muestra</span></div><div class="doctor-hours-grid">
                        <div><strong>Lunes</strong><span>08:00 – 14:00</span><small>6 bloques disponibles</small></div><div><strong>Martes</strong><span>08:00 – 14:00</span><small>4 bloques disponibles</small></div><div><strong>Miércoles</strong><span>No disponible</span><small>Sin consultas</small></div><div class="doctor-today"><strong>Jueves</strong><span>09:00 – 15:00</span><small>3 consultas programadas</small></div><div><strong>Viernes</strong><span>08:00 – 13:00</span><small>2 bloques disponibles</small></div><div><strong>Sábado</strong><span>No disponible</span><small>Sin consultas</small></div><div><strong>Domingo</strong><span>No disponible</span><small>Sin consultas</small></div>
                    </div></section>
                    <section class="doctor-panel doctor-room-panel"><p class="doctor-eyebrow">TU CONSULTORIO</p><h2>Dra. Andrea Méndez</h2><p>Cardiología · Consultorio 03</p><div><span>Duración habitual</span><strong>30 minutos</strong></div><div><span>Horario de hoy</span><strong>09:00 – 15:00</strong></div></section>
                </section>
                <footer class="doctor-footer"><span>Vista médica de demostración · Datos ficticios</span><span>SIGC · Consultorio</span></footer>
            </div>
        </main>
    </div>

    <dialog class="doctor-dialog" data-doctor-dialog aria-labelledby="doctor-dialog-title">
        <form method="dialog" class="doctor-dialog-card" data-doctor-form>
            <button type="button" class="doctor-dialog-close" data-close-doctor-dialog aria-label="Cerrar">×</button>
            <p class="doctor-eyebrow">EXPEDIENTE DE MUESTRA</p><h2 id="doctor-dialog-title">Paciente</h2>
            <div class="doctor-record-summary" data-record-summary></div>
            <label class="doctor-note-field"><span>Nota clínica</span><textarea rows="5" data-note-text placeholder="Escribe una nota de demostración para este expediente..."></textarea></label>
            <div class="doctor-dialog-actions"><button type="button" class="doctor-secondary-button" data-close-doctor-dialog>Cancelar</button><button type="submit" class="doctor-primary-button" value="save">Guardar nota local</button></div>
        </form>
    </dialog>
    <div class="doctor-toast" role="status" aria-live="polite"></div>
</body>
</html>
