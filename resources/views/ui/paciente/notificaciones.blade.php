<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Notificaciones — {{ config('app.name') }}</title>
    <meta name="description" content="Consulta tus notificaciones y recordatorios médicos.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/paciente.css', 'resources/js/app.js'])
</head>
<body>

    <div class="paciente-page">
        @include('portal::paciente.sidebar')

        <div class="paciente-main">
            <div class="paciente-content-area">
                <div class="paciente-header">
                    <div class="paciente-header-content">
                        <h1>Notificaciones</h1>
                        <p>Mantente al tanto de tus recordatorios y avisos.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-notificaciones-filters">
                        <button class="paciente-filter-btn paciente-filter-btn--active" aria-pressed="true">Todas</button>
                        <button class="paciente-filter-btn" aria-pressed="false">Recordatorios</button>
                        <button class="paciente-filter-btn" aria-pressed="false">Avisos</button>
                    </div>

                    <div class="paciente-notificaciones-list">
                        <div class="paciente-notificacion-item paciente-notificacion-item--unread">
                            <span class="paciente-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="paciente-notificacion-content">
                                <p class="paciente-notificacion-title">Recordatorio de cita</p>
                                <p class="paciente-notificacion-text">Tienes una cita programada para mañana a las 10:00 con el Dr. Carlos Méndez.</p>
                                <span class="paciente-notificacion-time">Hace 2 horas</span>
                            </div>
                        </div>
                        <div class="paciente-notificacion-item">
                            <span class="paciente-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="paciente-notificacion-content">
                                <p class="paciente-notificacion-title">Resultado disponible</p>
                                <p class="paciente-notificacion-text">Los resultados de tu análisis de sangre están listos para descargar.</p>
                                <span class="paciente-notificacion-time">Hace 1 día</span>
                            </div>
                        </div>
                        <div class="paciente-notificacion-item paciente-notificacion-item--unread">
                            <span class="paciente-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="paciente-notificacion-content">
                                <p class="paciente-notificacion-title">Cambio de horario</p>
                                <p class="paciente-notificacion-text">Tu cita del 12 de noviembre se ha movido a las 15:00.</p>
                                <span class="paciente-notificacion-time">Hace 3 días</span>
                            </div>
                        </div>
                    </div>

                    <p class="paciente-text-center paciente-disclaimer">Datos de demostración. No son notificaciones reales.</p>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.paciente-mobile-toggle');
            var close = document.querySelector('.paciente-mobile-close');
            var sidebar = document.getElementById('paciente-mobile-sidebar');
            var overlay = document.querySelector('.paciente-sidebar-overlay');

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