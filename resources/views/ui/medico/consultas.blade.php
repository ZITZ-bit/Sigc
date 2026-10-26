<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Consultas Médicas — {{ config('app.name') }}</title>
    <meta name="description" content="Gestione sus consultas médicas asignadas.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/medico.css', 'resources/js/app.js'])
</head>
<body>

    <div class="medico-page">
        @include('portal::medico.sidebar')

        <div class="medico-main">
            <div class="medico-content-area">
                <div class="medico-header">
                    <div class="medico-header-content">
                        <h1>Consultas médicas</h1>
                        <p>Registre y gestione las consultas asignadas a su consultorio.</p>
                    </div>
                </div>

                @if (!$medico)
                    <section class="medico-section">
                        <div class="medico-empty-state">
                            <strong>No tiene registro de médico asociado</strong>
                            <p>Su usuario no cuenta con un perfil médico registrado. Contacte a coordinación para asignarle una especialidad y consultorio.</p>
                        </div>
                    </section>
                @else
                    <section class="medico-section">
                        <form method="GET" action="{{ route('medico.consultas') }}" class="medico-agenda-filters">
                            <select name="estado" class="medico-filter-select">
                                <option value="">Todos los estados</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado }}" {{ $filtro_estado === $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                                @endforeach
                            </select>
                            <input type="search" name="search" class="medico-filter-select" placeholder="Buscar paciente..." value="{{ $search }}">
                            <button type="submit" class="medico-filter-btn medico-filter-btn--active">Filtrar</button>
                            <a href="{{ route('medico.consultas') }}" class="medico-filter-btn">Limpiar</a>
                        </form>

                        <table class="medico-agenda-table">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Especialidad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($consultas->isEmpty())
                                    <tr>
                                        <td colspan="6">
                                            <div class="medico-empty-state">
                                                <strong>No tiene consultas asignadas</strong>
                                                <p>Cuando los pacientes le asignen una consulta, aparecerá aquí con fecha, hora y estado.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($consultas as $consulta)
                                        <tr>
                                            <td>{{ $consulta->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $consulta->hora }}</td>
                                            <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
                                            <td>
                                                <span class="medico-badge medico-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                            </td>
                                            <td>
                                                <div class="medico-agenda-actions">
                                                    @if ($consulta->estado === 'confirmada')
                                                        <form method="POST" action="{{ route('medico.consultas.completar', $consulta) }}" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="medico-agenda-btn medico-agenda-btn--completar">Completar</button>
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
                @endif

                <p class="medico-text-center">Las consultas se crean a partir de las solicitudes de los pacientes.</p>
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
