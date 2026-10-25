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
                    <div class="paciente-doctores-header">
                        <div class="paciente-doctores-search">
                            <input type="text" class="paciente-consulta-input" placeholder="Buscar por nombre o especialidad..." aria-label="Buscar doctores">
                        </div>
                    </div>

                    <div class="paciente-doctores-grid">
                        <article class="paciente-doctor-card">
                            <div class="paciente-doctor-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                            <h3 class="paciente-doctor-name">Dr. Carlos Méndez</h3>
                            <span class="paciente-doctor-specialty">Medicina General</span>
                            <p class="paciente-doctor-bio">Atención primaria y control general de salud para pacientes de todas las edades.</p>
                            <div class="paciente-doctor-rating">
                                <span class="paciente-consulta-step-number">4.9</span>
                                <span>(120 reseñas)</span>
                            </div>
                            <a href="#" class="paciente-doctor-btn">Solicitar consulta</a>
                        </article>
                        <article class="paciente-doctor-card">
                            <div class="paciente-doctor-avatar" aria-hidden="true">&#x1F469;&#x200D;&#x1F3EB;</div>
                            <h3 class="paciente-doctor-name">Dra. Laura Gómez</h3>
                            <span class="paciente-doctor-specialty">Cardiología</span>
                            <p class="paciente-doctor-bio">Especialista en salud cardiovascular y prevención de enfermedades del corazón.</p>
                            <div class="paciente-doctor-rating">
                                <span class="paciente-consulta-step-number">4.8</span>
                                <span>(95 reseñas)</span>
                            </div>
                            <a href="#" class="paciente-doctor-btn">Solicitar consulta</a>
                        </article>
                        <article class="paciente-doctor-card">
                            <div class="paciente-doctor-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                            <h3 class="paciente-doctor-name">Dr. Roberto Silva</h3>
                            <span class="paciente-doctor-specialty">Pediatría</span>
                            <p class="paciente-doctor-bio">Cuidado integral de la salud infantil, desde recién nacidos hasta adolescentes.</p>
                            <div class="paciente-doctor-rating">
                                <span class="paciente-consulta-step-number">4.7</span>
                                <span>(88 reseñas)</span>
                            </div>
                            <a href="#" class="paciente-doctor-btn">Solicitar consulta</a>
                        </article>
                    </div>

                    <p class="paciente-text-center paciente-disclaimer">Datos de demostración. No son profesionales reales.</p>
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