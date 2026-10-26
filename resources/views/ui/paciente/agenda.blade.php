<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Agenda — {{ config('app.name') }}</title>
    <meta name="description" content="Gestiona tus citas médicas pendientes y anteriores.">
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
                        <h1>Mi agenda</h1>
                        <p>Consulta y gestiona tus citas médicas programadas.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-agenda-header">
                        <div class="paciente-agenda-filters">
                            <button class="paciente-filter-btn paciente-filter-btn--active" aria-pressed="true">Próximas</button>
                            <button class="paciente-filter-btn" aria-pressed="false">Anteriores</button>
                        </div>
                    </div>

                    <div class="paciente-agenda-list">
                        @if ($consultas->isEmpty())
                            <div class="paciente-empty-state">
                                <strong>No tienes citas programadas</strong>
                                <p>Cuando se registren citas, verás aquí la fecha, el profesional y el estado.</p>
                            </div>
                        @else
                            @foreach ($consultas as $consulta)
                                <div class="paciente-agenda-item">
                                    <div class="paciente-agenda-date-time">
                                        <span class="paciente-agenda-date">{{ \Carbon\Carbon::parse($consulta->fecha)->format('d M') }}</span>
                                        <span class="paciente-agenda-time">{{ $consulta->hora }}</span>
                                    </div>
                                    <div class="paciente-agenda-info">
                                        <span class="paciente-agenda-doctor">{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</span>
                                        <span class="paciente-agenda-specialty">{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</span>
                                        @if ($consulta->motivo)
                                            <span class="paciente-agenda-motivo">{{ $consulta->motivo }}</span>
                                        @endif
                                    </div>
                                    <span class="paciente-badge paciente-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    @if ($consultas->isEmpty())
                        <p class="paciente-text-center">
                            <a href="{{ route('paciente.consulta') }}" class="paciente-btn-primary">Solicitar consulta</a>
                        </p>
                    @else
                        <p class="paciente-text-center">Tus consultas se muestran ordenadas por fecha y hora.</p>
                    @endif
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