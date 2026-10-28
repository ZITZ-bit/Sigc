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

                @if (session('success'))
                    <div class="paciente-agenda-feedback paciente-agenda-feedback--success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="paciente-agenda-feedback paciente-agenda-feedback--error" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="paciente-section">
                    <div class="paciente-agenda-header">
                        <div>
                            <h2 class="paciente-agenda-heading">Tus citas</h2>
                            <p>Revisa los detalles y administra tus solicitudes.</p>
                        </div>
                        <a href="{{ route('paciente.consulta') }}" class="paciente-btn-primary">Solicitar consulta</a>
                    </div>

                    <div class="paciente-agenda-list" data-sync-list="paciente-agenda">
                        @if ($consultas->isEmpty())
                            <div class="paciente-empty-state">
                                <strong>No tienes citas programadas</strong>
                                <p>Cuando se registren citas, verás aquí la fecha, el profesional y el estado.</p>
                            </div>
                        @else
                            @foreach ($consultas as $consulta)
                                <article class="paciente-agenda-item" data-consulta-id="{{ $consulta->id }}" data-consulta-estado="{{ $consulta->estado }}">
                                    <div class="paciente-agenda-date-time" aria-label="Fecha y hora de la consulta">
                                        <span class="paciente-agenda-month">{{ \Carbon\Carbon::parse($consulta->fecha)->locale('es')->translatedFormat('M') }}</span>
                                        <span class="paciente-agenda-date">{{ \Carbon\Carbon::parse($consulta->fecha)->format('d') }}</span>
                                        <span class="paciente-agenda-time">{{ substr($consulta->hora, 0, 5) }}</span>
                                    </div>
                                    <div class="paciente-agenda-info">
                                        <span class="paciente-agenda-doctor">{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</span>
                                        <span class="paciente-agenda-specialty">{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</span>
                                        @if ($consulta->motivo)
                                            <span class="paciente-agenda-motivo"><strong>Motivo:</strong> {{ $consulta->motivo }}</span>
                                        @endif
                                    </div>
                                    <div class="paciente-agenda-side">
                                        <span class="paciente-badge paciente-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                        <div class="paciente-agenda-actions">
                                            @if (in_array($consulta->estado, ['pendiente', 'confirmada'], true))
                                                <form method="POST" action="{{ route('paciente.consultas.cancelar', $consulta) }}" onsubmit="return confirm('¿Cancelar esta cita?')">
                                                    @csrf
                                                    <button type="submit" class="paciente-agenda-btn paciente-agenda-btn--cancel">Cancelar</button>
                                                </form>
                                            @endif
                                            @if (! $consulta->historialClinico)
                                                <form method="POST" action="{{ route('paciente.consultas.destroy', $consulta) }}" onsubmit="return confirm('¿Eliminar esta cita de forma permanente? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="paciente-agenda-btn paciente-agenda-btn--delete">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="paciente-agenda-protected" title="Esta cita tiene un historial clínico asociado">Con historial clínico</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        @endif
                    </div>

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