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
                        <h1>Horarios de atención</h1>
                        <p>{{ $medico->user->name }} · {{ $medico->especialidad?->name ?? 'Sin especialidad' }}</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <a href="{{ route('coordinacion.medicos.editar', $medico) }}" class="coordinacion-btn-secondary">Editar médico</a>
                        <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-btn-link">Volver a médicos</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="coordinacion-horario-feedback coordinacion-horario-feedback--success coordinacion-mb-2" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="coordinacion-horario-feedback coordinacion-horario-feedback--error coordinacion-mb-2" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="coordinacion-horarios-panel" aria-labelledby="horarios-heading">
                    <div class="coordinacion-horarios-heading">
                        <div>
                            <span class="coordinacion-horarios-eyebrow">Disponibilidad semanal</span>
                            <h2 id="horarios-heading" class="coordinacion-section-title">Horario de atención</h2>
                            <p>Organiza los días y rangos en que {{ $medico->user->name }} puede recibir consultas.</p>
                        </div>
                        <div class="coordinacion-horarios-count" aria-label="{{ $horarios->where('activo', true)->count() }} horarios activos">
                            <strong>{{ $horarios->where('activo', true)->count() }}</strong>
                            <span>activos</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('coordinacion.medicos.horarios.store', $medico) }}" class="coordinacion-horario-create">
                        @csrf
                        <div class="coordinacion-horario-create-heading">
                            <span class="coordinacion-horario-create-icon" aria-hidden="true">+</span>
                            <div>
                                <h3>Agregar disponibilidad</h3>
                                <p>Los horarios del mismo día pueden ser consecutivos, pero no cruzarse.</p>
                            </div>
                        </div>
                        <div class="coordinacion-horario-form-grid">
                            <div class="coordinacion-form-field">
                                <label for="dia_semana" class="coordinacion-form-label">Día de atención</label>
                                <select id="dia_semana" name="dia_semana" class="coordinacion-configuracion-input" required>
                                    @foreach ($diasSemana as $numeroDia => $nombreDia)
                                        <option value="{{ $numeroDia }}" {{ (string) old('dia_semana', now()->dayOfWeek) === (string) $numeroDia ? 'selected' : '' }}>{{ $nombreDia }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="coordinacion-form-field">
                                <label for="hora_inicio" class="coordinacion-form-label">Desde</label>
                                <input id="hora_inicio" type="time" name="hora_inicio" class="coordinacion-configuracion-input" value="{{ old('hora_inicio') }}" required>
                            </div>
                            <div class="coordinacion-form-field">
                                <label for="hora_fin" class="coordinacion-form-label">Hasta</label>
                                <input id="hora_fin" type="time" name="hora_fin" class="coordinacion-configuracion-input" value="{{ old('hora_fin') }}" required>
                            </div>
                            <label class="coordinacion-horario-switch">
                                <input type="hidden" name="activo" value="0">
                                <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                                <span class="coordinacion-horario-switch-track" aria-hidden="true"></span>
                                <span>Dejar activo</span>
                            </label>
                            <button type="submit" class="coordinacion-horario-button coordinacion-horario-button--primary">Agregar horario</button>
                        </div>
                        <p class="coordinacion-horario-note">La hora de fin marca el cierre y no se considera disponible para citas.</p>
                    </form>

                    <div class="coordinacion-horarios-list-heading">
                        <div>
                            <h3>Horarios asignados</h3>
                            <p>{{ $horarios->count() }} {{ $horarios->count() === 1 ? 'rango configurado' : 'rangos configurados' }}</p>
                        </div>
                    </div>

                    @if ($horarios->isEmpty())
                        <div class="coordinacion-horarios-empty">
                            <span class="coordinacion-horarios-empty-icon" aria-hidden="true">◷</span>
                            <strong>Aún no hay horarios</strong>
                            <p>Agrega una disponibilidad para que puedan asignarse citas a este médico.</p>
                        </div>
                    @else
                        <div class="coordinacion-horarios-grid">
                            @foreach ($horarios as $horario)
                                <article class="coordinacion-horario-card {{ $horario->activo ? 'is-active' : 'is-inactive' }}">
                                    <div class="coordinacion-horario-card-heading">
                                        <div>
                                            <span class="coordinacion-horario-day">{{ $diasSemana[$horario->dia_semana] }}</span>
                                            <span class="coordinacion-horario-range">{{ substr($horario->hora_inicio, 0, 5) }} <span aria-hidden="true">—</span> {{ substr($horario->hora_fin, 0, 5) }}</span>
                                        </div>
                                        <span class="coordinacion-horario-status">
                                            <span aria-hidden="true"></span>
                                            {{ $horario->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>

                                    <form id="horario-form-{{ $horario->id }}" method="POST" action="{{ route('coordinacion.medicos.horarios.update', [$medico, $horario]) }}" class="coordinacion-horario-edit-form">
                                        @csrf
                                        @method('PUT')
                                        <div class="coordinacion-horario-form-grid coordinacion-horario-form-grid--edit">
                                            <div class="coordinacion-form-field">
                                                <label for="horario-dia-{{ $horario->id }}" class="coordinacion-form-label">Día</label>
                                                <select id="horario-dia-{{ $horario->id }}" name="dia_semana" class="coordinacion-configuracion-input" required>
                                                    @foreach ($diasSemana as $numeroDia => $nombreDia)
                                                        <option value="{{ $numeroDia }}" {{ $horario->dia_semana == $numeroDia ? 'selected' : '' }}>{{ $nombreDia }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="coordinacion-form-field">
                                                <label for="horario-inicio-{{ $horario->id }}" class="coordinacion-form-label">Desde</label>
                                                <input id="horario-inicio-{{ $horario->id }}" type="time" name="hora_inicio" value="{{ substr($horario->hora_inicio, 0, 5) }}" class="coordinacion-configuracion-input" required>
                                            </div>
                                            <div class="coordinacion-form-field">
                                                <label for="horario-fin-{{ $horario->id }}" class="coordinacion-form-label">Hasta</label>
                                                <input id="horario-fin-{{ $horario->id }}" type="time" name="hora_fin" value="{{ substr($horario->hora_fin, 0, 5) }}" class="coordinacion-configuracion-input" required>
                                            </div>
                                            <label class="coordinacion-horario-switch">
                                                <input type="hidden" name="activo" value="0">
                                                <input type="checkbox" name="activo" value="1" {{ $horario->activo ? 'checked' : '' }}>
                                                <span class="coordinacion-horario-switch-track" aria-hidden="true"></span>
                                                <span>Disponible</span>
                                            </label>
                                        </div>
                                    </form>
                                    <div class="coordinacion-horario-card-actions">
                                        <button type="submit" form="horario-form-{{ $horario->id }}" class="coordinacion-horario-button coordinacion-horario-button--secondary">Guardar horario</button>
                                        @if ($horario->activo)
                                            <form method="POST" action="{{ route('coordinacion.medicos.horarios.destroy', [$medico, $horario]) }}" class="coordinacion-horario-deactivate-form" onsubmit="return confirm('¿Quieres desactivar este horario?');">
                                                @csrf
                                                <button type="submit" class="coordinacion-horario-button coordinacion-horario-button--quiet">Desactivar</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('coordinacion.medicos.horarios.eliminar', [$medico, $horario]) }}" class="coordinacion-horario-delete-form" onsubmit="return confirm('¿Eliminar permanentemente este horario? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="coordinacion-horario-button coordinacion-horario-button--delete">Eliminar</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
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
