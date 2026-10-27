<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Horario — {{ config('app.name') }}</title>
    <meta name="description" content="Gestionar horarios de atención del médico.">
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
                        <h1>Mi horario</h1>
                        <p>Consulte sus horarios de atención asignados por coordinación.</p>
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
                        <table class="medico-agenda-table">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora inicial</th>
                                    <th>Hora final</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($horarios->isEmpty())
                                    <tr>
                                        <td colspan="4">
                                            <div class="medico-empty-state">
                                                <strong>No hay horarios registrados</strong>
                                                <p>Cuando coordinación registre sus horarios, aparecerán aquí.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($horarios as $horario)
                                        <tr>
                                            <td>{{ $diasSemana[$horario->dia_semana] }}</td>
                                            <td>{{ substr($horario->hora_inicio, 0, 5) }}</td>
                                            <td>{{ substr($horario->hora_fin, 0, 5) }}</td>
                                            <td>
                                                <span class="medico-badge medico-badge--{{ $horario->activo ? 'activo' : 'inactivo' }}">
                                                    {{ $horario->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </section>
                @endif

                <p class="medico-text-center">Los horarios se gestionan desde la coordinación de la clínica.</p>
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
