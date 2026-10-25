<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Reportes y Estadísticas — {{ config('app.name') }}</title>
    <meta name="description" content="Reportes y estadísticas de la clínica.">
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
                        <h1>Reportes y estadísticas</h1>
                        <p>Consulte indicadores y reportes de la clínica.</p>
                    </div>
                </div>

                <section class="coordinacion-section">
                    <div class="coordinacion-reportes-grid">
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Citas del mes</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--forest">142</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x1F4CA;</div>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Asistencia</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--sage">92%</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x2705;</div>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Ausencias</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--mint">8</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x23F1;</div>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Cancelaciones</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--forest">5</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x274C;</div>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Por especialidad</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--sage">4</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x1F4CB;</div>
                        </div>
                        <div class="coordinacion-reporte-card">
                            <span class="coordinacion-reporte-title">Carga por médico</span>
                            <span class="coordinacion-reporte-value coordinacion-reporte-value--mint">6</span>
                            <div class="coordinacion-reporte-chart" aria-hidden="true">&#x1F4CC;</div>
                        </div>
                    </div>

                    <p class="coordinacion-disclaimer">Datos de demostración. No son reportes reales. Exportación solo visual.</p>
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