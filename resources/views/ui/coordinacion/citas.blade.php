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
                            <input type="date" name="fecha" class="coordinacion-filter-select" value="{{ $filtro_fecha }}">
                            <input type="search" name="search" class="coordinacion-filter-select" placeholder="Buscar paciente..." value="{{ $search }}">
                            <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Filtrar</button>
                            <a href="{{ route('coordinacion.citas') }}" class="coordinacion-filter-btn">Limpiar</a>
                        </form>
                    </div>

                    <table class="coordinacion-citas-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Especialidad</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($consultas->isEmpty())
                                <tr>
                                    <td colspan="8">
                                        <div class="coordinacion-empty-state">
                                            <strong>No hay consultas registradas</strong>
                                            <p>Cuando los pacientes registren solicitudes de consulta, aparecerán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($consultas as $consulta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $consulta->hora }}</td>
                                        <td>{{ $consulta->user->name }}</td>
                                        <td>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</td>
                                        <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
                                        <td>{{ $consulta->motivo ?: 'Sin motivo registrado' }}</td>
                                        <td>
                                            <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                        </td>
                                        <td>
                                            <div class="coordinacion-citas-actions">
                                                <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}" style="display:inline;">
                                                    @csrf
                                                    <select name="estado" class="coordinacion-estado-select" onchange="this.form.submit()">
                                                        @php
                                                            $transiciones = [
                                                                'pendiente' => ['confirmada', 'cancelada'],
                                                                'confirmada' => ['completada', 'cancelada'],
                                                                'completada' => [],
                                                                'cancelada' => [],
                                                            ];
                                                            $permitidas = $transiciones[$consulta->estado] ?? [];
                                                        @endphp
                                                        @foreach ($permitidas as $estadoDestino)
                                                            <option value="{{ $estadoDestino }}" {{ old('estado', $consulta->estado) === $estadoDestino ? 'selected' : '' }}>{{ ucfirst($estadoDestino) }}</option>
                                                        @endforeach
                                                        @if (count($permitidas) === 0)
                                                            <option disabled>Sin transiciones</option>
                                                        @endif
                                                    </select>
                                                </form>
                                                @if ($consulta->estado === 'pendiente')
                                                    <form method="POST" action="{{ route('coordinacion.citas.asignar-medico', $consulta) }}" style="display:inline; margin-left:0.5rem;">
                                                        @csrf
                                                        <select name="medico_id" class="coordinacion-medico-select">
                                                            <option value="">Asignar médico</option>
                                                            @foreach ($medicos as $medico)
                                                                <option value="{{ $medico->id }}" {{ old('medico_id', $consulta->medico_id) === $medico->id ? 'selected' : '' }}>{{ $medico->user->name }} ({{ $medico->especialidad?->name }})</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--asignar">Asignar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    {{ $consultas->appends(request()->except('page'))->links() }}
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
