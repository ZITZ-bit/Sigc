<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Perfil — {{ config('app.name') }}</title>
    <meta name="description" content="Administre su información profesional y preferencias.">
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
                        <h1>Mi perfil</h1>
                        <p>Administre su información profesional y preferencias de cuenta.</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <button class="coordinacion-btn-primary">Editar perfil</button>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-perfil-grid">
                        <aside class="coordinacion-perfil-sidebar-card">
                            <div class="coordinacion-perfil-avatar-wrapper">
                                <div class="coordinacion-perfil-avatar" aria-hidden="true">&#x1F465;</div>
                                <p class="coordinacion-perfil-name">Coordinador demostrativo</p>
                                <span class="coordinacion-perfil-role">Coordinación clínica</span>
                                <p class="coordinacion-perfil-email">coordinador@ejemplo.com</p>
                            </div>
                            <button class="coordinacion-perfil-edit-btn">Editar información</button>
                        </aside>

                        <div class="coordinacion-perfil-content">
                            <div class="coordinacion-perfil-section">
                                <h3 class="coordinacion-perfil-section-title">Información profesional</h3>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Nombre y apellidos</label>
                                    <p class="coordinacion-perfil-field-value">Coordinador demostrativo</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Cargo</label>
                                    <p class="coordinacion-perfil-field-value">Coordinador clínico</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Correo electrónico</label>
                                    <p class="coordinacion-perfil-field-value">coordinador@ejemplo.com</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Teléfono</label>
                                    <p class="coordinacion-perfil-field-value">+34 600 000 005</p>
                                </div>
                            </div>

                            <div class="coordinacion-perfil-section">
                                <h3 class="coordinacion-perfil-section-title">Responsabilidades</h3>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Área principal</label>
                                    <p class="coordinacion-perfil-field-value">Coordinación de citas y agenda</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Horario habitual</label>
                                    <p class="coordinacion-perfil-field-value">Lunes a viernes, 9:00 — 17:00</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Departamento</label>
                                    <p class="coordinacion-perfil-field-value">Administración clínica</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="coordinacion-disclaimer">Datos de demostración. Esta vista no guarda cambios realmente.</p>
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