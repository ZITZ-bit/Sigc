<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Personal y Usuarios — {{ config('app.name') }}</title>
    <meta name="description" content="Listado de trabajadores de la clínica.">
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
                        <h1>Personal y usuarios</h1>
                        <p>Administre el listado de trabajadores de la clínica.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-personal-header">
                        <div>
                            <input type="text" class="coordinacion-configuracion-input" placeholder="Buscar por nombre, rol o departamento..." aria-label="Buscar personal">
                        </div>
                    </div>

                    <table class="coordinacion-personal-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>ID</th>
                                <th>Rol</th>
                                <th>Departamento</th>
                                <th>Estado</th>
                                <th>Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8">
                                    <div class="coordinacion-empty-state">
                                        <strong>No hay personal registrado</strong>
                                        <p>Los usuarios del sistema aparecerán aquí cuando se creen cuentas de personal.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="coordinacion-text-center">El personal se gestiona a través del módulo de Médicos y horarios para el personal clínico, y del registro de usuarios para el personal administrativo.</p>
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