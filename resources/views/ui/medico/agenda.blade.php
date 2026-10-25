<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Agenda — {{ config('app.name') }}</title>
    <meta name="description" content="Gestiona tus citas médicas programadas.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/medico.css', 'resources/js/app.js'])
</head>
<body>

    <div class="medico-page">
        @include('portal::medico.sidebar')

        <div class="medico-main">
            <div class="medico-content-area">
                <div class="medico-header">
                    <div class="medico-header-content">
                        <h1>Mi agenda</h1>
                        <p>Consulta y gestiona tus citas médicas programadas.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-agenda-header">
                        <div class="medico-agenda-filters">
                            <button class="medico-filter-btn medico-filter-btn--active" aria-pressed="true">Hoy</button>
                            <button class="medico-filter-btn" aria-pressed="false">Esta semana</button>
                            <button class="medico-filter-btn" aria-pressed="false">Próximos 7 días</button>
                        </div>
                    </div>

                    <table class="medico-agenda-table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="medico-agenda-time">09:00</span></td>
                                <td><span class="medico-agenda-patient">Ana López García</span></td>
                                <td><span class="medico-agenda-reason">Control general</span></td>
                                <td>30 min</td>
                                <td><span class="medico-badge medico-badge--confirmed">Confirmada</span></td>
                                <td>
                                    <div class="medico-agenda-actions">
                                        <button class="medico-agenda-btn medico-agenda-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="medico-agenda-time">09:45</span></td>
                                <td><span class="medico-agenda-patient">Carlos Ruiz</span></td>
                                <td><span class="medico-agenda-reason">Seguimiento cardiológico</span></td>
                                <td>45 min</td>
                                <td><span class="medico-badge medico-badge--pending">Pendiente</span></td>
                                <td>
                                    <div class="medico-agenda-actions">
                                        <button class="medico-agenda-btn medico-agenda-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="medico-agenda-time">10:30</span></td>
                                <td><span class="medico-agenda-patient">María Rodríguez</span></td>
                                <td><span class="medico-agenda-reason">Control pediátrico</span></td>
                                <td>30 min</td>
                                <td><span class="medico-badge medico-badge--completed">Completada</span></td>
                                <td>
                                    <div class="medico-agenda-actions">
                                        <button class="medico-agenda-btn medico-agenda-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="medico-agenda-time">11:15</span></td>
                                <td><span class="medico-agenda-patient">Luis Fernández</span></td>
                                <td><span class="medico-agenda-reason">Consulta general</span></td>
                                <td>30 min</td>
                                <td><span class="medico-badge medico-badge--cancelled">Cancelada</span></td>
                                <td>
                                    <div class="medico-agenda-actions">
                                        <button class="medico-agenda-btn medico-agenda-btn--view" aria-label="Ver detalles">Ver</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="medico-disclaimer">Datos de demostración. No son citas reales.</p>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.medico-mobile-toggle');
            var close = document.querySelector('.medico-mobile-close');
            var sidebar = document.getElementById('medico-mobile-sidebar');
            var overlay = document.querySelector('.medico-sidebar-overlay');

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