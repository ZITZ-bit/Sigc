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
    @php
        $medico = auth()->user()->medico;
    @endphp
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
                </div>

                <section class="medico-section">
                    <div class="medico-perfil-grid">
                        <aside class="medico-perfil-sidebar-card">
                            <div class="medico-perfil-avatar-wrapper">
                                <div class="medico-perfil-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                <p class="medico-perfil-name">{{ auth()->user()->name }}</p>
                                <span class="medico-perfil-specialty">{{ $medico?->especialidad?->name ?? 'Sin especialidad asignada' }}</span>
                                <p class="medico-perfil-email">{{ auth()->user()->email }}</p>
                            </div>
                        </aside>

                        <div class="medico-perfil-content">
                            <div class="medico-perfil-section">
                                <h3 class="medico-perfil-section-title">Información profesional</h3>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Nombre y apellidos</label>
                                    <p class="medico-perfil-field-value">{{ auth()->user()->name }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Especialidad</label>
                                    <p class="medico-perfil-field-value">{{ $medico?->especialidad?->name ?? 'Sin especialidad asignada' }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Número de colegiado</label>
                                    <p class="medico-perfil-field-value">{{ $medico?->numero_licencia ?? 'No registrado' }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Correo electrónico</label>
                                    <p class="medico-perfil-field-value">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Teléfono</label>
                                    <p class="medico-perfil-field-value">{{ $medico?->telefono ?? 'No registrado' }}</p>
                                </div>
                            </div>

                            <div class="medico-perfil-section">
                                <h3 class="medico-perfil-section-title">Resumen profesional</h3>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Estado de cuenta</label>
                                    <p class="medico-perfil-field-value">{{ $medico?->estado ?? 'Sin estado' }}</p>
                                </div>
                                <div class="medico-perfil-field">
                                    <label class="medico-perfil-field-label">Fecha de incorporación</label>
                                    <p class="medico-perfil-field-value">{{ $medico?->fecha_incorporacion ?? 'No registrada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="medico-text-center">Tu información proviene de tu cuenta de usuario y el registro médico asociado.</p>
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