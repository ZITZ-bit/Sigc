<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Panel | Paciente | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/components.css', 'resources/css/portal.css', 'resources/js/pacientes.js'])
</head>
<body class="patient-page">
    <main class="section-shell">
        <section class="dashboard-window" aria-label="Panel del paciente">
            <div class="dashboard-content">
                <aside class="dashboard-sidebar" aria-label="Secciones del paciente">
                    <button class="side-icon side-icon-active" type="button" data-view="perfil" title="Perfil" aria-label="Perfil" aria-current="page">▦</button>
                    <button class="side-icon" type="button" data-view="medicos" title="Médicos" aria-label="Médicos">⌕</button>
                    <button class="side-icon" type="button" data-view="horarios" title="Horarios" aria-label="Horarios">◷</button>
                    <button class="side-icon" type="button" data-view="consultas" title="Consultas" aria-label="Consultas">♡</button>
                    <button class="side-icon" type="button" data-view="notificaciones" title="Notificaciones" aria-label="Notificaciones">▤</button>
                    <a class="patient-home-link" href="{{ url('/') }}" title="Volver al portal principal" aria-label="Volver al portal principal"><span aria-hidden="true">⌂</span><small>Portal</small></a>
                </aside>

                <div class="panel-body">
                    <!-- Perfil / datos personales -->
                    <section class="panel-view active" id="view-perfil">
                        <div class="panel-header">
                            <p class="dashboard-kicker">DATOS PERSONALES</p>
                            <h2>Mi perfil</h2>
                        </div>
                        <div class="profile-layout">
                            <div class="profile-summary">
                                <div class="avatar avatar-large">JP</div>
                                <h3>Juan Pérez</h3>
                                <p>Paciente desde 2025</p>
                                <div class="patient-divider"></div>
                                <div class="detail-row"><span>Correo</span><strong>juan@correo.com</strong></div>
                                <div class="detail-row"><span>Teléfono</span><strong>+52 55 0000 0000</strong></div>
                                <div class="detail-row"><span>Notificaciones</span><strong>Activas</strong></div>
                                <div class="patient-status"><span></span> Datos actualizados</div>
                                <button class="button button-primary profile-edit">Editar mis datos <span aria-hidden="true">→</span></button>
                            </div>
                            <form class="profile-form" data-profile-form>
                                <p class="dashboard-kicker">ACTUALIZAR INFORMACIÓN</p>
                                <h3>Editar datos personales</h3>
                                <div class="auth-field-row">
                                    <label class="auth-field"><span>Nombre</span><input type="text" value="Juan" required></label>
                                    <label class="auth-field"><span>Apellido</span><input type="text" value="Pérez" required></label>
                                </div>
                                <label class="auth-field"><span>Correo electrónico</span><input type="email" value="juan@correo.com" required></label>
                                <label class="auth-field"><span>Teléfono</span><input type="tel" value="+52 55 0000 0000"></label>
                                <label class="auth-field"><span>Fecha de nacimiento</span><input type="date"></label>
                                <label class="auth-field"><span>Tipo de sangre</span><select><option>O+</option><option>A+</option><option>B+</option><option>AB+</option><option>O-</option><option>A-</option><option>B-</option><option>AB-</option></select></label>
                                <button class="button button-primary" type="submit">Guardar cambios <span aria-hidden="true">→</span></button>
                            </form>
                        </div>
                    </section>

                    <!-- Médicos por especialidad -->
                    <section class="panel-view" id="view-medicos">
                        <div class="panel-header">
                            <p class="dashboard-kicker">BÚSQUEDA DE ESPECIALISTAS</p>
                            <h2>Médicos por especialidad</h2>
                        </div>
                        <label class="search-field">
                            <span>⌕</span>
                            <input type="search" data-doctor-search placeholder="Buscar por especialidad o nombre del médico..." aria-label="Buscar médicos por nombre o especialidad">
                        </label>
                        <p class="eyebrow" style="margin:22px 0 8px;">ESPECIALIDADES DISPONIBLES</p>
                        <div class="specialty-grid">
                            <button class="specialty-chip" type="button" data-specialty-filter="Cardiología">
                                <span>Cardiología</span><strong>3 médicos</strong>
                            </button>
                            <button class="specialty-chip" type="button" data-specialty-filter="Dermatología">
                                <span>Dermatología</span><strong>2 médicos</strong>
                            </button>
                            <button class="specialty-chip" type="button" data-specialty-filter="Pediatría">
                                <span>Pediatría</span><strong>4 médicos</strong>
                            </button>
                            <button class="specialty-chip" type="button" data-specialty-filter="Ginecología">
                                <span>Ginecología</span><strong>2 médicos</strong>
                            </button>
                            <button class="specialty-chip" type="button" data-specialty-filter="Traumatología">
                                <span>Traumatología</span><strong>1 médico</strong>
                            </button>
                            <button class="specialty-chip" type="button" data-specialty-filter="Medicina general">
                                <span>Medicina general</span><strong>5 médicos</strong>
                            </button>
                        </div>
                        <button class="patient-clear-filter" type="button" data-clear-doctor-filter hidden>Limpiar filtro</button>
                        <p class="eyebrow" style="margin:30px 0 8px;">RESULTADOS</p>
                        <div class="doctors-list">
                            <article class="doctor-card" data-doctor="Dra. Andrea Méndez" data-specialty="Cardiología">
                                <div class="doctor-avatar">AM</div>
                                <div>
                                    <div class="doctor-meta"><strong>Dra. Andrea Méndez</strong><span>Cardiología · 10 años</span></div>
                                    <div class="doctor-rating"><span>★ 4.9</span><span>(24 reseñas)</span></div>
                                </div>
                                <button class="button button-primary doctor-btn" type="button" data-doctor-schedule="Dra. Andrea Méndez">Ver horario</button>
                            </article>
                            <article class="doctor-card" data-doctor="Dr. Diego Ramírez" data-specialty="Dermatología">
                                <div class="doctor-avatar">DR</div>
                                <div>
                                    <div class="doctor-meta"><strong>Dr. Diego Ramírez</strong><span>Dermatología · 8 años</span></div>
                                    <div class="doctor-rating"><span>★ 4.7</span><span>(18 reseñas)</span></div>
                                </div>
                                <button class="button button-primary doctor-btn" type="button" data-doctor-schedule="Dr. Diego Ramírez">Ver horario</button>
                            </article>
                            <article class="doctor-card" data-doctor="Dra. Lucía Torres" data-specialty="Pediatría">
                                <div class="doctor-avatar">LT</div>
                                <div>
                                    <div class="doctor-meta"><strong>Dra. Lucía Torres</strong><span>Pediatría · 12 años</span></div>
                                    <div class="doctor-rating"><span>★ 4.8</span><span>(31 reseñas)</span></div>
                                </div>
                                <button class="button button-primary doctor-btn" type="button" data-doctor-schedule="Dra. Lucía Torres">Ver horario</button>
                            </article>
                        </div>
                        <p class="patient-empty-state" data-doctor-empty hidden>No hay médicos para esta búsqueda.</p>
                    </section>

                    <!-- Horarios disponibles -->
                    <section class="panel-view" id="view-horarios">
                        <div class="panel-header">
                            <p class="dashboard-kicker">DISPONIBILIDAD</p>
                            <h2>Horarios disponibles</h2>
                        </div>
                        <div class="schedule-date"><button class="schedule-arrow" type="button" data-date-step="-1" aria-label="Día anterior">‹</button><strong data-schedule-date>Jueves, 15 oct</strong><button class="schedule-arrow" type="button" data-date-step="1" aria-label="Día siguiente">›</button><span class="view-toggle" role="group" aria-label="Vista de horarios"><button type="button" class="is-active" data-schedule-mode="day">Día</button><button type="button" data-schedule-mode="week">Semana</button></span></div>
                        <div class="calendar-grid">
                            <div class="time-column"><span>8:00</span><span>9:00</span><span>10:00</span><span>11:00</span><span>12:00</span><span>13:00</span><span>14:00</span><span>15:00</span><span>16:00</span></div>
                            <div class="appointments">
                                <div class="slot slot-available" data-doctor="Dra. Andrea Méndez"><span>Dra. Andrea Méndez</span><small>Cardiología · Consulta inicial</small><button type="button" data-reserve>Reservar</button></div>
                                <div class="slot slot-taken" data-doctor="Dr. Diego Ramírez"><span>Dr. Diego Ramírez</span><small>Dermatología · Seguimiento</small><button type="button" disabled>Ocupado</button></div>
                                <div class="slot slot-available" data-doctor="Dra. Lucía Torres"><span>Dra. Lucía Torres</span><small>Pediatría · Control</small><button type="button" data-reserve>Reservar</button></div>
                                <div class="slot slot-available" data-doctor="Dra. Andrea Méndez"><span>Dra. Andrea Méndez</span><small>Cardiología · Consulta inicial</small><button type="button" data-reserve>Reservar</button></div>
                                <div class="slot slot-taken" data-doctor="Dr. Diego Ramírez"><span>Dr. Diego Ramírez</span><small>Dermatología · Seguimiento</small><button type="button" disabled>Ocupado</button></div>
                                <div class="slot slot-available" data-doctor="Dra. Lucía Torres"><span>Dra. Lucía Torres</span><small>Pediatría · Control</small><button type="button" data-reserve>Reservar</button></div>
                                <div class="slot slot-available" data-doctor="Dra. Andrea Méndez"><span>Dra. Andrea Méndez</span><small>Cardiología · Consulta inicial</small><button type="button" data-reserve>Reservar</button></div>
                                <div class="slot slot-taken" data-doctor="Dr. Diego Ramírez"><span>Dr. Diego Ramírez</span><small>Dermatología · Seguimiento</small><button type="button" disabled>Ocupado</button></div>
                                <div class="slot slot-available" data-doctor="Dra. Lucía Torres"><span>Dra. Lucía Torres</span><small>Pediatría · Control</small><button type="button" data-reserve>Reservar</button></div>
                            </div>
                        </div>
                        <div class="patient-week-scroll"><div class="patient-week-grid" data-week-grid hidden></div></div>
                    </section>

                    <!-- Mis consultas -->
                    <section class="panel-view" id="view-consultas">
                        <div class="panel-header">
                            <p class="dashboard-kicker">MIS CITAS</p>
                            <h2>Consultas solicitadas</h2>
                        </div>
                        <div class="consultas-tabs">
                            <button class="consulta-tab active" type="button" data-consultation-filter="all">Todas</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="pending">Pendientes</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="confirmed">Confirmadas</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="completed">Completadas</button>
                        </div>
                        <div class="consultas-list">
                            <div class="consulta-card" data-consultation-status="pending">
                                <div class="consulta-info">
                                    <strong>Consulta inicial · Cardiología</strong>
                                    <span>Dra. Andrea Méndez</span>
                                </div>
                                <div class="consulta-meta">
                                    <span>Jue 15 oct · 9:00</span><span>Consultorio 3</span>
                                </div>
                                <span class="consulta-status status-pending">Pendiente</span>
                            </div>
                            <div class="consulta-card" data-consultation-status="confirmed">
                                <div class="consulta-info">
                                    <strong>Seguimiento · Dermatología</strong>
                                    <span>Dr. Diego Ramírez</span>
                                </div>
                                <div class="consulta-meta">
                                    <span>Mar 20 oct · 10:30</span><span>Consultorio 7</span>
                                </div>
                                <span class="consulta-status status-confirmed">Confirmada</span>
                            </div>
                            <div class="consulta-card" data-consultation-status="completed">
                                <div class="consulta-info">
                                    <strong>Control · Pediatría</strong>
                                    <span>Dra. Lucía Torres</span>
                                </div>
                                <div class="consulta-meta">
                                    <span>Lun 26 oct · 11:30</span><span>Consultorio 2</span>
                                </div>
                                <span class="consulta-status status-completed">Completada</span>
                            </div>
                            <div class="consulta-card" data-consultation-status="pending">
                                <div class="consulta-info">
                                    <strong>Consulta inicial · Cardiología</strong>
                                    <span>Dra. Andrea Méndez</span>
                                </div>
                                <div class="consulta-meta">
                                    <span>Vie 30 oct · 12:15</span><span>Consultorio 3</span>
                                </div>
                                <span class="consulta-status status-pending">Pendiente</span>
                            </div>
                        </div>
                        <p class="patient-empty-state" data-consultations-empty hidden>No hay consultas en esta categoría.</p>
                    </section>

                    <!-- Notificaciones -->
                    <section class="panel-view" id="view-notificaciones">
                        <div class="panel-header">
                            <p class="dashboard-kicker">AVISOS Y RECUERDOS</p>
                            <h2>Notificaciones</h2>
                            <button class="patient-clear-filter" type="button" data-mark-notifications>Marcar todas como leídas</button>
                        </div>
                        <div class="notifications-list">
                            <div class="notification-item notification-unread">
                                <div class="notification-dot"></div>
                                <div class="notification-content">
                                    <strong>Cita confirmada</strong>
                                    <span>Tu consulta con la Dra. Andrea Méndez ha sido confirmada para el jueves 15 de octubre a las 9:00.</span>
                                    <time>Hace 2 h</time>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-dot"></div>
                                <div class="notification-content">
                                    <strong>Nueva solicitud recibida</strong>
                                    <span>Tu solicitud de consulta con el Dr. Diego Ramírez está pendiente de confirmación.</span>
                                    <time>Hace 5 h</time>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-dot"></div>
                                <div class="notification-content">
                                    <strong>Recordatorio de cita</strong>
                                    <span>Mañana tienes una consulta con la Dra. Lucía Torres a las 11:30.</span>
                                    <time>Ayer</time>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-dot"></div>
                                <div class="notification-content">
                                    <strong>Datos actualizados</strong>
                                    <span>Tu perfil personal se ha actualizado correctamente.</span>
                                    <time>Lun 12 oct</time>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="patient-card">
                    <div class="patient-card-heading"><span>MI PERFIL</span><button type="button" class="patient-profile-link" data-view="perfil" aria-label="Abrir mi perfil">•••</button></div>
                    <div class="avatar avatar-large">JP</div>
                    <h3>Juan Pérez</h3>
                    <p>Paciente desde 2025</p>
                    <div class="patient-divider"></div>
                    <div class="detail-row"><span>Correo</span><strong>juan@correo.com</strong></div>
                    <div class="detail-row"><span>Teléfono</span><strong>+52 55 0000 0000</strong></div>
                    <div class="detail-row"><span>Notificaciones</span><strong>Activas</strong></div>
                    <div class="patient-status"><span></span> Datos actualizados</div>
                </aside>
            </div>
        </section>
    </main>

    <dialog class="patient-dialog" data-reservation-dialog aria-labelledby="reservation-title">
        <form class="patient-dialog-content" method="dialog" data-reservation-form>
            <p class="dashboard-kicker">RESERVA DE MUESTRA</p>
            <h2 id="reservation-title">Solicitar horario</h2>
            <p data-reservation-description></p>
            <div class="patient-dialog-actions"><button type="button" data-close-reservation>Cancelar</button><button type="submit">Confirmar solicitud</button></div>
        </form>
    </dialog>
    <div class="patient-toast" role="status" aria-live="polite"></div>
</body>
</html>
