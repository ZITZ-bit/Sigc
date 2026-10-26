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
                        <p>Consulte y gestione sus horarios de atención.</p>
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
                        <div class="medico-agenda-header">
                            <div class="medico-agenda-filters">
                                <form method="POST" action="{{ route('coordinacion.medicos.horarios.store', $medico) }}" class="medico-agenda-filters">
                                    @csrf
                                    <select name="dia_semana" class="medico-filter-select">
                                        <option value="0">Domingo</option>
                                        <option value="1">Lunes</option>
                                        <option value="2">Martes</option>
                                        <option value="3">Miércoles</option>
                                        <option value="4">Jueves</option>
                                        <option value="5">Viernes</option>
                                        <option value="6">Sábado</option>
                                    </select>
                                    <input type="time" name="hora_inicio" class="medico-filter-select" required>
                                    <input type="time" name="hora_fin" class="medico-filter-select" required>
                                    <label class="medico-filter-checkbox">
                                        <input type="checkbox" name="activo" value="1" checked>
                                        Activo
                                    </label>
                                    <button type="submit" class="medico-filter-btn medico-filter-btn--active">Agregar horario</button>
                                </form>
                            </div>
                        </div>

                        <table class="medico-agenda-table">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora inicial</th>
                                    <th>Hora final</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($horarios->isEmpty())
                                    <tr>
                                        <td colspan="5">
                                            <div class="medico-empty-state">
                                                <strong>No hay horarios registrados</strong>
                                                <p>Cuando se registren horarios, aparecerán aquí para gestionar su disponibilidad.</p>
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
                                            <td>
                                                <div class="medico-agenda-actions">
                                                    <form method="POST" action="{{ route('coordinacion.medicos.horarios.update', [$medico, $horario]) }}" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="dia_semana" class="medico-estado-select">
                                                            <option value="0" {{ $horario->dia_semana == 0 ? 'selected' : '' }}>Domingo</option>
                                                            <option value="1" {{ $horario->dia_semana == 1 ? 'selected' : '' }}>Lunes</option>
                                                            <option value="2" {{ $horario->dia_semana == 2 ? 'selected' : '' }}>Martes</option>
                                                            <option value="3" {{ $horario->dia_semana == 3 ? 'selected' : '' }}>Miércoles</option>
                                                            <option value="4" {{ $horario->dia_semana == 4 ? 'selected' : '' }}>Jueves</option>
                                                            <option value="5" {{ $horario->dia_semana == 5 ? 'selected' : '' }}>Viernes</option>
                                                            <option value="6" {{ $horario->dia_semana == 6 ? 'selected' : '' }}>Sábado</option>
                                                        </select>
                                                        <input type="time" name="hora_inicio" value="{{ substr($horario->hora_inicio, 0, 5) }}" class="medico-estado-select" style="width: 80px;">
                                                        <input type="time" name="hora_fin" value="{{ substr($horario->hora_fin, 0, 5) }}" class="medico-estado-select" style="width: 80px;">
                                                        <label class="medico-filter-checkbox" style="margin:0;">
                                                            <input type="checkbox" name="activo" value="1" {{ $horario->activo ? 'checked' : '' }}>
                                                            Activo
                                                        </label>
                                                        <button type="submit" class="medico-agenda-btn medico-agenda-btn--view">Guardar</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('coordinacion.medicos.horarios.destroy', [$medico, $horario]) }}" style="display:inline; margin-left:0.5rem;">
                                                        @csrf
                                                        @method('POST')
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="medico-agenda-btn coordinacion-citas-btn--view" onclick="return confirm('¿Está seguro de que desea desactivar este horario?')">Desactivar</button>
                                                    </form>
                                                </div>
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
