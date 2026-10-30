<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Reportes y Estadísticas — {{ config('app.name') }}</title>
    <meta name="description" content="Reportes y estadísticas de la clínica.">
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
                        <h1>Reportes y estadísticas</h1>
                        <p>Consulte indicadores y reportes de la clínica.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <form method="GET" action="{{ route('coordinacion.reportes') }}" class="coordinacion-reportes-filters">
                        <label>Desde <input type="date" name="desde" value="{{ $desde ?? '' }}"></label>
                        <label>Hasta <input type="date" name="hasta" value="{{ $hasta ?? '' }}"></label>
                        <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Filtrar</button>
                    </form>

                    <div class="coordinacion-reportes-grid">
                        <div class="coordinacion-reporte-card coordinacion-reporte-card--featured">
                            <span class="coordinacion-reporte-title">Total de consultas</span>
                            <strong class="coordinacion-reporte-value">{{ number_format($totales['total'] ?? 0) }}</strong>
                            <span class="coordinacion-reporte-caption">En el periodo seleccionado</span>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Pendientes</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--warning">{{ number_format($totales['pendiente'] ?? 0) }}</strong>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Confirmadas</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--forest">{{ number_format($totales['confirmada'] ?? 0) }}</strong>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Completadas</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--sage">{{ number_format($totales['completada'] ?? 0) }}</strong>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Canceladas</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--error">{{ number_format($totales['cancelada'] ?? 0) }}</strong>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Pacientes registrados</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--mint">{{ number_format($pacientesActivos ?? 0) }}</strong>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Médicos activos</span>
                            <strong class="coordinacion-reporte-value coordinacion-reporte-value--mint">{{ number_format($medicosActivos ?? 0) }}</strong>
                        </div>
                    </div>

                    <div class="coordinacion-reportes-charts">
                        <article class="coordinacion-reporte-chart-card coordinacion-reporte-chart-card--wide">
                            <div class="coordinacion-reporte-chart-heading">
                                <div>
                                    <span class="coordinacion-reporte-eyebrow">Tendencia</span>
                                    <h2>Consultas a lo largo del tiempo</h2>
                                </div>
                                <span class="coordinacion-reporte-chart-icon" aria-hidden="true"><i data-lucide="chart-no-axes-combined"></i></span>
                            </div>
                            <p class="coordinacion-reporte-chart-description">Volumen mensual de consultas; muestra hasta los últimos 12 meses del periodo seleccionado.</p>
                            @php
                                $maxConsultas = max($tendenciaMensual->max('total') ?? 0, 1);
                                $cantidadMeses = $tendenciaMensual->count();
                            @endphp
                            <div class="coordinacion-reporte-line-chart">
                                <svg viewBox="0 0 800 250" preserveAspectRatio="none" role="img" aria-label="Gráfica de línea de consultas mensuales" data-values="{{ $tendenciaMensual->pluck('total')->implode(',') }}">
                                    <defs>
                                        <linearGradient id="consultas-area-gradient" x1="0" x2="0" y1="0" y2="1">
                                            <stop offset="0%" stop-color="var(--coordinacion-color-forest)" stop-opacity="0.22"></stop>
                                            <stop offset="100%" stop-color="var(--coordinacion-color-forest)" stop-opacity="0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <line x1="56" y1="35" x2="744" y2="35" class="coordinacion-chart-gridline"></line>
                                    <line x1="56" y1="85" x2="744" y2="85" class="coordinacion-chart-gridline"></line>
                                    <line x1="56" y1="135" x2="744" y2="135" class="coordinacion-chart-gridline"></line>
                                    <line x1="56" y1="185" x2="744" y2="185" class="coordinacion-chart-gridline"></line>
                                    @if ($cantidadMeses > 0)
                                        @php
                                            $puntos = [];
                                            foreach ($tendenciaMensual as $index => $item) {
                                                $x = $cantidadMeses > 1 ? 56 + (688 * $index / ($cantidadMeses - 1)) : 400;
                                                $y = 185 - (140 * $item['total'] / $maxConsultas);
                                                $puntos[] = number_format($x, 2, '.', '').','.number_format($y, 2, '.', '');
                                            }
                                            $puntosSvg = implode(' ', $puntos);
                                            $primerPuntoX = $cantidadMeses > 1 ? 56 : 400;
                                            $ultimoPuntoX = $cantidadMeses > 1 ? 744 : 400;
                                        @endphp
                                        <polygon points="{{ $primerPuntoX }},185 {{ $puntosSvg }} {{ $ultimoPuntoX }},185" class="coordinacion-chart-area"></polygon>
                                        <polyline points="{{ $puntosSvg }}" class="coordinacion-chart-line"></polyline>
                                    @endif
                                    @foreach ($tendenciaMensual as $index => $item)
                                        @php
                                            $x = $cantidadMeses > 1 ? 56 + (688 * $index / ($cantidadMeses - 1)) : 400;
                                            $y = 185 - (140 * $item['total'] / $maxConsultas);
                                        @endphp
                                        <circle cx="{{ $x }}" cy="{{ $y }}" r="5" class="coordinacion-chart-point">
                                            <title>{{ $item['label'] }}: {{ $item['total'] }} consultas</title>
                                        </circle>
                                    @endforeach
                                </svg>
                                <div class="coordinacion-reporte-chart-labels" style="--chart-month-count: {{ max($cantidadMeses, 1) }}" aria-hidden="true">
                                    @foreach ($tendenciaMensual as $item)
                                        <span>{{ $item['label'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </article>

                        <article class="coordinacion-reporte-chart-card">
                            <div class="coordinacion-reporte-chart-heading">
                                <div>
                                    <span class="coordinacion-reporte-eyebrow">Distribución</span>
                                    <h2>Consultas por estado</h2>
                                </div>
                                <span class="coordinacion-reporte-chart-icon coordinacion-reporte-chart-icon--bars" aria-hidden="true"><i data-lucide="chart-no-axes-combined"></i></span>
                            </div>
                            <p class="coordinacion-reporte-chart-description">Comparativo de estados en el periodo elegido.</p>
                            @php
                                $maxEstado = max(collect($consultasPorEstado)->max('total'), 1);
                            @endphp
                            <div class="coordinacion-reporte-bars" role="img" aria-label="Gráfica de barras de consultas por estado" data-values="{{ collect($consultasPorEstado)->pluck('total')->implode(',') }}">
                                @foreach ($consultasPorEstado as $estado)
                                    <div class="coordinacion-reporte-bar-row">
                                        <div class="coordinacion-reporte-bar-meta">
                                            <span>{{ $estado['label'] }}</span>
                                            <strong>{{ number_format($estado['total']) }}</strong>
                                        </div>
                                        <div class="coordinacion-reporte-bar-track">
                                            <span class="coordinacion-reporte-bar coordinacion-reporte-bar--{{ $estado['class'] }}" style="--bar-width: {{ $estado['total'] / $maxEstado * 100 }}%"></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    </div>

                    <div class="coordinacion-reportes-details">
                        <article class="coordinacion-reporte-detail-card">
                            <h2>Consultas por especialidad</h2>
                            <ul>
                                @forelse (($porEspecialidad ?? collect()) as $item)
                                    <li>{{ $item['especialidad'] }}: {{ $item['total'] }}</li>
                                @empty
                                    <li>Sin datos</li>
                                @endforelse
                            </ul>
                        </article>
                        <article class="coordinacion-reporte-detail-card">
                            <h2>Consultas por médico (asignadas)</h2>
                            <ul>
                                @forelse (($porMedico ?? collect()) as $item)
                                    <li>{{ $item['medico'] }}: {{ $item['total'] }}</li>
                                @empty
                                    <li>Sin datos</li>
                                @endforelse
                            </ul>
                        </article>
                    </div>

                    <p class="coordinacion-reportes-disclaimer">Los reportes y gráficas se generan a partir de los datos reales de consultas registradas.</p>
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