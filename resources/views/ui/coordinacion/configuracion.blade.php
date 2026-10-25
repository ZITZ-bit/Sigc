<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Configuración — {{ config('app.name') }}</title>
    <meta name="description" content="Configuración de la clínica.">
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
                        <h1>Configuración de la clínica</h1>
                        <p>Administre los datos y políticas de la clínica.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <form novalidate class="coordinacion-configuracion-form">
                        <div class="coordinacion-configuracion-section">
                            <h3 class="coordinacion-configuracion-section-title">Datos de la clínica</h3>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Nombre de la clínica</label>
                                <input type="text" class="coordinacion-configuracion-input" value="{{ config('app.name') }}" disabled>
                                <p class="coordinacion-configuracion-help">Solo para visualización.</p>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Dirección</label>
                                <input type="text" class="coordinacion-configuracion-input" placeholder="Dirección de la clínica" disabled>
                                <p class="coordinacion-configuracion-help">Campo demostrativo, no guarda cambios.</p>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Teléfono</label>
                                <input type="tel" class="coordinacion-configuracion-input" placeholder="+34 600 000 000" disabled>
                                <p class="coordinacion-configuracion-help">Campo demostrativo, no guarda cambios.</p>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Correo electrónico</label>
                                <input type="email" class="coordinacion-configuracion-input" placeholder="clinica@ejemplo.com" disabled>
                                <p class="coordinacion-configuracion-help">Campo demostrativo, no guarda cambios.</p>
                            </div>
                        </div>

                        <div class="coordinacion-configuracion-section">
                            <h3 class="coordinacion-configuracion-section-title">Horario y especialidades</h3>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Horario de atención</label>
                                <input type="text" class="coordinacion-configuracion-input" value="Lunes a viernes, 9:00 — 17:00" disabled>
                                <p class="coordinacion-configuracion-help">Solo para visualización.</p>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Duración de citas</label>
                                <input type="text" class="coordinacion-configuracion-input" value="30 minutos" disabled>
                                <p class="coordinacion-configuracion-help">Solo para visualización.</p>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Política de cancelación</label>
                                <input type="text" class="coordinacion-configuracion-input" value="Hasta 24 horas antes" disabled>
                                <p class="coordinacion-configuracion-help">Solo para visualización.</p>
                            </div>
                        </div>

                        <div class="coordinacion-configuracion-section">
                            <h3 class="coordinacion-configuracion-section-title">Preferencias de notificaciones</h3>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Notificaciones por email</label>
                                <input type="checkbox" disabled>
                            </div>
                            <div class="coordinacion-configuracion-field">
                                <label class="coordinacion-configuracion-label">Recordatorios de cita</label>
                                <input type="checkbox" disabled>
                            </div>
                        </div>

                        <div class="coordinacion-configuracion-actions">
                            <button class="coordinacion-configuracion-btn coordinacion-configuracion-btn--save coordinacion-configuracion-btn--disabled" disabled>Guardar cambios</button>
                            <button class="coordinacion-configuracion-btn coordinacion-configuracion-btn--cancel">Descartar</button>
                        </div>

                        <p class="coordinacion-disclaimer">Este formulario es una demostración visual. No guarda cambios realmente.</p>
                    </form>
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