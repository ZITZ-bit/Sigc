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
                        <form method="GET" action="{{ route('medico.consultas') }}" class="medico-agenda-filters" data-filtros-form="true" data-filtros-target="#medico-consultas">
                            <select name="estado" class="medico-filter-select" data-filtros-input>
                                <option value="">Todos los estados</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado }}" {{ $filtro_estado === $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                                @endforeach
                            </select>
                            <input type="search" name="search" class="medico-filter-select" placeholder="Buscar paciente..." value="{{ $search }}" data-filtros-input>
                            <button type="button" class="medico-filter-btn medico-filter-btn--clear" data-filtros-limpiar>Limpiar</button>
                        </form>

                        <div id="medico-consultas" data-filtros-target>
                            @include('ui.medico.partials.consultas-lista', [
                                'consultas' => $consultas,
                                'medico' => $medico,
                                'search' => $search,
                                'filtro_estado' => $filtro_estado,
                            ])
                        </div>
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
