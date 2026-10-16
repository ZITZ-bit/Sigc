<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <meta name="description" content="SIGC te ayuda a organizar tu clínica, tus citas y la atención de cada paciente desde un solo lugar.">
    <title>SIGC | Gestión clínica, más humana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/components.css', 'resources/css/portal.css'])
</head>
<body>
    <x-navbar />

    <main id="inicio">
        <section class="hero section-shell">
            <div class="hero-copy">
                <p class="eyebrow"><span class="eyebrow-dot"></span> SISTEMA INTEGRAL DE GESTIÓN CLÍNICA</p>
                <h1>Tu clínica en orden.<br><span>Tu atención, al centro.</span></h1>
                <p class="hero-description">Menos tiempo en tareas administrativas y más espacio para lo que importa: cuidar a cada paciente.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#contacto">Descubre SIGC <span aria-hidden="true">→</span></a>
                    <a class="text-link" href="#funciones">Explorar funciones <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-note"><span class="note-check" aria-hidden="true">✓</span> Agenda, expedientes y seguimiento en un solo lugar</div>
            </div>

            <div class="product-stage" aria-label="Vista previa de la agenda de SIGC">
                <div class="stage-sticker">UN DÍA MÁS<br>EN EQUILIBRIO <span aria-hidden="true">✳</span></div>
                <div class="dashboard-window">
                    <div class="dashboard-topbar">
                        <div class="dashboard-brand"><span class="mini-brand-mark">S</span> sigc <span class="workspace-label">/ Consultorio Norte</span></div>
                        <div class="topbar-tools"><span class="tool-search">⌕ <span>Buscar paciente</span></span><span class="avatar avatar-small">AM</span></div>
                    </div>
                    <div class="dashboard-content">
                        <aside class="dashboard-sidebar" aria-hidden="true">
                            <span class="side-icon side-icon-active">▦</span>
                            <span class="side-icon">◷</span>
                            <span class="side-icon">♡</span>
                            <span class="side-icon">▤</span>
                            <span class="side-icon">⚙</span>
                        </aside>
                        <div class="schedule-panel">
                            <div class="schedule-heading">
                                <div><p class="dashboard-kicker">JUEVES, 15 OCTUBRE</p><h2>Tu agenda</h2></div>
                                <span class="today-button">Hoy <span aria-hidden="true">⌄</span></span>
                            </div>
                            <div class="schedule-date"><span>‹</span><strong>15 oct, 2026</strong><span>›</span><span class="view-toggle">Día&nbsp;&nbsp; Semana</span></div>
                            <div class="calendar-grid">
                                <div class="time-column"><span>9:00</span><span>10:00</span><span>11:00</span><span>12:00</span><span>13:00</span></div>
                                <div class="appointments">
                                    <div class="appointment appointment-mint"><strong>Mariana López</strong><span>Consulta inicial · 9:00</span><i>AM</i></div>
                                    <div class="appointment appointment-peach"><strong>Diego Ramírez</strong><span>Seguimiento · 10:30</span><i>DR</i></div>
                                    <div class="appointment appointment-blue"><strong>Lucía Torres</strong><span>Evaluación · 11:30</span><i>LT</i></div>
                                    <div class="appointment appointment-yellow"><strong>Nota clínica pendiente</strong><span>Mariana López · 12:15</span></div>
                                </div>
                            </div>
                        </div>
                        <aside class="patient-card">
                            <div class="patient-card-heading"><span>PRÓXIMA CITA</span><span>•••</span></div>
                            <div class="avatar avatar-large">ML</div>
                            <h3>Mariana López</h3>
                            <p>Paciente desde marzo 2025</p>
                            <div class="patient-divider"></div>
                            <div class="detail-row"><span>Hora</span><strong>9:00 – 9:50</strong></div>
                            <div class="detail-row"><span>Especialista</span><strong>Andrea Méndez</strong></div>
                            <div class="detail-row"><span>Motivo</span><strong>Consulta inicial</strong></div>
                            <div class="patient-status"><span></span> Expediente actualizado</div>
                        </aside>
                    </div>
                </div>
                <div class="floating-reminder"><span class="reminder-icon" aria-hidden="true">✓</span><span><strong>Cita confirmada</strong><small>Recordatorio enviado</small></span></div>
            </div>
        </section>

        <section class="trust-strip" aria-label="Beneficios de SIGC">
            <div class="trust-inner"><span>UN SISTEMA PARA ACOMPAÑARTE EN CADA PASO</span><strong><i>01</i> Organiza</strong><strong><i>02</i> Acompaña</strong><strong><i>03</i> Crece</strong></div>
        </section>

        <section class="features section-shell" id="funciones">
            <div class="section-heading">
                <p class="eyebrow">TODO CONECTADO, POR FIN</p>
                <h2>La gestión fluye.<br><span>El cuidado también.</span></h2>
                <p>Herramientas sencillas que trabajan juntas para que el día a día de tu clínica sea más claro.</p>
            </div>
            <div class="feature-grid">
                <x-feature-card
                    number="01"
                    label="ORGANIZA"
                    visual-label="AGENDA"
                    title="Una agenda que sí te sigue el ritmo"
                    description="Visualiza citas, disponibilidad y cambios del día sin perder de vista a nadie."
                    visual-tone="agenda"
                >
                    <x-slot:visual>
                        <div class="mini-calendar">
                            <div class="mini-calendar-head"><span>Octubre 2026</span><span>‹ &nbsp; ›</span></div>
                            <div class="mini-week"><i>L</i><i>M</i><i>M</i><i>J</i><i>V</i><i>S</i><i>D</i></div>
                            <div class="mini-days"><span>12</span><span>13</span><span>14</span><b>15</b><span>16</span><span>17</span><span>18</span></div>
                            <div class="mini-event">9:00&nbsp; Mariana López</div>
                            <div class="mini-event mini-event-alt">10:30&nbsp; Diego Ramírez</div>
                        </div>
                    </x-slot:visual>
                </x-feature-card>
                <x-feature-card
                    number="02"
                    label="ACOMPAÑA"
                    visual-label="EXPEDIENTE"
                    title="La historia de cada paciente, a la mano"
                    description="Centraliza expedientes y notas para dar continuidad a cada proceso."
                    visual-tone="records"
                >
                    <x-slot:visual>
                        <div class="record-sheet">
                            <span class="record-avatar">LT</span>
                            <div><strong>Lucía Torres</strong><small>Expediente clínico</small></div>
                            <span class="record-check">✓</span>
                            <hr><i></i><i></i><i></i>
                            <div class="record-tag">Última consulta&nbsp; · &nbsp;14 oct</div>
                        </div>
                    </x-slot:visual>
                </x-feature-card>
                <x-feature-card
                    number="03"
                    label="CRECE"
                    visual-label="RESUMEN DEL MES"
                    title="Decisiones con más claridad"
                    description="Consulta la actividad de tu clínica y descubre dónde enfocar tu energía."
                    visual-tone="insights"
                >
                    <x-slot:visual>
                        <div class="insight-card">
                            <div><small>Citas atendidas</small><strong>128 <em>+12%</em></strong></div>
                            <div class="chart-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                            <span>Una vista clara para decidir tu siguiente paso.</span>
                        </div>
                    </x-slot:visual>
                </x-feature-card>
            </div>
        </section>

        <section class="care-band" id="beneficios">
            <div class="care-inner">
                <div class="care-symbol" aria-hidden="true"><span>+</span><i></i></div>
                <div class="care-copy"><p class="eyebrow">TECNOLOGÍA CON SENTIDO HUMANO</p><h2>La administración<br>no debería ocupar<br><span>el lugar del cuidado.</span></h2></div>
                <div class="care-details"><p>SIGC reúne lo esencial para que tú y tu equipo trabajen con calma, claridad y más tiempo para sus pacientes.</p><a class="text-link text-link-light" href="#contacto">Conoce cómo funciona <span aria-hidden="true">→</span></a><div class="care-index">HECHO PARA EL DÍA A DÍA DE TU CLÍNICA <span>✳</span></div></div>
            </div>
        </section>

        <section class="contact-section section-shell" id="contacto">
            <div><p class="eyebrow">UN MEJOR DÍA EMPIEZA AQUÍ</p><h2>Haz espacio para<br><span>lo que importa.</span></h2></div>
            <div class="contact-action"><p>Conoce una forma más simple de llevar tu clínica, equipo y atención en sintonía.</p><a class="button button-primary" href="mailto:hola@sigc.mx?subject=Quiero%20conocer%20SIGC">Hablemos de tu clínica <span aria-hidden="true">↗</span></a></div>
        </section>
    </main>

    <footer class="site-footer"><x-brand variant="footer" label="SIGC, volver al inicio" /><span>Gestión clínica con más sentido humano.</span><a href="#inicio">Volver arriba ↑</a></footer>
</body>
</html>