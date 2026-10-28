<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Recepción y Seguimiento — {{ config('app.name') }}</title>
    <meta name="description" content="Gestión de recepción y seguimiento de pacientes en la clínica.">
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
                        <h1>Recepción y seguimiento</h1>
                        <p>Controle la llegada y asistencia de pacientes en tiempo real.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-recepcion-header">
                        <div class="coordinacion-recepcion-filters">
                            <a href="{{ route('coordinacion.recepcion', ['filtro' => 'todas']) }}" class="coordinacion-filter-btn {{ ($filtro ?? 'todas') === 'todas' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? 'todas') === 'todas' ? 'true' : 'false' }}">Todas</a>
                            <a href="{{ route('coordinacion.recepcion', ['filtro' => 'hoy']) }}" class="coordinacion-filter-btn {{ ($filtro ?? 'todas') === 'hoy' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? 'todas') === 'hoy' ? 'true' : 'false' }}">Hoy</a>
                            <a href="{{ route('coordinacion.recepcion', ['filtro' => 'semana']) }}" class="coordinacion-filter-btn {{ ($filtro ?? '') === 'semana' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? '') === 'semana' ? 'true' : 'false' }}">Esta semana</a>
                        </div>
                    </div>

                    <table class="coordinacion-recepcion-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Especialidad</th>
                                <th>Médico asignado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($consultas) && $consultas->isNotEmpty())
                                @foreach ($consultas as $consulta)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $consulta->hora }}</td>
                                        <td>{{ $consulta->user->name ?? 'N/A' }}</td>
                                        <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
                                        <td>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</td>
                                        <td>
                                            <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        <div class="coordinacion-empty-state">
                                            <strong>No hay pacientes en recepción</strong>
                                            <p>Cuando haya citas programadas para el periodo seleccionado, aparecerán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <p class="coordinacion-text-center">La recepción se actualiza a partir de las consultas registradas en el sistema.</p>
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