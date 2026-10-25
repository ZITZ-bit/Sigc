<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ config('app.name') }} — Clínica Médica</title>
    <meta name="description" content="Atención médica integral con un enfoque humano y tecnología moderna.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('portal::portal-navbar')

    <main>
        @include('portal::portal-hero')

        <section class="portal-section" id="presentacion" aria-labelledby="presentacion-title">
            <div class="container">
                <div class="portal-section-head">
                    <h2 id="presentacion-title" class="portal-section-title">Sobre {{ config('app.name') }}</h2>
                    <p class="portal-section-description">
                        Una clínica médica dedicada al cuidado integral de cada persona, combinando calidez humana con los más altos estándares de práctica clínica.
                    </p>
                </div>

                <div class="portal-presentation-grid">
                    <div class="portal-presentation-content">
                        <h3>Nuestra propuesta</h3>
                        <p>Creemos que una buena atención comienza por escuchar. Acompañamos a nuestros pacientes en cada etapa del proceso, con respeto, claridad y tiempo dedicado a cada consulta.</p>
                        <ul>
                            <li>Atención personalizada y sin prisas</li>
                            <li>Equipo médico comprometido con el bienestar integral</li>
                            <li>Instalaciones diseñadas para tu comodidad</li>
                            <li>Acompañamiento continuo antes, durante y después de tu visita</li>
                        </ul>
                    </div>
                    <div class="portal-presentation-image" aria-hidden="true">
                        <svg width="100%" height="400" viewBox="0 0 800 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="800" height="400" fill="#e8f5e9" rx="2"/>
                            <circle cx="200" cy="200" r="80" fill="#c7e9c1"/>
                            <circle cx="600" cy="200" r="60" fill="#c7e9c1"/>
                            <line x1="120" y1="300" x2="280" y2="300" stroke="#8fbc8f" stroke-width="3" stroke-linecap="round"/>
                            <line x1="520" y1="300" x2="680" y2="300" stroke="#8fbc8f" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <section class="portal-section" id="servicios" aria-labelledby="servicios-title">
            <div class="container">
                <div class="portal-section-head">
                    <h2 id="servicios-title" class="portal-section-title">Nuestros servicios</h2>
                    <p class="portal-section-description">
                        A continuación encontrarás información sobre las áreas de atención disponibles en la clínica.
                    </p>
                </div>

                <div class="portal-services-grid">
                    <article class="portal-service-item">
                        <div class="portal-service-icon" aria-hidden="true">&#x260e;</div>
                        <h3 class="portal-service-title">Consulta general</h3>
                        <p class="portal-service-description">Atención médica primaria para el cuidado integral de tu salud y la de tu familia.</p>
                    </article>
                    <article class="portal-service-item">
                        <div class="portal-service-icon" aria-hidden="true">&#x1F3E5;</div>
                        <h3 class="portal-service-title">Pediatría</h3>
                        <p class="portal-service-description">Cuidado especializado para la salud de los más pequeños, desde el nacimiento hasta la adolescencia.</p>
                    </article>
                    <article class="portal-service-item">
                        <div class="portal-service-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
                        <h3 class="portal-service-title">Medicina interna</h3>
                        <p class="portal-service-description">Diagnóstico y tratamiento integral de enfermedades en la etapa adulta.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="portal-cta-section" id="cta" aria-labelledby="cta-title">
            <div class="container">
                <h2 id="cta-title" class="portal-cta-title">¿Necesitas más información?</h2>
                <p class="portal-cta-description">
                    Estamos aquí para resolver tus dudas y ayudarte a dar el siguiente paso en el cuidado de tu salud.
                </p>
                <a href="#contacto" class="portal-btn portal-btn-primary portal-btn-lg">Contactar con la clínica</a>
            </div>
        </section>

        <section class="portal-section" id="contacto" aria-labelledby="contacto-title">
            <div class="container">
                <div class="portal-section-head">
                    <h2 id="contacto-title" class="portal-section-title">Contacto</h2>
                    <p class="portal-section-description">
                        Escríbenos o llámanos. Estaremos encantados de atenderte.
                    </p>
                </div>

                <div class="portal-contact-grid">
                    <div class="portal-contact-item">
                        <span class="portal-contact-label">Teléfono</span>
                        <span class="portal-contact-value placeholder">+34 600 000 000</span>
                    </div>
                    <div class="portal-contact-item">
                        <span class="portal-contact-label">Correo electrónico</span>
                        <span class="portal-contact-value placeholder">contacto@clinica.com</span>
                    </div>
                    <div class="portal-contact-item">
                        <span class="portal-contact-label">Dirección</span>
                        <span class="portal-contact-value placeholder">Por configurar en el archivo .env</span>
                    </div>
                    <div class="portal-contact-item">
                        <span class="portal-contact-label">Horario</span>
                        <span class="portal-contact-value placeholder">Lunes a viernes, 9:00 — 20:00</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('portal::portal-footer')

</body>
</html>