<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Perfil — {{ config('app.name') }}</title>
    <meta name="description" content="Gestiona tu información personal y preferencias.">
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
                        <h1>Mi perfil</h1>
                        <p>Administra tu información personal y preferencias de cuenta.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-perfil-grid">
                        <aside class="paciente-perfil-sidebar-card">
<div class="paciente-perfil-avatar-wrapper">
                                <div class="paciente-perfil-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                <p class="paciente-perfil-name">{{ auth()->user()->name }}</p>
                                <p class="paciente-perfil-email">{{ auth()->user()->email }}</p>
                            </div>
                        </aside>

                        <div class="paciente-perfil-content">
                            <div class="paciente-perfil-section">
                                <h3 class="paciente-perfil-section-title">Información personal</h3>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Nombre completo</label>
                                    <p class="paciente-perfil-field-value">{{ auth()->user()->name }}</p>
                                </div>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Correo electrónico</label>
                                    <p class="paciente-perfil-field-value">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Teléfono</label>
                                    <p class="paciente-perfil-field-value">No registrado</p>
                                </div>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Fecha de nacimiento</label>
                                    <p class="paciente-perfil-field-value">No registrada</p>
                                </div>
                            </div>

                            <div class="paciente-perfil-section">
                                <h3 class="paciente-perfil-section-title">Preferencias</h3>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Notificaciones por email</label>
                                    <p class="paciente-perfil-field-value">Activadas</p>
                                </div>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Recordatorios de cita</label>
                                    <p class="paciente-perfil-field-value">Activados</p>
                                </div>
                                <div class="paciente-perfil-field">
                                    <label class="paciente-perfil-field-label">Idioma</label>
                                    <p class="paciente-perfil-field-value">Español</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="paciente-text-center">Tu información proviene de tu cuenta de usuario. Los datos mostrados son los reales de tu perfil.</p>
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