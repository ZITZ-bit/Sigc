<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Médicos y Horarios — {{ config('app.name') }}</title>
    <meta name="description" content="Directorio de médicos y sus horarios disponibles.">
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
                        <h1>Médicos y horarios</h1>
                        <p>Directorio de profesionales y su disponibilidad semanal.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-medicos-grid">
                        <article class="coordinacion-medico-card">
                            <div class="coordinacion-medico-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                            <h3 class="coordinacion-medico-name">Dr. Carlos Méndez</h3>
                            <span class="coordinacion-medico-id">ID: MED-001</span>
                            <p class="coordinacion-medico-info">Especialidad: Medicina General · Disponible: Lunes a Viernes · Citas hoy: 4</p>
                            <div class="coordinacion-medico-actions">
                                <button class="coordinacion-medico-btn coordinacion-medico-btn--view" disabled>Ver horario</button>
                            </div>
                        </article>
                        <article class="coordinacion-medico-card">
                            <div class="coordinacion-medico-avatar" aria-hidden="true">&#x1F469;&#x200D;&#x1F3EB;</div>
                            <h3 class="coordinacion-medico-name">Dra. Laura Gómez</h3>
                            <span class="coordinacion-medico-id">ID: MED-002</span>
                            <p class="coordinacion-medico-info">Especialidad: Cardiología · Disponible: Martes a Sábado · Citas hoy: 3</p>
                            <div class="coordinacion-medico-actions">
                                <button class="coordinacion-medico-btn coordinacion-medico-btn--view" disabled>Ver horario</button>
                            </div>
                        </article>
                        <article class="coordinacion-medico-card">
                            <div class="coordinacion-medico-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                            <h3 class="coordinacion-medico-name">Dr. Roberto Silva</h3>
                            <span class="coordinacion-medico-id">ID: MED-003</span>
                            <p class="coordinacion-medico-info">Especialidad: Pediatría · Disponible: Lunes a Jueves · Citas hoy: 2</p>
                            <div class="coordinacion-medico-actions">
                                <button class="coordinacion-medico-btn coordinacion-medico-btn--view" disabled>Ver horario</button>
                            </div>
                        </article>
                    </div>

                    <p class="coordinacion-disclaimer">Datos de demostración. No son profesionales reales.</p>
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