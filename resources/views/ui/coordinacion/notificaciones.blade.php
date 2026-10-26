<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Notificaciones — {{ config('app.name') }}</title>
    <meta name="description" content="Consulte notificaciones y recordatorios de la clínica.">
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
                        <h1>Notificaciones</h1>
                        <p>Manténgase al tanto de recordatorios y avisos clínicos.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-notificaciones-list">
                        @if (isset($notifications) && $notifications->isNotEmpty())
                            @foreach ($notifications as $notification)
                                <div class="coordinacion-notificacion-item" style="border:1px solid #e5e7eb; padding:1rem; margin-bottom:0.75rem; border-radius:8px; background: {{ is_null($notification->read_at) ? '#f0fdf4' : '#ffffff' }};">
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
                                            <form method="POST" action="{{ route('coordinacion.notificaciones.leer', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="coordinacion-filter-btn">Marcar como leída</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div style="margin-top:1rem;">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="coordinacion-empty-state">
                                <strong>No hay notificaciones recientes</strong>
                                <p>Los avisos de citas y disponibilidad aparecerán aquí cuando existan registros en el sistema.</p>
                            </div>
                        @endif
                    </div>

                    <p class="coordinacion-text-center">Las notificaciones se generan automáticamente según la actividad de la clínica.</p>
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