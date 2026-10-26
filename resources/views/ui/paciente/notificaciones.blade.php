<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Notificaciones — {{ config('app.name') }}</title>
    <meta name="description" content="Consulta tus notificaciones y recordatorios médicos.">
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
                        <h1>Notificaciones</h1>
                        <p>Mantente al tanto de tus recordatorios y avisos.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-notificaciones-list">
                        @if (isset($notifications) && $notifications->isNotEmpty())
                            @foreach ($notifications as $notification)
                                <div class="paciente-notificacion-item" style="border:1px solid #e5e7eb; padding:1rem; margin-bottom:0.75rem; border-radius:8px; background: {{ is_null($notification->read_at) ? '#f0fdf4' : '#ffffff' }};">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem;">
                                        <div>
                                            <strong>{{ $notification->data['message'] ?? 'Notificación' }}</strong>
                                            <p style="margin:0.25rem 0 0; color:#6b7280; font-size:0.9rem;">
                                                {{ $notification->created_at->diffForHumans() }}
                                                @if (!is_null($notification->read_at))
                                                    · Leída
                                                @else
                                                    · Sin leer
                                                @endif
                                            </p>
                                        </div>
                                        @if (is_null($notification->read_at))
                                            <form method="POST" action="{{ route('paciente.notificaciones.leer', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="paciente-filter-btn">Marcar como leída</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div style="margin-top:1rem;">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="paciente-empty-state">
                                <strong>No tienes notificaciones recientes</strong>
                                <p>Los recordatorios y avisos sobre tu atención aparecerán aquí.</p>
                            </div>
                        @endif
                    </div>

                    <p class="paciente-text-center">Las notificaciones se generan automáticamente según el estado de tus solicitudes de consulta.</p>
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