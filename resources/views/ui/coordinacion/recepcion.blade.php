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
                                <a href="{{ route('coordinacion.recepcion', ['filtro' => 'todas']) }}" class="coordinacion-filter-btn {{ ($filtro ?? 'todas') === 'todas' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? 'todas') === 'todas' ? 'true' : 'false' }}" data-filtros-link="todas" data-filtros-target="#coordinacion-recepcion">Todas</a>
                                <a href="{{ route('coordinacion.recepcion', ['filtro' => 'hoy']) }}" class="coordinacion-filter-btn {{ ($filtro ?? 'todas') === 'hoy' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? 'todas') === 'hoy' ? 'true' : 'false' }}" data-filtros-link="hoy" data-filtros-target="#coordinacion-recepcion">Hoy</a>
                                <a href="{{ route('coordinacion.recepcion', ['filtro' => 'semana']) }}" class="coordinacion-filter-btn {{ ($filtro ?? '') === 'semana' ? 'coordinacion-filter-btn--active' : '' }}" aria-pressed="{{ ($filtro ?? '') === 'semana' ? 'true' : 'false' }}" data-filtros-link="semana" data-filtros-target="#coordinacion-recepcion">Esta semana</a>
                                @if ($filtro_estado)
                                    <button type="button" class="coordinacion-filter-btn" data-filtros-limpiar data-filtros-target="#coordinacion-recepcion">Limpiar filtros</button>
                                @endif
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
                            <tbody id="coordinacion-recepcion" data-sync-list="coordinacion-recepcion" data-filtros-target>
                                @include('ui.coordinacion.partials.recepcion-lista', [
                                    'consultas' => $consultas,
                                    'filtro' => $filtro,
                                    'filtro_estado' => $filtro_estado,
                                ])
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