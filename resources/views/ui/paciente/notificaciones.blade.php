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
                        <div class="paciente-empty-state">
                            <strong>No tienes notificaciones recientes</strong>
                            <p>Los recordatorios y avisos sobre tu atención aparecerán aquí.</p>
                        </div>
                    </div>

                    <p class="paciente-text-center">Las notificaciones se generan automáticamente según el estado de tus solicitudes de consulta.</p>
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