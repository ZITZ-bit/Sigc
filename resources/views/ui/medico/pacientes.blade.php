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
                        <div class="medico-empty-state">
                            <strong>No tienes pacientes asignados</strong>
                            <p>Los pacientes vinculados a tus consultas aparecerán aquí.</p>
                        </div>
                    </div>

                    <p class="medico-text-center">Los pacientes se asignan automáticamente cuando atiendes una solicitud de consulta.</p>
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