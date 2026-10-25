<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mis Pacientes — {{ config('app.name') }}</title>
    <meta name="description" content="Listado de pacientes bajo tu cuidado.">
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
                        <h1>Mis pacientes</h1>
                        <p>Gestiona el listado de pacientes bajo tu atención.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-pacientes-header">
                        <div class="medico-pacientes-search">
                            <input type="text" class="medico-consulta-input" placeholder="Buscar por nombre o ID..." aria-label="Buscar pacientes">
                        </div>
                    </div>

                    <div class="medico-pacientes-grid">
                        <article class="medico-paciente-card">
                            <div class="medico-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="medico-paciente-name">Ana López García</h3>
                            <span class="medico-paciente-id">ID: PAC-001</span>
                            <p class="medico-paciente-info">Edad: 34 años · Tel: +34 600 000 001</p>
                            <div class="medico-paciente-actions">
                                <button class="medico-paciente-btn medico-paciente-btn--view" disabled>Ver historial</button>
                                <button class="medico-paciente-btn medico-paciente-btn--consult" disabled>Nueva consulta</button>
                            </div>
                        </article>
                        <article class="medico-paciente-card">
                            <div class="medico-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="medico-paciente-name">Carlos Ruiz</h3>
                            <span class="medico-paciente-id">ID: PAC-002</span>
                            <p class="medico-paciente-info">Edad: 45 años · Tel: +34 600 000 002</p>
                            <div class="medico-paciente-actions">
                                <button class="medico-paciente-btn medico-paciente-btn--view" disabled>Ver historial</button>
                                <button class="medico-paciente-btn medico-paciente-btn--consult" disabled>Nueva consulta</button>
                            </div>
                        </article>
                        <article class="medico-paciente-card">
                            <div class="medico-paciente-avatar" aria-hidden="true">&#x1F464;</div>
                            <h3 class="medico-paciente-name">María Rodríguez</h3>
                            <span class="medico-paciente-id">ID: PAC-003</span>
                            <p class="medico-paciente-info">Edad: 28 años · Tel: +34 600 000 003</p>
                            <div class="medico-paciente-actions">
                                <button class="medico-paciente-btn medico-paciente-btn--view" disabled>Ver historial</button>
                                <button class="medico-paciente-btn medico-paciente-btn--consult" disabled>Nueva consulta</button>
                            </div>
                        </article>
                    </div>

                    <p class="medico-disclaimer">Datos de demostración. No son pacientes reales.</p>
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