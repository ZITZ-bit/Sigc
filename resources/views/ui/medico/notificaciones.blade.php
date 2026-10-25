<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Notificaciones — {{ config('app.name') }}</title>
    <meta name="description" content="Consulta tus notificaciones y recordatorios médicos.">
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
                        <h1>Notificaciones</h1>
                        <p>Mantente al tanto de tus recordatorios y avisos clínicos.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-notificaciones-filters">
                        <button class="medico-filter-btn medico-filter-btn--active" aria-pressed="true">Todas</button>
                        <button class="medico-filter-btn" aria-pressed="false">Citas</button>
                        <button class="medico-filter-btn" aria-pressed="false">Alertas clínicas</button>
                    </div>

                    <div class="medico-notificaciones-list">
                        <div class="medico-notificacion-item medico-notificacion-item--unread">
                            <span class="medico-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="medico-notificacion-content">
                                <p class="medico-notificacion-title">Nueva cita solicitada</p>
                                <p class="medico-notificacion-text">Juan Pérez ha solicitado una consulta para mañana a las 10:00.</p>
                                <span class="medico-notificacion-time">Hace 30 min</span>
                            </div>
                        </div>
                        <div class="medico-notificacion-item">
                            <span class="medico-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="medico-notificacion-content">
                                <p class="medico-notificacion-title">Resultado de laboratorio</p>
                                <p class="medico-notificacion-text">Los exámenes de María Rodríguez están listos para revisar.</p>
                                <span class="medico-notificacion-time">Hace 2 horas</span>
                            </div>
                        </div>
                        <div class="medico-notificacion-item medico-notificacion-item--unread">
                            <span class="medico-notificacion-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="medico-notificacion-content">
                                <p class="medico-notificacion-title">Cambio de horario</p>
                                <p class="medico-notificacion-text">La cita de las 14:00 se ha movido a las 15:30.</p>
                                <span class="medico-notificacion-time">Hace 5 horas</span>
                            </div>
                        </div>
                    </div>

                    <p class="medico-disclaimer">Datos de demostración. No son notificaciones reales.</p>
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