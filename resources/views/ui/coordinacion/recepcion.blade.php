<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Recepción y Seguimiento — {{ config('app.name') }}</title>
    <meta name="description" content="Gestión de recepción y seguimiento de pacientes en la clínica.">
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
                        <h1>Recepción y seguimiento</h1>
                        <p>Controle la llegada y asistencia de pacientes en tiempo real.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-recepcion-header">
                        <div class="coordinacion-recepcion-filters">
                            <button class="coordinacion-filter-btn coordinacion-filter-btn--active" aria-pressed="true">Hoy</button>
                            <button class="coordinacion-filter-btn" aria-pressed="false">Esta semana</button>
                        </div>
                    </div>

                    <table class="coordinacion-recepcion-table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Hora prevista</th>
                                <th>Tiempo de espera</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="coordinacion-recepcion-time">09:00</span></td>
                                <td><span class="coordinacion-recepcion-patient">Ana López García</span></td>
                                <td>Dr. Carlos Méndez</td>
                                <td>09:00</td>
                                <td>0 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--completed">Atendido</span></td>
                                <td>
                                    <div class="coordinacion-recepcion-actions">
                                        <button class="coordinacion-recepcion-btn coordinacion-recepcion-btn--arrived" disabled>Marcar llegado</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="coordinacion-recepcion-time">10:30</span></td>
                                <td><span class="coordinacion-recepcion-patient">Carlos Ruiz</span></td>
                                <td>Dra. Laura Gómez</td>
                                <td>10:30</td>
                                <td>5 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--pending">En espera</span></td>
                                <td>
                                    <div class="coordinacion-recepcion-actions">
                                        <button class="coordinacion-recepcion-btn coordinacion-recepcion-btn--arrived" disabled>Marcar llegado</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="coordinacion-recepcion-time">11:15</span></td>
                                <td><span class="coordinacion-recepcion-patient">María Rodríguez</span></td>
                                <td>Dr. Roberto Silva</td>
                                <td>11:15</td>
                                <td>10 min</td>
                                <td><span class="coordinacion-badge coordinacion-badge--pending">Llegó</span></td>
                                <td>
                                    <div class="coordinacion-recepcion-actions">
                                        <button class="coordinacion-recepcion-btn coordinacion-recepcion-btn--arrived" disabled>Marcar en atención</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="coordinacion-disclaimer">Datos de demostración. No son registros reales.</p>
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