<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Citas y Agenda — {{ config('app.name') }}</title>
    <meta name="description" content="Gestionar citas médicas y agenda de la clínica.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/coordinacion.css', 'resources/js/app.js'])
</head>
<body>

    <div class="coordinacion-page">
        @include('portal::coordinacion.sidebar')

        <div class="coordinacion-main">
            <div class="coordinacion-content-area">
                <div class="coordinacion-header">
                    <div class="coordinacion-header-content">
                        <h1>Citas y agenda</h1>
                        <p>Gestione las citas médicas programadas para hoy y los próximos días.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-citas-header">
                        <div class="coordinacion-citas-filters">
                            <button class="coordinacion-filter-btn coordinacion-filter-btn--active" aria-pressed="true">Hoy</button>
                            <button class="coordinacion-filter-btn" aria-pressed="false">Esta semana</button>
                            <button class="coordinacion-filter-btn" aria-pressed="false">Próximos 7 días</button>
                        </div>
                    </div>

                    <table class="coordinacion-citas-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Especialidad</th>
                                <th>Motivo</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>25 Oct</td>
                                <td><span class="coordinacion-citas-time">09:00</span></td>
                                <td><span class="coordinacion-citas-patient">Ana López García</span></td>
                                <td>Dr. Carlos Méndez</td>
                                <td>Medicina General</td>
                                <td><span class="coordinacion-citas-reason">Control general</span></td>
                                <td>30 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--confirmed">Confirmada</span></td>
                                <td>
                                    <div class="coordinacion-citas-actions">
                                        <button class="coordinacion-citas-btn coordinacion-citas-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>25 Oct</td>
                                <td><span class="coordinacion-citas-time">10:30</span></td>
                                <td><span class="coordinacion-citas-patient">Carlos Ruiz</span></td>
                                <td>Dra. Laura Gómez</td>
                                <td>Cardiología</td>
                                <td><span class="coordinacion-citas-reason">Seguimiento cardiológico</span></td>
                                <td>45 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--pending">Pendiente</span></td>
                                <td>
                                    <div class="coordinacion-citas-actions">
                                        <button class="coordinacion-citas-btn coordinacion-citas-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>25 Oct</td>
                                <td><span class="coordinacion-citas-time">11:15</span></td>
                                <td><span class="coordinacion-citas-patient">María Rodríguez</span></td>
                                <td>Dr. Roberto Silva</td>
                                <td>Pediatría</td>
                                <td><span class="coordinacion-citas-reason">Control pediátrico</span></td>
                                <td>30 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--completed">Completada</span></td>
                                <td>
                                    <div class="coordinacion-citas-actions">
                                        <button class="coordinacion-citas-btn coordinacion-citas-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>25 Oct</td>
                                <td><span class="coordinacion-citas-time">12:00</span></td>
                                <td><span class="coordinacion-citas-patient">Luis Fernández</span></td>
                                <td>Dr. Carlos Méndez</td>
                                <td>Medicina General</td>
                                <td><span class="coordinacion-citas-reason">Consulta general</span></td>
                                <td>30 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--cancelled">Cancelada</span></td>
                                <td>
                                    <div class="coordinacion-citas-actions">
                                        <button class="coordinacion-citas-btn coordinacion-citas-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="coordinacion-disclaimer">Datos de demostración. No son citas reales.</p>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.coordinacion-mobile-toggle');
            var close = document.querySelector('.coordinacion-mobile-close');
            var sidebar = document.getElementById('coordinacion-mobile-sidebar');
            var overlay = document.querySelector('.coordinacion-sidebar-overlay');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    if (overlay) overlay.classList.toggle('open');
                });
            }

            if (close && sidebar) {
                close.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
            }
        });
    </script>
</body>
</html>