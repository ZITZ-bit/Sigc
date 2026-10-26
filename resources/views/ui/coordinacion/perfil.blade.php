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
    @php
        $coordinador = auth()->user();
    @endphp
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
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-perfil-grid">
                        <aside class="coordinacion-perfil-sidebar-card">
                            <div class="coordinacion-perfil-avatar-wrapper">
                                <div class="coordinacion-perfil-avatar" aria-hidden="true">{{ strtoupper(substr($coordinador->name, 0, 1)) }}</div>
                                <p class="coordinacion-perfil-name">{{ $coordinador->name }}</p>
                                <span class="coordinacion-perfil-role">{{ $coordinador->role->name ?? 'Sin rol asignado' }}</span>
                                <p class="coordinacion-perfil-email">{{ $coordinador->email }}</p>
                            </div>
                        </aside>

                        <div class="coordinacion-perfil-content">
                            <div class="coordinacion-perfil-section">
                                <h3 class="coordinacion-perfil-section-title">Información de cuenta</h3>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Nombre y apellidos</label>
                                    <p class="coordinacion-perfil-field-value">{{ $coordinador->name }}</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Correo electrónico</label>
                                    <p class="coordinacion-perfil-field-value">{{ $coordinador->email }}</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Rol</label>
                                    <p class="coordinacion-perfil-field-value">{{ $coordinador->role->name ?? 'Sin rol asignado' }}</p>
                                </div>
                                <div class="coordinacion-perfil-field">
                                    <label class="coordinacion-perfil-field-label">Fecha de registro</label>
                                    <p class="coordinacion-perfil-field-value">{{ $coordinador->created_at?->format('d/m/Y') ?? 'No registrada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="coordinacion-text-center">Tu información proviene de tu cuenta de usuario en el sistema.</p>
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