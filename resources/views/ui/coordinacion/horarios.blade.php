<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Horarios de {{ $medico->user->name }} — {{ config('app.name') }}</title>
    <meta name="description" content="Gestionar horarios de atención del médico.">
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
                        <h1>Horarios de {{ $medico->user->name }}</h1>
                        <p>Gestione los horarios de atención de {{ $medico->user->name }} ({{ $medico->especialidad?->name ?? 'Sin especialidad' }}).</p>
                    </div>
                    <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-btn-link">Volver a médicos</a>
                </div>

                @if (session('success'))
                    <div class="coordinacion-feedback coordinacion-feedback-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="coordinacion-feedback coordinacion-feedback-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="coordinacion-section">
                    <div class="coordinacion-citas-header">
                        <form method="POST" action="{{ route('coordinacion.medicos.horarios.store', $medico) }}" class="coordinacion-citas-filters">
                            @csrf
                            <select name="dia_semana" class="coordinacion-filter-select">
                                <option value="0">Domingo</option>
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miércoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sábado</option>
                            </select>
                            <input type="time" name="hora_inicio" class="coordinacion-filter-select" required>
                            <input type="time" name="hora_fin" class="coordinacion-filter-select" required>
                            <label class="coordinacion-filter-checkbox">
                                <input type="checkbox" name="activo" value="1" checked>
                                Activo
                            </label>
                            <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Agregar horario</button>
                        </form>
                    </div>

                    <table class="coordinacion-citas-table">
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
                                        <div class="coordinacion-empty-state">
                                            <strong>No hay horarios registrados</strong>
                                            <p>Cuando se registren horarios, aparecerán aquí para gestionar la disponibilidad del médico.</p>
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
                                            <span class="coordinacion-badge coordinacion-badge--{{ $horario->activo ? 'activo' : 'inactivo' }}">
                                                {{ $horario->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="coordinacion-citas-actions">
                                                <form method="POST" action="{{ route('coordinacion.medicos.horarios.update', [$medico, $horario]) }}" style="display:inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="dia_semana" class="coordinacion-estado-select">
                                                        <option value="0" {{ $horario->dia_semana == 0 ? 'selected' : '' }}>Domingo</option>
                                                        <option value="1" {{ $horario->dia_semana == 1 ? 'selected' : '' }}>Lunes</option>
                                                        <option value="2" {{ $horario->dia_semana == 2 ? 'selected' : '' }}>Martes</option>
                                                        <option value="3" {{ $horario->dia_semana == 3 ? 'selected' : '' }}>Miércoles</option>
                                                        <option value="4" {{ $horario->dia_semana == 4 ? 'selected' : '' }}>Jueves</option>
                                                        <option value="5" {{ $horario->dia_semana == 5 ? 'selected' : '' }}>Viernes</option>
                                                        <option value="6" {{ $horario->dia_semana == 6 ? 'selected' : '' }}>Sábado</option>
                                                    </select>
                                                    <input type="time" name="hora_inicio" value="{{ substr($horario->hora_inicio, 0, 5) }}" class="coordinacion-estado-select" style="width: 80px;">
                                                    <input type="time" name="hora_fin" value="{{ substr($horario->hora_fin, 0, 5) }}" class="coordinacion-estado-select" style="width: 80px;">
                                                    <label class="coordinacion-filter-checkbox" style="margin:0;">
                                                        <input type="checkbox" name="activo" value="1" {{ $horario->activo ? 'checked' : '' }}>
                                                        Activo
                                                    </label>
                                                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--view">Guardar</button>
                                                </form>
                                                <form method="POST" action="{{ route('coordinacion.medicos.horarios.destroy', [$medico, $horario]) }}" style="display:inline; margin-left:0.5rem;">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--view" onclick="return confirm('¿Está seguro de que desea desactivar este horario?')">Desactivar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
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
