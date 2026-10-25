<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Historial Clínico — {{ config('app.name') }}</title>
    <meta name="description" content="Consulta el historial clínico de tus pacientes.">
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
                        <h1>Historial clínico</h1>
                        <p>Revisa los antecedentes y registros médicos de tus pacientes.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-historial-header">
                        <div class="medico-historial-selector">
                            <select class="medico-consulta-select" aria-label="Seleccionar paciente">
                                <option value="">Seleccionar paciente...</option>
                                <option value="pac-001">Ana López García (PAC-001)</option>
                                <option value="pac-002">Carlos Ruiz (PAC-002)</option>
                                <option value="pac-003">María Rodríguez (PAC-003)</option>
                            </select>
                        </div>
                    </div>

                    <div class="medico-historial-timeline">
                        <div class="medico-historial-item medico-historial-item--completed">
                            <span class="medico-historial-date">15 Oct 2024</span>
                            <span class="medico-historial-title">Consulta general</span>
                            <span class="medico-historial-description">Control de rutina. Paciente estable.</span>
                        </div>
                        <div class="medico-historial-item">
                            <span class="medico-historial-date">02 Sep 2024</span>
                            <span class="medico-historial-title">Seguimiento cardiológico</span>
                            <span class="medico-historial-description">Revisión de presión arterial y colesterol.</span>
                        </div>
                        <div class="medico-historial-item">
                            <span class="medico-historial-date">10 Jul 2024</span>
                            <span class="medico-historial-title">Primera consulta</span>
                            <span class="medico-historial-description">Valoración inicial e historia clínica completa.</span>
                        </div>
                    </div>

                    <div class="medico-historial-section">
                        <h3 class="medico-historial-section-title">Antecedentes</h3>
                        <div class="medico-historial-grid medico-historial-grid-cols-2">
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Antecedentes personales</span>
                                <span class="medico-historial-field-value">Sin antecedentes relevantes</span>
                            </div>
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Antecedentes familiares</span>
                                <span class="medico-historial-field-value">Hipertensión en padre</span>
                            </div>
                        </div>
                    </div>

                    <div class="medico-historial-section">
                        <h3 class="medico-historial-section-title">Alergias</h3>
                        <div class="medico-historial-grid medico-historial-grid-cols-2">
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Medicamentos</span>
                                <span class="medico-historial-field-value">Ninguna registrada</span>
                            </div>
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Alimentarias</span>
                                <span class="medico-historial-field-value">Ninguna registrada</span>
                            </div>
                        </div>
                    </div>

                    <div class="medico-historial-section">
                        <h3 class="medico-historial-section-title">Medicamentos actuales</h3>
                        <div class="medico-historial-grid medico-historial-grid-cols-2">
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Medicamento</span>
                                <span class="medico-historial-field-value">No consume medicación</span>
                            </div>
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Dosis</span>
                                <span class="medico-historial-field-value">—</span>
                            </div>
                        </div>
                    </div>

                    <div class="medico-historial-section">
                        <h3 class="medico-historial-section-title">Diagnósticos previos</h3>
                        <div class="medico-historial-grid medico-historial-grid-cols-2">
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Diagnóstico 1</span>
                                <span class="medico-historial-field-value">Control de rutina</span>
                            </div>
                            <div class="medico-historial-field">
                                <span class="medico-historial-field-label">Fecha</span>
                                <span class="medico-historial-field-value">15 Oct 2024</span>
                            </div>
                        </div>
                    </div>

                    <div class="medico-historial-section">
                        <h3 class="medico-historial-section-title">Recomendaciones</h3>
                        <div class="medico-historial-field">
                            <span class="medico-historial-field-label">Indicaciones</span>
                            <span class="medico-historial-field-value">Mantener hábitos saludables y control anual</span>
                        </div>
                    </div>

                    <p class="medico-disclaimer">Datos de demostración. No son registros médicos reales.</p>
                </section>
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