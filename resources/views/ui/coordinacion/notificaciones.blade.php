<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Notificaciones — {{ config('app.name') }}</title>
    <meta name="description" content="Consulte notificaciones y recordatorios de la clínica.">
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
                        <h1>Notificaciones</h1>
                        <p>Manténgase al tanto de recordatorios y avisos clínicos.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-notificaciones-filters">
                        <button class="coordinacion-filter-btn coordinacion-filter-btn--active" aria-pressed="true">Todas</button>
                        <button class="coordinacion-filter-btn" aria-pressed="false">Citas</button>
                        <button class="coordinacion-filter-btn" aria-pressed="false">Alertas clínicas</button>
                    </div>

                    <div class="coordinacion-notificaciones-list">
                        <div class="coordinacion-notificacion-item coordinacion-notificacion-item--unread">
                            <span class="coordinacion-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="coordinacion-notificacion-content">
                                <p class="coordinacion-notificacion-title">Cita sin confirmar</p>
                                <p class="coordinacion-notificacion-text">La cita de las 11:30 con Carlos Ruiz está pendiente de confirmación.</p>
                                <span class="coordinacion-notificacion-time">Hace 15 min</span>
                            </div>
                        </div>
                        <div class="coordinacion-notificacion-item">
                            <span class="coordinacion-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="coordinacion-notificacion-content">
                                <p class="coordinacion-notificacion-title">Resultado de laboratorio</p>
                                <p class="coordinacion-notificacion-text">Los exámenes de María Rodríguez están listos para revisar.</p>
                                <span class="coordinacion-notificacion-time">Hace 2 horas</span>
                            </div>
                        </div>
                        <div class="coordinacion-notificacion-item coordinacion-notificacion-item--unread">
                            <span class="coordinacion-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="coordinacion-notificacion-content">
                                <p class="coordinacion-notificacion-title">Cambio de horario</p>
                                <p class="coordinacion-notificacion-text">La cita de las 14:00 se ha movido a las 15:30.</p>
                                <span class="coordinacion-notificacion-time">Hace 5 horas</span>
                            </div>
                        </div>
                    </div>

                    <p class="coordinacion-disclaimer">Datos de demostración. No son notificaciones reales.</p>
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