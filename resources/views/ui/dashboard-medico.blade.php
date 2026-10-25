<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Dashboard Médico — {{ config('app.name') }}</title>
    <meta name="description" content="Panel privado del médico para gestionar citas, pacientes y consultas.">
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
                        <h1>Panel del Médico</h1>
                        <p>Bienvenido de vuelta, Dr. Médico demostrativo. Aquí gestionas tus citas y pacientes.</p>
                    </div>
                    <div class="medico-header-actions">
                        <a href="{{ route('medico.agenda') }}" class="medico-btn-primary">Mi agenda</a>
                    </div>
                </div>

                <!-- Resumen de citas -->
                <section class="medico-section">
                    <h2 class="medico-section-title">Resumen de citas</h2>
                    <div class="medico-grid medico-grid-cols-3">
                        <div class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--forest" aria-hidden="true">&#x1F4C5;</span>
                            <span class="medico-stat-label">Hoy</span>
                            <span class="medico-stat-value">4</span>
                        </div>
                        <div class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--sage" aria-hidden="true">&#x23F3;</span>
                            <span class="medico-stat-label">Pendientes</span>
                            <span class="medico-stat-value">2</span>
                        </div>
                        <div class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--mint" aria-hidden="true">&#x2705;</span>
                            <span class="medico-stat-label">Completadas</span>
                            <span class="medico-stat-value">18</span>
                        </div>
                    </div>
                </section>

                <!-- Próxima cita -->
                <section class="medico-section">
                    <h2 class="medico-section-title">Próxima cita</h2>
                    <div class="medico-appointment-card">
                        <div class="medico-appointment-header">
                            <div>
                                <span class="medico-appointment-date">26 Oct</span>
                                <span class="medico-appointment-patient">Ana López García</span>
                                <span class="medico-appointment-reason">Control general</span>
                            </div>
                            <span class="medico-badge medico-badge--confirmed">Confirmada</span>
                        </div>
                        <p class="medico-mb-2">Consulta de control general programada para las 10:00.</p>
                        <a href="{{ route('medico.agenda') }}" class="medico-btn-link">Ver detalles</a>
                    </div>
                </section>

                <!-- Acciones rápidas -->
                <section class="medico-section">
                    <h2 class="medico-section-title">Acciones rápidas</h2>
                    <div class="medico-grid medico-actions-grid medico-grid-cols-2 medico-grid-cols-4">
                        <a href="{{ route('medico.agenda') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">&#x1F4C5;</span>
                            Mi agenda
                        </a>
                        <a href="{{ route('medico.pacientes') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">&#x1F464;</span>
                            Mis pacientes
                        </a>
                        <a href="{{ route('medico.consultas') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">&#x1F4E9;</span>
                            Consultas médicas
                        </a>
                        <a href="{{ route('medico.historial') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">&#x1F4C4;</span>
                            Historial clínico
                        </a>
                    </div>
                </section>

                <!-- Notificaciones recientes -->
                <section class="medico-section">
                    <h2 class="medico-section-title">Notificaciones recientes</h2>
                    <div class="medico-notifications-list">
                        <div class="medico-notification-item medico-notification-item--unread">
                            <span class="medico-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="medico-notification-content">
                                <p class="medico-notification-title">Nueva cita solicitada</p>
                                <p class="medico-notification-text">Juan Pérez ha solicitado una consulta para mañana.</p>
                                <span class="medico-notification-time">Hace 30 min</span>
                            </div>
                        </div>
                        <div class="medico-notification-item">
                            <span class="medico-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="medico-notification-content">
                                <p class="medico-notification-title">Resultado disponible</p>
                                <p class="medico-notification-text">Los exámenes de María Rodríguez están listos.</p>
                                <span class="medico-notification-time">Hace 2 horas</span>
                            </div>
                        </div>
                    </div>
                    <p class="medico-disclaimer">Datos de demostración. No son notificaciones reales.</p>
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