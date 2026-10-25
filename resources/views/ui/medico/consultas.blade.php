<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Consultas Médicas — {{ config('app.name') }}</title>
    <meta name="description" content="Interfaz para registrar y gestionar consultas médicas.">
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
                        <p>Registra la información de las consultas de tus pacientes.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-consultas-table">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="medico-agenda-patient">Ana López García</span></td>
                                <td>25 Oct 2024</td>
                                <td>Presencial</td>
                                <td><span class="medico-badge medico-badge--completed">Completada</span></td>
                                <td><button class="medico-agenda-btn medico-agenda-btn--view" disabled>Ver</button></td>
                            </tr>
                            <tr>
                                <td><span class="medico-agenda-patient">Carlos Ruiz</span></td>
                                <td>24 Oct 2024</td>
                                <td>Presencial</td>
                                <td><span class="medico-badge medico-badge--completed">Completada</span></td>
                                <td><button class="medico-agenda-btn medico-agenda-btn--view" disabled>Ver</button></td>
                            </tr>
                            <tr>
                                <td><span class="medico-agenda-patient">María Rodríguez</span></td>
                                <td>23 Oct 2024</td>
                                <td>Telemedicina</td>
                                <td><span class="medico-badge medico-badge--pending">Pendiente</span></td>
                                <td><button class="medico-agenda-btn medico-agenda-btn--view" disabled>Ver</button></td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section class="medico-section">
                    <div class="medico-consulta-detail">
                        <h2 class="medico-section-title">Registro de consulta (demostración)</h2>

                        <div class="medico-consulta-section">
                            <h3 class="medico-consulta-section-title">Paciente</h3>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Nombre</label>
                                <p class="medico-consulta-field-value">Ana López García</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">ID</label>
                                <p class="medico-consulta-field-value">PAC-001</p>
                            </div>
                        </div>

                        <div class="medico-consulta-section">
                            <h3 class="medico-consulta-section-title">Motivo y síntomas</h3>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Motivo de consulta</label>
                                <p class="medico-consulta-field-value">Control general de salud</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Síntomas reportados</label>
                                <p class="medico-consulta-field-value">Cefalea leve, fatiga ocasional</p>
                            </div>
                        </div>

                        <div class="medico-consulta-section">
                            <h3 class="medico-consulta-section-title">Signos vitales</h3>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Presión arterial</label>
                                <p class="medico-consulta-field-value">120/80 mmHg</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Frecuencia cardíaca</label>
                                <p class="medico-consulta-field-value">72 bpm</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Temperatura</label>
                                <p class="medico-consulta-field-value">36.6 °C</p>
                            </div>
                        </div>

                        <div class="medico-consulta-section">
                            <h3 class="medico-consulta-section-title">Evaluación y diagnóstico</h3>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Evaluación clínica</label>
                                <p class="medico-consulta-field-value">Paciente estable, sin hallazgos relevantes</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Diagnóstico presuntivo</label>
                                <p class="medico-consulta-field-value">Control rutinario sin patología</p>
                            </div>
                        </div>

                        <div class="medico-consulta-section">
                            <h3 class="medico-consulta-section-title">Plan e indicaciones</h3>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Tratamiento</label>
                                <p class="medico-consulta-field-value">No requiere tratamiento farmacológico</p>
                            </div>
                            <div class="medico-consulta-field">
                                <label class="medico-consulta-field-label">Recomendaciones</label>
                                <p class="medico-consulta-field-value">Control en 6 meses, hábitos saludables</p>
                            </div>
                        </div>

                        <div class="medico-consulta-actions">
                            <button class="medico-consulta-btn medico-consulta-btn--draft medico-consulta-btn--disabled" disabled>Guardar borrador</button>
                            <button class="medico-consulta-btn medico-consulta-btn--finish medico-consulta-btn--disabled" disabled>Finalizar consulta</button>
                        </div>

                        <p class="medico-disclaimer">Este formulario es una demostración visual. No guarda datos realmente.</p>
                    </div>
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