<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Solicitar Consulta — {{ config('app.name') }}</title>
    <meta name="description" content="Formulario visual para solicitar una consulta médica.">
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
                        <h1>Solicitar consulta</h1>
                        <p>Completa el formulario para solicitar una cita médica.</p>
                    </div>
                </div>

                <section class="paciente-section">
                    <div class="paciente-consulta-steps">
                        <div class="paciente-consulta-step paciente-consulta-step--active">
                            <span class="paciente-consulta-step-number">1</span>
                            <span>Especialidad</span>
                        </div>
                        <div class="paciente-consulta-step">
                            <span class="paciente-consulta-step-number">2</span>
                            <span>Doctor</span>
                        </div>
                        <div class="paciente-consulta-step">
                            <span class="paciente-consulta-step-number">3</span>
                            <span>Fecha y hora</span>
                        </div>
                        <div class="paciente-consulta-step">
                            <span class="paciente-consulta-step-number">4</span>
                            <span>Resumen</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('paciente.consulta.store') }}" class="paciente-consulta-form">
                            @csrf
                        <div class="paciente-consulta-section">
                            <h3 class="paciente-consulta-section-title">1. Especialidad</h3>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="especialidad">Selecciona una especialidad</label>
                                <select class="paciente-consulta-select" id="especialidad" name="especialidad" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="medicina-general">Medicina General</option>
                                    <option value="cardiologia">Cardiología</option>
                                    <option value="pediatria">Pediatría</option>
                                    <option value="medicina-interna">Medicina Interna</option>
                                </select>
                                @error('especialidad')
                                    <p class="paciente-consulta-help text-error">{{ $message }}</p>
                                @enderror
                                <p class="paciente-consulta-help">Elige el área médica que necesitas.</p>
                            </div>
                        </div>

                        <div class="paciente-consulta-section">
                            <h3 class="paciente-consulta-section-title">2. Profesional</h3>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="doctor">Selecciona un doctor</label>
                                <select class="paciente-consulta-select" id="doctor" name="doctor" disabled>
                                    <option value="">Selecciona primero una especialidad</option>
                                </select>
                                <p class="paciente-consulta-help">Los doctores disponibles se mostrarán según la especialidad elegida.</p>
                            </div>
                        </div>

                        <div class="paciente-consulta-section">
                            <h3 class="paciente-consulta-section-title">3. Fecha y hora</h3>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="fecha">Fecha de la cita</label>
                                <input type="date" class="paciente-consulta-input" id="fecha" name="fecha" required>
                                <p class="paciente-consulta-help">Selecciona una fecha disponible.</p>
                                @error('fecha')
                                    <p class="paciente-consulta-help text-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="hora">Hora de la cita</label>
                                <select class="paciente-consulta-select" id="hora" name="hora" required>
                                    <option value="">Seleccionar hora...</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                </select>
                                @error('hora')
                                    <p class="paciente-consulta-help text-error">{{ $message }}</p>
                                @enderror
                                <p class="paciente-consulta-help">Elige un horario disponible.</p>
                            </div>
                        </div>

                        <div class="paciente-consulta-section">
                            <h3 class="paciente-consulta-section-title">4. Motivo de la consulta</h3>
                            <div class="paciente-consulta-field">
                                <label class="paciente-consulta-label" for="motivo">Motivo general (opcional)</label>
                                <textarea class="paciente-consulta-textarea" id="motivo" name="motivo" rows="3" placeholder="Describe brevemente el motivo de tu visita..."></textarea>
                                <p class="paciente-consulta-help">Este campo es opcional y ayuda al doctor a prepararse.</p>
                            </div>
                        </div>

                        <div class="paciente-consulta-summary">
                            <h3 class="paciente-consulta-summary-title">Resumen de tu solicitud</h3>
                            <div class="paciente-consulta-summary-item">
                                <span class="paciente-consulta-summary-label">Especialidad:</span>
                                <span class="paciente-consulta-summary-value">—</span>
                            </div>
                            <div class="paciente-consulta-summary-item">
                                <span class="paciente-consulta-summary-label">Doctor:</span>
                                <span class="paciente-consulta-summary-value">—</span>
                            </div>
                            <div class="paciente-consulta-summary-item">
                                <span class="paciente-consulta-summary-label">Fecha:</span>
                                <span class="paciente-consulta-summary-value">—</span>
                            </div>
                            <div class="paciente-consulta-summary-item">
                                <span class="paciente-consulta-summary-label">Hora:</span>
                                <span class="paciente-consulta-summary-value">—</span>
                            </div>
                        </div>

                        <p class="paciente-consulta-help">Tu solicitud será procesada y asignada a un profesional disponible.</p>
                    </form>
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

            var especialidadSelect = document.getElementById('especialidad');
            var doctorSelect = document.getElementById('doctor');

            if (especialidadSelect && doctorSelect) {
                especialidadSelect.addEventListener('change', function () {
                    doctorSelect.disabled = this.value === '';
                    if (this.value === '') {
                        doctorSelect.innerHTML = '<option value="">Selecciona primero una especialidad</option>';
                    } else {
                        doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
                    }
                });
            }
        });
    </script>
</body>
</html>