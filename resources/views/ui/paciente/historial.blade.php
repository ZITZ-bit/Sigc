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
                        <div class="paciente-historial-item">
                            <div class="paciente-historial-date">
                                <span class="paciente-historial-date-value">15 Sep 2024</span>
                            </div>
                            <div class="paciente-historial-info">
                                <span class="paciente-historial-doctor">Dr. Carlos Méndez</span>
                                <span class="paciente-historial-specialty">Medicina General · Consulta de control</span>
                                <span class="paciente-historial-type">Consulta presencial</span>
                            </div>
                            <div class="paciente-historial-actions">
                                <button class="paciente-historial-btn" aria-label="Ver detalles">Ver detalles</button>
                                <button class="paciente-historial-btn" aria-label="Descargar reporte">Descargar</button>
                            </div>
                        </div>
                        <div class="paciente-historial-item">
                            <div class="paciente-historial-date">
                                <span class="paciente-historial-date-value">02 Ago 2024</span>
                            </div>
                            <div class="paciente-historial-info">
                                <span class="paciente-historial-doctor">Dra. Laura Gómez</span>
                                <span class="paciente-historial-specialty">Cardiología · Revisión cardíaca</span>
                                <span class="paciente-historial-type">Consulta presencial</span>
                            </div>
                            <div class="paciente-historial-actions">
                                <button class="paciente-historial-btn" aria-label="Ver detalles">Ver detalles</button>
                                <button class="paciente-historial-btn" aria-label="Descargar reporte">Descargar</button>
                            </div>
                        </div>
                        <div class="paciente-historial-item">
                            <div class="paciente-historial-date">
                                <span class="paciente-historial-date-value">10 Jun 2024</span>
                            </div>
                            <div class="paciente-historial-info">
                                <span class="paciente-historial-doctor">Dr. Roberto Silva</span>
                                <span class="paciente-historial-specialty">Pediatría · Control infantil</span>
                                <span class="paciente-historial-type">Telemedicina</span>
                            </div>
                            <div class="paciente-historial-actions">
                                <button class="paciente-historial-btn" aria-label="Ver detalles">Ver detalles</button>
                                <button class="paciente-historial-btn" aria-label="Descargar reporte">Descargar</button>
                            </div>
                        </div>
                    </div>

                    <p class="paciente-text-center paciente-disclaimer">Datos de demostración. No son registros médicos reales.</p>
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