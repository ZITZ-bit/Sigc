<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Administración | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-page">
    <div class="admin-shell">
        <aside class="admin-rail" aria-label="Navegación administrativa">
            <a class="admin-mark" href="#resumen" aria-label="SIGC">S</a>
            <nav class="admin-nav">
                <button class="admin-nav-item is-active" data-admin-view="resumen" title="Resumen" aria-label="Resumen">▦</button>
                <button class="admin-nav-item" data-admin-view="pacientes" title="Pacientes" aria-label="Pacientes">♧</button>
                <button class="admin-nav-item" data-admin-view="equipo" title="Médicos y especialidades" aria-label="Médicos y especialidades">✳</button>
                <button class="admin-nav-item" data-admin-view="consultas" title="Consultas y horarios" aria-label="Consultas y horarios">▤</button>
                <button class="admin-nav-item" data-admin-view="agenda" title="Agenda" aria-label="Agenda">◷</button>
            </nav>
            <button class="admin-rail-bottom" title="Configuración" aria-label="Configuración">⚙</button>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-wordmark"><span class="admin-wordmark-icon">S</span><strong>sigc</strong><span class="admin-topbar-divider"></span><span>Administración</span></div>
                <div class="admin-topbar-actions">
                    <a class="admin-home-link" href="{{ url('/') }}">← Portal principal</a>
                    <label class="admin-search"><span aria-hidden="true">⌕</span><input type="search" placeholder="Buscar en el panel" aria-label="Buscar en el panel"></label>
                    <button class="admin-icon-button" title="Notificaciones" aria-label="Notificaciones">♧<i></i></button>
                    <button class="admin-user"><span class="admin-avatar">MC</span><span><strong>María Cruz</strong><small>Administradora</small></span><span class="admin-user-chevron">⌄</span></button>
                </div>
            </header>
            <main class="admin-content">
                <section class="admin-view is-visible" id="admin-view-resumen" data-view-title="Resumen">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">VIERNES, 23 DE OCTUBRE DE 2026</p><h1>Buenos días, María</h1><p class="admin-subtitle">Aquí tienes el pulso de tu clínica hoy.</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-metrics">
                        <x-admin.metric-card label="Pacientes activos" value="1,284" note="+8.2% este mes" icon="♧" tone="mint" />
                        <x-admin.metric-card label="Consultas de hoy" value="24" note="6 pendientes de confirmar" icon="◷" tone="peach" />
                        <x-admin.metric-card label="Médicos disponibles" value="12" note="En 6 especialidades" icon="✳" tone="yellow" />
                        <x-admin.metric-card label="Tiempo de espera" value="12 min" note="−4 min frente a ayer" icon="↘" tone="lavender" />
                    </div>
                    <div class="admin-overview-grid">
                        <section class="admin-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">AGENDA · HOY</p><h2>Consultas próximas</h2></div><button class="admin-text-button" data-go-view="agenda">Ver agenda <span>→</span></button></div>
                            <div class="admin-timeline">
                                <article class="admin-timeline-row"><time>09:00</time><span class="admin-timeline-line"></span><div class="admin-event event-green"><strong>Elena Vargas</strong><span>Dra. Andrea Méndez · Cardiología</span></div><span class="admin-pill pill-confirmed">Confirmada</span></article>
                                <article class="admin-timeline-row"><time>09:30</time><span class="admin-timeline-line"></span><div class="admin-event event-coral"><strong>Roberto Silva</strong><span>Dr. Diego Ramírez · Dermatología</span></div><span class="admin-pill pill-pending">Pendiente</span></article>
                                <article class="admin-timeline-row"><time>10:00</time><span class="admin-timeline-line"></span><div class="admin-event event-yellow"><strong>Camila Ortega</strong><span>Dra. Lucía Torres · Pediatría</span></div><span class="admin-pill pill-confirmed">Confirmada</span></article>
                                <article class="admin-timeline-row"><time>10:30</time><span class="admin-timeline-line"></span><div class="admin-event event-green"><strong>Miguel Herrera</strong><span>Dr. Pablo Ríos · Medicina general</span></div><span class="admin-pill pill-confirmed">Confirmada</span></article>
                            </div>
                        </section>
                        <section class="admin-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">EQUIPO CLÍNICO</p><h2>En consulta hoy</h2></div><button class="admin-more-button" aria-label="Más opciones">•••</button></div>
                            <div class="admin-duty-list">
                                <div class="admin-duty-person"><span class="admin-doctor-avatar avatar-sage">AM</span><div><strong>Dra. Andrea Méndez</strong><span>Cardiología · Consultorio 03</span></div><i class="duty-dot"></i></div>
                                <div class="admin-duty-person"><span class="admin-doctor-avatar avatar-coral">DR</span><div><strong>Dr. Diego Ramírez</strong><span>Dermatología · Consultorio 07</span></div><i class="duty-dot"></i></div>
                                <div class="admin-duty-person"><span class="admin-doctor-avatar avatar-yellow">LT</span><div><strong>Dra. Lucía Torres</strong><span>Pediatría · Consultorio 02</span></div><i class="duty-dot"></i></div>
                            </div><button class="admin-outline-button admin-wide-button" data-go-view="equipo">Ver equipo completo <span>→</span></button>
                        </section>
                    </div>
                    <section class="admin-panel admin-recent-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">ACTIVIDAD RECIENTE</p><h2>Últimos movimientos</h2></div><button class="admin-text-button" data-go-view="consultas">Ver consultas <span>→</span></button></div>
                        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>PACIENTE</th><th>CONSULTA</th><th>MÉDICO</th><th>FECHA Y HORA</th><th>ESTADO</th></tr></thead><tbody>
                            <tr><td><span class="table-person"><span class="table-avatar">EV</span>Elena Vargas</span></td><td>Seguimiento</td><td>Dra. Andrea Méndez</td><td>Hoy, 09:00</td><td><span class="admin-pill pill-confirmed">Confirmada</span></td></tr>
                            <tr><td><span class="table-person"><span class="table-avatar avatar-blush">RS</span>Roberto Silva</span></td><td>Primera consulta</td><td>Dr. Diego Ramírez</td><td>Hoy, 09:30</td><td><span class="admin-pill pill-pending">Pendiente</span></td></tr>
                            <tr><td><span class="table-person"><span class="table-avatar avatar-lemon">CO</span>Camila Ortega</span></td><td>Control pediátrico</td><td>Dra. Lucía Torres</td><td>Hoy, 10:00</td><td><span class="admin-pill pill-confirmed">Confirmada</span></td></tr>
                        </tbody></table></div>
                    </section>
                </section>

                <section class="admin-view" id="admin-view-pacientes" data-view-title="Pacientes">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">DIRECTORIO CLÍNICO</p><h1>Pacientes</h1><p class="admin-subtitle">Consulta y organiza los registros de tu clínica.</p></div><button class="admin-button admin-button-primary" data-open-modal="paciente"><span>＋</span> Nuevo paciente</button></div>
                    <div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><label class="admin-filter-search"><span>⌕</span><input type="search" data-filter-input="patients" placeholder="Buscar paciente por nombre o correo"></label><select class="admin-select" data-filter-select="patients" aria-label="Filtrar pacientes"><option value="all">Todos los pacientes</option><option value="active">Activos</option><option value="new">Nuevos este mes</option></select></div>
                        <div class="admin-table-wrap"><table class="admin-table" data-filter-table="patients"><thead><tr><th>PACIENTE</th><th>CONTACTO</th><th>ÚLTIMA VISITA</th><th>CONSULTAS</th><th>ESTADO</th><th></th></tr></thead><tbody>
                            <tr data-status="active"><td><span class="table-person"><span class="table-avatar">EV</span><span>Elena Vargas<small>Paciente desde feb. 2024</small></span></span></td><td>elena.vargas@ejemplo.test<br><small>+52 55 0101 0202</small></td><td>16 oct 2026</td><td>12</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Acciones de Elena Vargas">•••</button></td></tr>
                            <tr data-status="active"><td><span class="table-person"><span class="table-avatar avatar-blush">RS</span><span>Roberto Silva<small>Paciente desde jun. 2025</small></span></span></td><td>roberto.silva@ejemplo.test<br><small>+52 55 0202 0303</small></td><td>02 oct 2026</td><td>5</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Acciones de Roberto Silva">•••</button></td></tr>
                            <tr data-status="new"><td><span class="table-person"><span class="table-avatar avatar-lemon">CO</span><span>Camila Ortega<small>Paciente desde oct. 2026</small></span></span></td><td>camila.ortega@ejemplo.test<br><small>+52 55 0303 0404</small></td><td>Sin visitas</td><td>1</td><td><span class="admin-pill pill-pending">Nuevo</span></td><td><button class="admin-row-menu" aria-label="Acciones de Camila Ortega">•••</button></td></tr>
                            <tr data-status="active"><td><span class="table-person"><span class="table-avatar avatar-blue">MH</span><span>Miguel Herrera<small>Paciente desde mar. 2023</small></span></span></td><td>miguel.herrera@ejemplo.test<br><small>+52 55 0404 0505</small></td><td>29 sep 2026</td><td>18</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Acciones de Miguel Herrera">•••</button></td></tr>
                            <tr class="admin-empty-row" hidden><td colspan="6">No hay pacientes que coincidan con la búsqueda.</td></tr>
                        </tbody></table></div><div class="admin-pagination"><span>Mostrando 1–4 de 1,284 pacientes ficticios</span><div><button disabled aria-label="Página anterior">←</button><button class="page-current">1</button><button>2</button><button>3</button><button aria-label="Página siguiente">→</button></div></div>
                    </div>
                </section>

                <section class="admin-view" id="admin-view-equipo" data-view-title="Médicos y especialidades">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">DIRECTORIO Y ÁREAS CLÍNICAS</p><h1>Médicos y especialidades</h1><p class="admin-subtitle">Equipo médico y áreas clínicas de muestra.</p></div><button class="admin-button admin-button-primary" data-open-modal="medico"><span>＋</span> Agregar médico</button></div>
                    <div class="admin-tabbar" role="tablist" aria-label="Secciones del equipo"><button class="admin-tab is-active" data-local-tab="doctors">Médicos <span>12</span></button><button class="admin-tab" data-local-tab="specialties">Especialidades <span>6</span></button></div>
                    <div class="admin-local-panel is-visible" data-tab-panel="doctors"><div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><label class="admin-filter-search"><span>⌕</span><input type="search" data-filter-input="doctors" placeholder="Buscar médico o especialidad"></label><select class="admin-select" data-doctor-specialty><option value="all">Todas las especialidades</option><option>Cardiología</option><option>Dermatología</option><option>Pediatría</option></select></div><div class="admin-doctor-grid">
                        <article class="admin-doctor-card"><div class="admin-doctor-card-top"><span class="admin-doctor-avatar avatar-sage">AM</span><span class="admin-pill pill-confirmed">Disponible</span></div><h3>Dra. Andrea Méndez</h3><p>Cardiología</p><div class="admin-doctor-card-meta"><span>Consultorio 03</span><span>10 años de experiencia</span></div><div class="admin-doctor-card-bottom">Próxima consulta <strong>09:00 · Elena Vargas</strong><button class="admin-row-menu" aria-label="Opciones">•••</button></div></article>
                        <article class="admin-doctor-card"><div class="admin-doctor-card-top"><span class="admin-doctor-avatar avatar-coral">DR</span><span class="admin-pill pill-pending">En consulta</span></div><h3>Dr. Diego Ramírez</h3><p>Dermatología</p><div class="admin-doctor-card-meta"><span>Consultorio 07</span><span>8 años de experiencia</span></div><div class="admin-doctor-card-bottom">Próxima consulta <strong>09:30 · Roberto Silva</strong><button class="admin-row-menu" aria-label="Opciones">•••</button></div></article>
                        <article class="admin-doctor-card"><div class="admin-doctor-card-top"><span class="admin-doctor-avatar avatar-yellow">LT</span><span class="admin-pill pill-confirmed">Disponible</span></div><h3>Dra. Lucía Torres</h3><p>Pediatría</p><div class="admin-doctor-card-meta"><span>Consultorio 02</span><span>12 años de experiencia</span></div><div class="admin-doctor-card-bottom">Próxima consulta <strong>10:00 · Camila Ortega</strong><button class="admin-row-menu" aria-label="Opciones">•••</button></div></article>
                        <article class="admin-doctor-card"><div class="admin-doctor-card-top"><span class="admin-doctor-avatar avatar-blue">PR</span><span class="admin-pill pill-confirmed">Disponible</span></div><h3>Dr. Pablo Ríos</h3><p>Medicina general</p><div class="admin-doctor-card-meta"><span>Consultorio 01</span><span>6 años de experiencia</span></div><div class="admin-doctor-card-bottom">Próxima consulta <strong>10:30 · Miguel Herrera</strong><button class="admin-row-menu" aria-label="Opciones">•••</button></div></article>
                    </div><p class="admin-filter-empty" hidden>No hay médicos que coincidan con la búsqueda.</p></div></div>
                    <div class="admin-local-panel" data-tab-panel="specialties"><div class="admin-panel admin-specialty-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">CATÁLOGO DE SERVICIOS</p><h2>Especialidades disponibles</h2></div><button class="admin-outline-button" data-open-modal="especialidad"><span>＋</span> Nueva especialidad</button></div><div class="admin-specialty-grid">
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-green">♡</span><div><h3>Cardiología</h3><p>3 médicos · 86 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-coral">✳</span><div><h3>Dermatología</h3><p>2 médicos · 54 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-yellow">☼</span><div><h3>Pediatría</h3><p>4 médicos · 112 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-blue">◌</span><div><h3>Ginecología</h3><p>2 médicos · 73 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-lavender">⌁</span><div><h3>Traumatología</h3><p>1 médico · 41 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                        <article class="admin-specialty-card"><span class="specialty-symbol symbol-sage">＋</span><div><h3>Medicina general</h3><p>5 médicos · 218 pacientes</p></div><button class="admin-row-menu" aria-label="Opciones">•••</button></article>
                    </div></div></div>
                </section>

                <section class="admin-view" id="admin-view-consultas" data-view-title="Consultas y horarios">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">OPERACIÓN CLÍNICA</p><h1>Consultas y horarios</h1><p class="admin-subtitle">Solicitudes y disponibilidad de muestra.</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-stat-strip"><div><span>Por confirmar</span><strong>06</strong></div><div><span>Confirmadas hoy</span><strong>18</strong></div><div><span>Completadas</span><strong>09</strong></div><div><span>Canceladas</span><strong>02</strong></div></div>
                    <div class="admin-tabbar" role="tablist" aria-label="Consultas y horarios"><button class="admin-tab is-active" data-local-tab="appointments">Consultas <span>24</span></button><button class="admin-tab" data-local-tab="hours">Horarios por médico</button></div>
                    <div class="admin-local-panel is-visible" data-tab-panel="appointments"><div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><div class="admin-filter-tabs" data-status-filter="appointments"><button class="is-active" data-status="all">Todas</button><button data-status="pending">Pendientes</button><button data-status="confirmed">Confirmadas</button><button data-status="completed">Completadas</button></div><label class="admin-filter-search compact-search"><span>⌕</span><input type="search" data-filter-input="appointments" placeholder="Buscar"></label></div><div class="admin-table-wrap"><table class="admin-table" data-filter-table="appointments"><thead><tr><th>PACIENTE</th><th>MÉDICO Y ÁREA</th><th>FECHA Y HORA</th><th>MOTIVO</th><th>ESTADO</th><th></th></tr></thead><tbody>
                        <tr data-status="confirmed"><td><span class="table-person"><span class="table-avatar">EV</span>Elena Vargas</span></td><td>Dra. Andrea Méndez<small>Cardiología</small></td><td>23 oct · 09:00</td><td>Seguimiento</td><td><span class="admin-pill pill-confirmed">Confirmada</span></td><td><button class="admin-row-menu" aria-label="Acciones">•••</button></td></tr>
                        <tr data-status="pending"><td><span class="table-person"><span class="table-avatar avatar-blush">RS</span>Roberto Silva</span></td><td>Dr. Diego Ramírez<small>Dermatología</small></td><td>23 oct · 09:30</td><td>Primera consulta</td><td><span class="admin-pill pill-pending">Pendiente</span></td><td><button class="admin-row-menu" aria-label="Acciones">•••</button></td></tr>
                        <tr data-status="confirmed"><td><span class="table-person"><span class="table-avatar avatar-lemon">CO</span>Camila Ortega</span></td><td>Dra. Lucía Torres<small>Pediatría</small></td><td>23 oct · 10:00</td><td>Control pediátrico</td><td><span class="admin-pill pill-confirmed">Confirmada</span></td><td><button class="admin-row-menu" aria-label="Acciones">•••</button></td></tr>
                        <tr data-status="completed"><td><span class="table-person"><span class="table-avatar avatar-blue">MH</span>Miguel Herrera</span></td><td>Dr. Pablo Ríos<small>Medicina general</small></td><td>22 oct · 16:30</td><td>Consulta general</td><td><span class="admin-pill pill-completed">Completada</span></td><td><button class="admin-row-menu" aria-label="Acciones">•••</button></td></tr>
                        <tr class="admin-empty-row" hidden><td colspan="6">No hay consultas que coincidan con los filtros.</td></tr>
                    </tbody></table></div><div class="admin-pagination"><span>Mostrando 1–4 de 24 consultas ficticias</span><div><button disabled aria-label="Página anterior">←</button><button class="page-current">1</button><button>2</button><button>3</button><button aria-label="Página siguiente">→</button></div></div></div></div>
                    <div class="admin-local-panel" data-tab-panel="hours"><div class="admin-panel admin-hours-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">DISPONIBILIDAD SEMANAL</p><h2>Horarios por médico</h2></div><button class="admin-outline-button" data-open-modal="horario"><span>＋</span> Agregar horario</button></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>MÉDICO</th><th>DÍAS</th><th>HORARIO</th><th>DURACIÓN</th><th>ESTADO</th><th></th></tr></thead><tbody>
                        <tr><td><span class="table-person"><span class="table-avatar">AM</span>Dra. Andrea Méndez</span><small>Cardiología · Consultorio 03</small></td><td>Lun, Mar, Jue</td><td>08:00 – 14:00</td><td>30 minutos</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Editar horario">•••</button></td></tr>
                        <tr><td><span class="table-person"><span class="table-avatar avatar-blush">DR</span>Dr. Diego Ramírez</span><small>Dermatología · Consultorio 07</small></td><td>Lun – Vie</td><td>09:00 – 17:00</td><td>45 minutos</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Editar horario">•••</button></td></tr>
                        <tr><td><span class="table-person"><span class="table-avatar avatar-lemon">LT</span>Dra. Lucía Torres</span><small>Pediatría · Consultorio 02</small></td><td>Mar – Sáb</td><td>08:30 – 15:30</td><td>30 minutos</td><td><span class="admin-pill pill-confirmed">Activo</span></td><td><button class="admin-row-menu" aria-label="Editar horario">•••</button></td></tr>
                    </tbody></table></div></div></div>
                </section>

                <section class="admin-view" id="admin-view-agenda" data-view-title="Agenda">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">AGENDA CLÍNICA</p><h1>Agenda de consultas</h1><p class="admin-subtitle">Vista de equipo · horarios y citas ficticias</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-panel admin-calendar-panel">
                        <div class="admin-calendar-toolbar"><div class="admin-calendar-date"><div class="calendar-month-mark"><span>OCT</span><strong>23</strong></div><div><h2 data-calendar-heading>Viernes, 23 de octubre</h2><span>Semana 43 · 2026</span></div></div><div class="admin-calendar-controls"><button class="calendar-arrow" data-calendar-step="-1" aria-label="Periodo anterior">‹</button><button class="admin-today-button" data-calendar-today>Hoy</button><button class="calendar-arrow" data-calendar-step="1" aria-label="Periodo siguiente">›</button><div class="admin-segmented" role="group" aria-label="Vista de agenda"><button class="is-active" data-calendar-mode="day">Día</button><button data-calendar-mode="week">Semana</button></div></div></div>
                        <div class="admin-calendar-legend"><span><i class="legend-dot dot-green"></i>Confirmada</span><span><i class="legend-dot dot-coral"></i>Pendiente</span><span><i class="legend-dot dot-yellow"></i>Seguimiento</span><label><select class="admin-select" data-calendar-doctor-filter aria-label="Filtrar agenda por médico"><option value="all">Todos los médicos</option><option value="Dra. Andrea Méndez">Dra. Andrea Méndez</option><option value="Dr. Diego Ramírez">Dr. Diego Ramírez</option><option value="Dra. Lucía Torres">Dra. Lucía Torres</option><option value="Dr. Pablo Ríos">Dr. Pablo Ríos</option></select></label></div>
                        <div class="admin-calendar-scroll"><div class="admin-calendar admin-calendar-day" data-calendar-grid><div class="calendar-day-head"><span>HORA</span><strong data-day-label>VIERNES <b>23</b></strong><span>CONSULTAS</span></div>
                            <div class="calendar-day-row"><time>08:00</time><div></div></div><div class="calendar-day-row"><time>09:00</time><div><article class="calendar-event event-green"><strong>Elena Vargas</strong><span>Dra. Andrea Méndez · Cardiología</span><small>09:00 – 09:30 · Consultorio 03</small></article><article class="calendar-event event-coral"><strong>Roberto Silva</strong><span>Dr. Diego Ramírez · Dermatología</span><small>09:30 – 10:15 · Consultorio 07</small></article></div></div>
                            <div class="calendar-day-row"><time>10:00</time><div><article class="calendar-event event-yellow"><strong>Camila Ortega</strong><span>Dra. Lucía Torres · Pediatría</span><small>10:00 – 10:30 · Consultorio 02</small></article></div></div><div class="calendar-day-row"><time>11:00</time><div><article class="calendar-event event-green"><strong>Miguel Herrera</strong><span>Dr. Pablo Ríos · Medicina general</span><small>11:00 – 11:30 · Consultorio 01</small></article></div></div>
                            <div class="calendar-day-row"><time>12:00</time><div></div></div><div class="calendar-day-row"><time>13:00</time><div></div></div><div class="calendar-day-row"><time>14:00</time><div></div></div><div class="calendar-day-row"><time>15:00</time><div></div></div><div class="calendar-day-row"><time>16:00</time><div></div></div>
                        </div><div class="admin-calendar admin-calendar-week" data-calendar-week hidden></div></div><div class="admin-calendar-footer"><span>4 consultas programadas</span><i class="duty-dot"></i><span>3 médicos en consulta</span><button class="admin-text-button" data-go-view="consultas">Administrar consultas <span>→</span></button></div>
                    </div>
                </section>
                <footer class="admin-footer"><span>Datos de demostración · No conectados a registros reales</span><span>SIGC · Gestión clínica</span></footer>
            </main>
        </div>
    </div>
    <dialog class="admin-modal" aria-labelledby="admin-modal-title"><form method="dialog" class="admin-modal-card" data-local-form><button class="admin-modal-close" value="cancel" aria-label="Cerrar">×</button><p class="admin-eyebrow">FORMULARIO DE DEMOSTRACIÓN</p><h2 id="admin-modal-title">Nuevo registro</h2><p class="admin-modal-description">Los datos solo se muestran en esta interfaz y no se guardan.</p><div class="admin-modal-fields" data-modal-fields></div><div class="admin-modal-actions"><button class="admin-outline-button" value="cancel">Cancelar</button><button class="admin-button admin-button-primary" value="save">Guardar muestra</button></div></form></dialog>
    <div class="admin-toast" role="status" aria-live="polite"></div>
</body>
</html>