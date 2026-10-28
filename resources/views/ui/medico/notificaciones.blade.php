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
                    @include('portal::medico.notificaciones', ['notifications' => $notifications])
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