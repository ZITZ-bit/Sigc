<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Pacientes — {{ config('app.name') }}</title>
    <meta name="description" content="Listado de pacientes bajo coordinación de la clínica.">
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
                        <h1>Pacientes</h1>
                        <p>Administre el listado de pacientes de la clínica.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-pacientes-header">
                        <div class="coordinacion-pacientes-search">
                            <input type="text" class="coordinacion-configuracion-input" placeholder="Buscar por nombre o ID..." aria-label="Buscar pacientes">
                        </div>
                    </div>

                    <div class="coordinacion-pacientes-grid">
                        <article class="coordinacion-paciente-card">
                            <div class="coordinacion-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="coordinacion-paciente-name">Ana López García</h3>
                            <span class="coordinacion-paciente-id">ID: PAC-001</span>
                            <p class="coordinacion-paciente-info">Edad: 34 años · Tel: +34 600 000 001</p>
                            <div class="coordinacion-paciente-actions">
                                <button class="coordinacion-paciente-btn coordinacion-paciente-btn--view" disabled>Ver expediente</button>
                            </div>
                        </article>
                        <article class="coordinacion-paciente-card">
                            <div class="coordinacion-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="coordinacion-paciente-name">Carlos Ruiz</h3>
                            <span class="coordinacion-paciente-id">ID: PAC-002</span>
                            <p class="coordinacion-paciente-info">Edad: 45 años · Tel: +34 600 000 002</p>
                            <div class="coordinacion-paciente-actions">
                                <button class="coordinacion-paciente-btn coordinacion-paciente-btn--view" disabled>Ver expediente</button>
                            </div>
                        </article>
                        <article class="coordinacion-paciente-card">
                            <div class="coordinacion-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="coordinacion-paciente-name">María Rodríguez</h3>
                            <span class="coordinacion-paciente-id">ID: PAC-003</span>
                            <p class="coordinacion-paciente-info">Edad: 28 años · Tel: +34 600 000 003</p>
                            <div class="coordinacion-paciente-actions">
                                <button class="coordinacion-paciente-btn coordinacion-paciente-btn--view" disabled>Ver expediente</button>
                            </div>
                        </article>
                    </div>

                    <p class="coordinacion-disclaimer">Datos de demostración. No son pacientes reales.</p>
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