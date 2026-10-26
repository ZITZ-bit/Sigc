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
                    <form method="GET" action="{{ route('coordinacion.reportes') }}" style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap; margin-bottom:1rem;">
                        <label>Desde <input type="date" name="desde" value="{{ $desde ?? '' }}"></label>
                        <label>Hasta <input type="date" name="hasta" value="{{ $hasta ?? '' }}"></label>
                        <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Filtrar</button>
                        <a href="{{ route('coordinacion.reportes') }}" class="coordinacion-filter-btn">Limpiar</a>
                    </form>

                    <div class="coordinacion-reportes-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Total de consultas</strong>
                            <p style="font-size:1.5rem; margin:0.25rem 0;">{{ $totales['total'] ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Pendiente</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $totales['pendiente'] ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Confirmada</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $totales['confirmada'] ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Completada</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $totales['completada'] ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Cancelada</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $totales['cancelada'] ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Pacientes registrados</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $pacientesActivos ?? 0 }}</p>
                        </div>
                        <div class="coordinacion-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Médicos activos</strong>
                            <p style="font-size:1.25rem; margin:0.25rem 0;">{{ $medicosActivos ?? 0 }}</p>
                        </div>
                    </div>

                    <div style="margin-top:1.5rem; display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1rem;">
                        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Consultas por especialidad</strong>
                            <ul style="margin:0.5rem 0 0; padding-left:1.25rem;">
                                @forelse (($porEspecialidad ?? collect()) as $item)
                                    <li>{{ $item['especialidad'] }}: {{ $item['total'] }}</li>
                                @empty
                                    <li>Sin datos</li>
                                @endforelse
                            </ul>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:1rem;">
                            <strong>Consultas por médico (asignadas)</strong>
                            <ul style="margin:0.5rem 0 0; padding-left:1.25rem;">
                                @forelse (($porMedico ?? collect()) as $item)
                                    <li>{{ $item['medico'] }}: {{ $item['total'] }}</li>
                                @empty
                                    <li>Sin datos</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <p class="coordinacion-text-center" style="margin-top:1rem;">Los reportes se generan a partir de los datos reales de consultas registradas.</p>
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