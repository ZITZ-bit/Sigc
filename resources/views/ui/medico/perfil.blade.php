<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Perfil — {{ config('app.name') }}</title>
    <meta name="description" content="Administra tu información profesional y preferencias.">
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
                        <h1>Mi perfil</h1>
                        <p>Administra tu información profesional y preferencias de cuenta.</p>
                    </div>
                    <div class="medico-header-actions">
                        <button class="medico-btn-primary">Editar perfil</button>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-perfil-grid">
                        <aside class="medico-perfil-sidebar-card">
                            <div class="medico-perfil-avatar-wrapper">
                                <div class="medico-perfil-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                                <p class="medico-perfil-name">Dr. Médico demostrativo</p>
                                <span class="medico-perfil-specialty">Medicina General</span>
                                <p class="medico-perfil-email">medico@ejemplo.com</p>
                            </div>
                            <button class="medico-perfil-edit-btn">Editar información</button>
                        </aside>

                        <div class="medico-perfil-content">
                            <div class="medico-perfil-section">
                                <h3 class="medico-perfil-section-title">Información profesional</h3>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Nombre y apellidos</label>
                                    <p class="medico-perfil-field-value">Dr. Médico demostrativo</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Especialidad</label>
                                    <p class="medico-perfil-field-value">Medicina General</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Número de colegiado</label>
                                    <p class="medico-perfil-field-value">COLEG-001234</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Correo electrónico</label>
                                    <p class="medico-perfil-field-value">medico@ejemplo.com</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Teléfono</label>
                                    <p class="medico-perfil-field-value">+34 600 000 004</p>
                                </div>
                            </div>

                            <div class="medico-perfil-section">
                                <h3 class="medico-perfil-section-title">Resumen profesional</h3>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Experiencia</label>
                                    <p class="medico-perfil-field-value">10 años en práctica clínica</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Centros de trabajo</label>
                                    <p class="medico-perfil-field-value">Clínica {{ config('app.name') }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Horario habitual</label>
                                    <p class="medico-perfil-field-value">Lunes a viernes, 9:00 — 17:00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="medico-disclaimer">Datos de demostración. Esta vista no guarda cambios realmente.</p>
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