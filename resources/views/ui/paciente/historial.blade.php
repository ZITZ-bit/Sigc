<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Historial Médico — {{ config('app.name') }}</title>
    <meta name="description" content="Consulta tus consultas y exámenes anteriores.">
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
                        <h1>Historial médico</h1>
                        <p>Revisa tus consultas y exámenes realizados.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-historial-list">
                        <div class="paciente-empty-state">
                            <strong>No tienes historial médico registrado</strong>
                            <p>Cuando se completen consultas, aparecerán aquí tus registros médicos anteriores.</p>
                        </div>
                    </div>

                    <p class="paciente-text-center">El historial se completa automáticamente cuando se atienden tus consultas solicitadas.</p>
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