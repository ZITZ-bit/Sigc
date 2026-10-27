<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Buscar Doctores — {{ config('app.name') }}</title>
    <meta name="description" content="Directorio de profesionales médicos disponibles.">
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
                        <h1>Buscar doctores</h1>
                        <p>Encuentra al profesional adecuado para tu atención.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <form method="GET" action="{{ route('paciente.doctores') }}" class="paciente-doctores-header">
                        <div class="paciente-doctores-search">
                            <input type="search" name="search" class="paciente-consulta-input" placeholder="Buscar por nombre o especialidad..." aria-label="Buscar doctores" value="{{ $search }}">
                        </div>
                        <button type="submit" class="paciente-btn-primary">Buscar</button>
                    </form>

                    <div class="paciente-doctores-grid">
                        @forelse ($medicos as $medico)
                            <article class="paciente-doctor-card">
                                <div class="paciente-doctor-avatar" aria-hidden="true">{{ strtoupper(substr($medico->user->name ?? 'M', 0, 1)) }}</div>
                                <p class="paciente-doctor-name">{{ $medico->user->name ?? 'Médico sin nombre' }}</p>
                                <span class="paciente-doctor-specialty">{{ $medico->especialidad?->name ?? 'Sin especialidad asignada' }}</span>
                                <p class="paciente-doctor-bio">{{ $medico->departamento?->name ?? 'Departamento no asignado' }}</p>
                                <a href="{{ route('paciente.consulta') }}" class="paciente-doctor-btn">Solicitar consulta</a>
                            </article>
                        @empty
                            <div class="paciente-empty-state">
                                <strong>No hay profesionales disponibles</strong>
                                <p>Los médicos activos aparecerán aquí cuando estén disponibles.</p>
                            </div>
                        @endforelse
                    </div>

                    <p class="paciente-text-center">Utiliza "Solicitar consulta" para iniciar una solicitud de atención médica.</p>
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