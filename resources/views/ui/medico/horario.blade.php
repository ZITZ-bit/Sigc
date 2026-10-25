<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Mi Horario — {{ config('app.name') }}</title>
    <meta name="description" content="Gestiona tu disponibilidad horaria semanal.">
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
                        <p>Organiza tu disponibilidad semanal para consultas.</p>
                    </div>
                </div>

                <section class="medico-section">
                    <div class="medico-horario-header">
                        <div>
                            <h2 class="medico-section-title">Semana laboral</h2>
                            <p class="medico-text-muted">Lunes 21 — Domingo 27 de octubre</p>
                        </div>
                    </div>

                    <div class="medico-horario-grid">
                        <div class="medico-horario-day-header">Hora</div>
                        <div class="medico-horario-day-header medico-horario-day-header--today">Lun</div>
                        <div class="medico-horario-day-header">Mar</div>
                        <div class="medico-horario-day-header">Mié</div>
                        <div class="medico-horario-day-header">Jue</div>
                        <div class="medico-horario-day-header">Vie</div>
                        <div class="medico-horario-day-header">Sáb</div>
                        <div class="medico-horario-day-header">Dom</div>

                        <div class="medico-horario-time-slot">08:00</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">09:00</div>
                        <div class="medico-horario-cell">
                            <div class="medico-horario-appointment">A. López</div>
                        </div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">10:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">11:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">12:00</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">13:00</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--break">Descanso</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">14:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">15:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">16:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>

                        <div class="medico-horario-time-slot">17:00</div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                        <div class="medico-horario-cell medico-horario-cell--available"></div>
                    </div>
                    <p class="medico-disclaimer">Datos de demostración. No representa disponibilidad real.</p>
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