<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Dashboard Coordinación — {{ config('app.name') }}</title>
    <meta name="description" content="Panel de coordinación clínica para gestionar citas, pacientes y personal.">
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
                        <h1>Panel de Coordinación</h1>
                        <p>Bienvenido, Coordinador demostrativo. Gestione citas, pacientes y personal desde este panel.</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <a href="{{ route('coordinacion.citas') }}" class="coordinacion-btn-primary">Ver citas del día</a>
                    </div>
                </div>

                <!-- Resumen de estadísticas -->
                <section class="coordinacion-section">
                    <h2 class="coordinacion-section-title">Resumen del día</h2>
                    <div class="coordinacion-grid coordinacion-grid-cols-3">
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--forest" aria-hidden="true">&#x1F4C5;</span>
                            <span class="coordinacion-stat-label">Citas hoy</span>
                            <span class="coordinacion-stat-value">12</span>
                        </div>
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--sage" aria-hidden="true">&#x23F3;</span>
                            <span class="coordinacion-stat-label">Pendientes confirmación</span>
                            <span class="coordinacion-stat-value">3</span>
                        </div>
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--mint" aria-hidden="true">&#x2705;</span>
                            <span class="coordinacion-stat-label">Completadas</span>
                            <span class="coordinacion-stat-value">45</span>
                        </div>
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--forest" aria-hidden="true">&#x1F464;</span>
                            <span class="coordinacion-stat-label">Pacientes programados</span>
                            <span class="coordinacion-stat-value">8</span>
                        </div>
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--sage" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                            <span class="coordinacion-stat-label">Médicos disponibles</span>
                            <span class="coordinacion-stat-value">6</span>
                        </div>
                        <div class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--mint" aria-hidden="true">&#x23F1;</span>
                            <span class="coordinacion-stat-label">Ausencias</span>
                            <span class="coordinacion-stat-value">1</span>
                        </div>
                    </div>
                </section>

                <!-- Próximas citas -->
                <section class="coordinacion-section">
                    <h2 class="coordinacion-section-title">Próximas citas</h2>
                    <div class="coordinacion-cita-list">
                        <div class="coordinacion-cita-item">
                            <div class="coordinacion-cita-date">25 Oct · 10:00</div>
                            <div class="coordinacion-cita-info">
                                <span class="coordinacion-cita-patient">Ana López García</span>
                                <span class="coordinacion-cita-reason">Control general · Dr. Carlos Méndez</span>
                            </div>
                            <span class="coordinacion-cita-status">Confirmada</span>
                        </div>
                        <div class="coordinacion-cita-item">
                            <div class="coordinacion-cita-date">25 Oct · 11:30</div>
                            <div class="coordinacion-cita-info">
                                <span class="coordinacion-cita-patient">Carlos Ruiz</span>
                                <span class="coordinacion-cita-reason">Seguimiento cardiológico · Dra. Laura Gómez</span>
                            </div>
                            <span class="coordinacion-cita-status">Pendiente</span>
                        </div>
                        <div class="coordinacion-cita-item">
                            <div class="coordinacion-cita-date">25 Oct · 14:00</div>
                            <div class="coordinacion-cita-info">
                                <span class="coordinacion-cita-patient">María Rodríguez</span>
                                <span class="coordinacion-cita-reason">Control pediátrico · Dr. Roberto Silva</span>
                            </div>
                            <span class="coordinacion-cita-status">Confirmada</span>
                        </div>
                    </div>
                    <p class="coordinacion-disclaimer">Datos de demostración. No son citas reales.</p>
                </section>

                <!-- Alertas -->
                <section class="coordinacion-section">
                    <h2 class="coordinacion-section-title">Alertas y avisos</h2>
                    <div class="coordinacion-notifications-list">
                        <div class="coordinacion-notification-item coordinacion-notification-item--unread">
                            <span class="coordinacion-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="coordinacion-notification-content">
                                <p class="coordinacion-notification-title">Cita sin confirmar</p>
                                <p class="coordinacion-notification-text">La cita de las 11:30 con Carlos Ruiz está pendiente de confirmación.</p>
                                <span class="coordinacion-notification-time">Hace 15 min</span>
                            </div>
                        </div>
                        <div class="coordinacion-notification-item">
                            <span class="coordinacion-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="coordinacion-notification-content">
                                <p class="coordinacion-notification-title">Médico disponible</p>
                                <p class="coordinacion-notification-text">Dra. Laura Gómez tiene espacio disponible a las 15:00.</p>
                                <span class="coordinacion-notification-time">Hace 2 horas</span>
                            </div>
                        </div>
                    </div>
                    <p class="coordinacion-disclaimer">Datos de demostración. No son alertas reales.</p>
                </section>

                <!-- Accesos rápidos -->
                <section class="coordinacion-section">
                    <h2 class="coordinacion-section-title">Accesos rápidos</h2>
                    <div class="coordinacion-grid coordinacion-actions-grid coordinacion-grid-cols-2 coordinacion-grid-cols-4">
                        <a href="{{ route('coordinacion.citas') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F4C5;</span>
                            Citas y agenda
                        </a>
                        <a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F464;</span>
                            Pacientes
                        </a>
                        <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                            Médicos y horarios
                        </a>
                        <a href="{{ route('coordinacion.recepcion') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F464;</span>
                            Recepción y seguimiento
                        </a>
                        <a href="{{ route('coordinacion.reportes') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F4CA;</span>
                            Reportes y estadísticas
                        </a>
                        <a href="{{ route('coordinacion.notificaciones') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">&#x1F4E7;</span>
                            Notificaciones
                        </a>
                    </div>
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