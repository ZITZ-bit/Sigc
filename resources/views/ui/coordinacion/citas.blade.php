<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Citas y Agenda — {{ config('app.name') }}</title>
    <meta name="description" content="Gestionar citas médicas y agenda de la clínica.">
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
                        <h1>Citas y agenda</h1>
                        <p>Gestione las solicitudes de consulta de los pacientes.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="coordinacion-feedback coordinacion-feedback-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="coordinacion-feedback coordinacion-feedback-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="coordinacion-section">
                    <div class="coordinacion-citas-header">
                        <form method="GET" action="{{ route('coordinacion.citas') }}" class="coordinacion-citas-filters">
                            <select name="estado" class="coordinacion-filter-select">
                                <option value="">Todos los estados</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado }}" {{ $filtro_estado === $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                                @endforeach
                            </select>
                            <input type="search" name="search" class="coordinacion-filter-select" placeholder="Buscar paciente..." value="{{ $search }}">
                            <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Filtrar</button>
                        </form>
                    </div>

                    <div class="coordinacion-citas-list" data-sync-list="coordinacion-citas">
                        @forelse ($consultas as $consulta)
                            <article class="coordinacion-cita-card" data-consulta-id="{{ $consulta->id }}" data-consulta-estado="{{ $consulta->estado }}">
                                <div class="coordinacion-cita-card__header">
                                    <div class="coordinacion-cita-card__when">
                                        <span class="coordinacion-cita-card__date">{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</span>
                                        <span class="coordinacion-cita-card__time">{{ substr($consulta->hora, 0, 5) }}</span>
                                    </div>
                                    <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                </div>

                                <div class="coordinacion-cita-card__details">
                                    <div>
                                        <span class="coordinacion-cita-card__label">Paciente</span>
                                        <strong>{{ $consulta->user->name }}</strong>
                                        <span class="coordinacion-cita-card__muted">{{ $consulta->user->email }}</span>
                                    </div>
                                    <div>
                                        <span class="coordinacion-cita-card__label">Médico</span>
                                        <strong>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</strong>
                                        <span class="coordinacion-cita-card__muted">{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</span>
                                    </div>
                                    <div class="coordinacion-cita-card__reason">
                                        <span class="coordinacion-cita-card__label">Motivo de consulta</span>
                                        <p>{{ $consulta->motivo ?: 'Sin motivo registrado' }}</p>
                                    </div>
                                </div>

                                <div class="coordinacion-cita-card__actions">
                                    @if ($consulta->estado === 'pendiente')
                                        <form method="POST" action="{{ route('coordinacion.citas.asignar-medico', $consulta) }}" class="coordinacion-cita-card__assign-form">
                                            @csrf
                                            <label class="visually-hidden" for="medico-{{ $consulta->id }}">Asignar médico</label>
                                            <select id="medico-{{ $consulta->id }}" name="medico_id" class="coordinacion-medico-select" required>
                                                <option value="">Asignar médico</option>
                                                @foreach ($medicos as $medico)
                                                    <option value="{{ $medico->id }}" {{ old('medico_id', $consulta->medico_id) === $medico->id ? 'selected' : '' }}>{{ $medico->user->name }} ({{ $medico->especialidad?->name }})</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--asignar">Asignar</button>
                                        </form>
                                    @endif

                                    @if ($consulta->estado === 'confirmada')
                                        <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="completada">
                                            <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--complete">Completar</button>
                                        </form>
                                    @elseif ($consulta->estado === 'pendiente')
                                        <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="confirmada">
                                            <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--confirm">Confirmar cita</button>
                                        </form>
                                    @endif

                                    @if (in_array($consulta->estado, ['pendiente', 'confirmada'], true))
                                        <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}" onsubmit="return confirm('¿Cancelar esta cita?')">
                                            @csrf
                                            <input type="hidden" name="estado" value="cancelada">
                                            <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--cancel">Cancelar cita</button>
                                        </form>
                                    @endif

                                    @if (! $consulta->historialClinico)
                                        <form method="POST" action="{{ route('coordinacion.citas.destroy', $consulta) }}" onsubmit="return confirm('¿Eliminar esta cita de forma permanente? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--delete">Eliminar cita</button>
                                        </form>
                                    @else
                                        <span class="coordinacion-cita-card__protected">Protegida por historial clínico</span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="coordinacion-empty-state">
                                <strong>No hay consultas registradas</strong>
                                <p>Cuando los pacientes registren solicitudes de consulta, aparecerán aquí.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="coordinacion-pagination">
                        {{ $consultas->appends(request()->except('page'))->links() }}
                    </div>
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
