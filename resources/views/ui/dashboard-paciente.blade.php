<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Panel de Paciente — {{ config('app.name') }}</title>
    <meta name="description" content="Panel privado del paciente para gestionar citas y salud.">
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
                        <h1>Bienvenido, Paciente demostrativo</h1>
                        <p>Aquí encontrarás tus citas, historial médico y opciones de atención.</p>
                    </div>
                    <div class="paciente-header-actions">
                        <a href="{{ route('paciente.consulta') }}" class="paciente-btn-primary">
                            Solicitar consulta
                        </a>
                    </div>
                </div>

                <!-- Resumen de citas -->
                <section class="paciente-section">
                    <h2 class="paciente-section-title">Resumen de citas</h2>
                    <div class="paciente-grid paciente-grid-cols-3">
                        <div class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--forest" aria-hidden="true">&#x1F4C5;</span>
                            <span class="paciente-stat-label">Próxima cita</span>
                            <span class="paciente-stat-value">25 Oct, 10:00</span>
                        </div>
                        <div class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--sage" aria-hidden="true">&#x23F3;</span>
                            <span class="paciente-stat-label">Pendientes</span>
                            <span class="paciente-stat-value">2</span>
                        </div>
                        <div class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--mint" aria-hidden="true">&#x2705;</span>
                            <span class="paciente-stat-label">Confirmadas</span>
                            <span class="paciente-stat-value">12</span>
                        </div>
                    </div>
                </section>

                <!-- Próxima cita destacada -->
                <section class="paciente-section">
                    <h2 class="paciente-section-title">Tu próxima cita</h2>
                    <div class="paciente-appointment-card">
                        <div class="paciente-appointment-header">
                            <div>
                                <span class="paciente-appointment-date">25 Oct</span>
                                <span class="paciente-appointment-doctor">Dr. Carlos Méndez</span>
                                <span class="paciente-appointment-specialty">Medicina General</span>
                            </div>
                            <span class="paciente-appointment-badge paciente-badge">Confirmada</span>
                        </div>
                        <p class="paciente-mb-2">Consulta de control general en la Clínica {{ config('app.name') }}.</p>
                        <a href="{{ route('paciente.agenda') }}" class="paciente-btn-link">Ver detalles de la cita</a>
                    </div>
                </section>

                <!-- Acciones rápidas -->
                <section class="paciente-section">
                    <h2 class="paciente-section-title">Acciones rápidas</h2>
                    <div class="paciente-grid paciente-actions-grid paciente-grid-cols-2 paciente-grid-cols-4">
                        <a href="{{ route('paciente.consulta') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">&#x1F4E9;</span>
                            Solicitar consulta
                        </a>
                        <a href="{{ route('paciente.doctores') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                            Buscar doctores
                        </a>
                        <a href="{{ route('paciente.agenda') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">&#x1F4C5;</span>
                            Ver agenda
                        </a>
                        <a href="{{ route('paciente.historial') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">&#x1F4C4;</span>
                            Historial médico
                        </a>
                    </div>
                </section>

                <!-- Próximas citas -->
                <section class="paciente-section">
                    <h2 class="paciente-section-title">Próximas citas</h2>
                    <div class="paciente-cita-list">
                        <div class="paciente-cita-item">
                            <div class="paciente-cita-date">
                                <span class="paciente-cita-doctor">Dr. Elena Ruiz</span>
                                <span class="paciente-cita-specialty">Pediatría</span>
                            </div>
                            <span class="paciente-cita-status">Programada</span>
                        </div>
                        <div class="paciente-cita-item">
                            <div class="paciente-cita-date">
                                <span class="paciente-cita-doctor">Dr. Carlos Méndez</span>
                                <span class="paciente-cita-specialty">Medicina Interna</span>
                            </div>
                            <span class="paciente-cita-status">Pendiente</span>
                        </div>
                    </div>
                    <p class="paciente-text-center paciente-disclaimer">Datos de demostración. No son citas reales.</p>
                </section>

                <!-- Notificaciones -->
                <section class="paciente-section">
                    <h2 class="paciente-section-title">Notificaciones recientes</h2>
                    <div class="paciente-notifications-list">
                        <div class="paciente-notification-item paciente-notification-item--unread">
                            <span class="paciente-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="paciente-notification-content">
                                <p class="paciente-notification-title">Recordatorio de cita</p>
                                <p class="paciente-notification-text">Tienes una cita programada para el 25 de octubre a las 10:00 con el Dr. Carlos Méndez.</p>
                                <span class="paciente-notification-time">Hace 2 horas</span>
                            </div>
                        </div>
                        <div class="paciente-notification-item">
                            <span class="paciente-notification-icon" aria-hidden="true">&#x1F4E7;</span>
                            <div class="paciente-notification-content">
                                <p class="paciente-notification-title">Resultado de laboratorio</p>
                                <p class="paciente-notification-text">Tus resultados de análisis están disponibles en el historial médico.</p>
                                <span class="paciente-notification-time">Hace 1 día</span>
                            </div>
                        </div>
                    </div>
                    <p class="paciente-text-center paciente-disclaimer">Datos de demostración. No son notificaciones reales.</p>
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