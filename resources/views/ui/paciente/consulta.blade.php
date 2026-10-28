<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Solicitar consulta — {{ config('app.name') }}</title>
    <meta name="description" content="Elige especialidad, fecha y hora para solicitar una consulta médica.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/paciente.css', 'resources/js/app.js'])
</head>
<body>
    <div class="paciente-page">
        @include('portal::paciente.sidebar')

        <main class="paciente-main">
            <div class="paciente-content-area">
                <header class="paciente-header">
                    <div class="paciente-header-content">
                        <span class="paciente-consulta-eyebrow">Agenda médica</span>
                        <h1>Solicita tu consulta</h1>
                        <p>Selecciona la especialidad, revisa los horarios disponibles y elige cuándo te viene mejor.</p>
                    </div>
                </header>

                @if ($errors->any())
                    <div class="paciente-consulta-alert" role="alert">
                        <strong>No pudimos enviar tu solicitud</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="paciente-consulta-layout">
                    <section class="paciente-consulta-main-card">
                        <div class="paciente-consulta-card-heading">
                            <span class="paciente-consulta-heading-icon" aria-hidden="true">✦</span>
                            <div>
                                <h2>Encuentra un horario</h2>
                                <p>La coordinación asignará un profesional de la especialidad elegida.</p>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('paciente.consulta') }}" class="paciente-consulta-availability-form">
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="especialidad">Especialidad</label>
                                <select class="paciente-consulta-select" id="especialidad" name="especialidad" required>
                                    <option value="">Selecciona una especialidad</option>
                                    @foreach ($especialidades as $especialidad)
                                        <option value="{{ $especialidad->slug }}" {{ $especialidadSeleccionada === $especialidad->slug ? 'selected' : '' }}>{{ $especialidad->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="fecha">Día de la consulta</label>
                                <input type="date" class="paciente-consulta-input" id="fecha" name="fecha" value="{{ $fechaSeleccionada }}" min="{{ now()->toDateString() }}" required>
                            </div>
                            <button type="submit" class="paciente-consulta-button paciente-consulta-button--check">
                                <span aria-hidden="true">⌕</span>
                                Ver horarios
                            </button>
                        </form>

                        @if ($fechaSeleccionada && $especialidadSeleccionada)
                            <div class="paciente-consulta-availability" aria-live="polite">
                                <div class="paciente-consulta-availability-heading">
                                    <div>
                                        <span class="paciente-consulta-eyebrow">Disponibilidad</span>
                                        <h3>Horas para {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}</h3>
                                    </div>
                                    <span class="paciente-consulta-slot-count">{{ count($horasDisponibles) }} {{ count($horasDisponibles) === 1 ? 'hora' : 'horas' }}</span>
                                </div>

                                <form method="POST" action="{{ route('paciente.consulta.store') }}" class="paciente-consulta-request-form">
                                    @csrf
                                    <input type="hidden" name="especialidad" value="{{ $especialidadSeleccionada }}">
                                    <input type="hidden" name="fecha" value="{{ $fechaSeleccionada }}">

                                    <fieldset class="paciente-consulta-time-fieldset">
                                        <legend>Elige una hora disponible</legend>
                                        @if (count($horasDisponibles) > 0)
                                            <div class="paciente-consulta-time-options">
                                                @foreach ($horasDisponibles as $hora)
                                                    <label class="paciente-consulta-time-option">
                                                        <input type="radio" name="hora" value="{{ $hora }}" {{ old('hora') === $hora ? 'checked' : '' }} required>
                                                        <span>{{ $hora }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="paciente-consulta-no-slots">
                                                <span aria-hidden="true">◷</span>
                                                <div>
                                                    <strong>No hay horas disponibles para esta fecha</strong>
                                                    @if ($fechaSeleccionadaEsHoy)
                                                        <p>Para hoy solo se muestran horas futuras dentro del horario activo. Si el horario terminó al mediodía, ya no quedan horas para reservar. Coordinación asignará al médico después de que envíes la solicitud.</p>
                                                    @else
                                                        <p>Prueba con otra fecha o especialidad. Solo mostramos horas cubiertas por horarios activos.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @error('hora')
                                            <p class="paciente-consulta-error">{{ $message }}</p>
                                        @enderror
                                    </fieldset>

                                    <div class="paciente-consulta-field paciente-consulta-reason">
                                        <label class="paciente-consulta-label" for="motivo">Describe qué te pasa o qué síntomas tienes <span>(opcional)</span></label>
                                        <textarea class="paciente-consulta-textarea" id="motivo" name="motivo" rows="4" maxlength="1000" placeholder="Cuéntanos qué sientes, desde cuándo y cualquier detalle que consideres importante...">{{ old('motivo') }}</textarea>
                                        <p class="paciente-consulta-field-hint">Esta información ayudará al equipo médico a preparar tu atención.</p>
                                        @error('motivo')
                                            <p class="paciente-consulta-error">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="paciente-consulta-selection-summary">
                                        <span class="paciente-consulta-summary-icon" aria-hidden="true">✓</span>
                                        <div class="paciente-consulta-summary-content">
                                            <strong>Resumen de tu solicitud</strong>
                                            <dl>
                                                <div><dt>Especialidad</dt><dd id="summary-especialidad">{{ $especialidades->firstWhere('slug', $especialidadSeleccionada)?->name ?? 'Especialidad seleccionada' }}</dd></div>
                                                <div><dt>Profesional</dt><dd>Asignado por coordinación</dd></div>
                                                <div><dt>Fecha</dt><dd>{{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}</dd></div>
                                                <div><dt>Hora</dt><dd id="summary-hora">Elige una hora</dd></div>
                                            </dl>
                                        </div>
                                    </div>

                                    <button type="submit" class="paciente-consulta-button paciente-consulta-button--submit" {{ count($horasDisponibles) === 0 || ! old('hora') ? 'disabled' : '' }}>
                                        Enviar solicitud de consulta
                                        <span aria-hidden="true">→</span>
                                    </button>
                                    @if (count($horasDisponibles) === 0)
                                        <p class="paciente-consulta-submit-hint">El botón se activará cuando elijas una fecha y especialidad con disponibilidad.</p>
                                    @endif
                                </form>
                            </div>
                        @else
                            <div class="paciente-consulta-empty-prompt">
                                <span aria-hidden="true">◷</span>
                                <p>Elige una especialidad y un día para consultar las horas disponibles.</p>
                            </div>
                        @endif
                    </section>

                    <aside class="paciente-consulta-side-card">
                        <div class="paciente-consulta-side-icon" aria-hidden="true">♡</div>
                        <h2>¿Cómo funciona?</h2>
                        <ol class="paciente-consulta-steps">
                            <li><span>1</span><div><strong>Elige especialidad y día</strong><p>Te mostraremos los horarios con disponibilidad.</p></div></li>
                            <li><span>2</span><div><strong>Selecciona una hora</strong><p>Las opciones corresponden a horarios activos de atención.</p></div></li>
                            <li><span>3</span><div><strong>Coordinación confirma</strong><p>El equipo revisará tu solicitud y asignará al profesional.</p></div></li>
                        </ol>
                        <p class="paciente-consulta-side-note">La solicitud queda pendiente hasta que coordinación la revise.</p>
                    </aside>
                </div>
            </div>
        </main>
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

            var selectedHour = document.querySelectorAll('input[name="hora"]');
            var summaryHour = document.getElementById('summary-hora');
            var submitButton = document.querySelector('.paciente-consulta-button--submit');

            selectedHour.forEach(function (input) {
                input.addEventListener('change', function () {
                    if (summaryHour) summaryHour.textContent = input.value;
                    if (submitButton) submitButton.disabled = false;
                });
            });

            var checkedHour = document.querySelector('input[name="hora"]:checked');
            if (checkedHour) {
                if (summaryHour) summaryHour.textContent = checkedHour.value;
                if (submitButton) submitButton.disabled = false;
            }
        });
    </script>
</body>
</html>
