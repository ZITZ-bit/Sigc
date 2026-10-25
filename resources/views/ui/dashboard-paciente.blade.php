<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Panel de paciente — {{ config('app.name') }}</title>
    <meta name="description" content="Panel privado del paciente para gestionar citas y salud.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/paciente.css', 'resources/js/app.js'])
</head>
<body>
    <div class="paciente-page">
        @include('portal::paciente.sidebar')

        <main class="paciente-main">
            <div class="paciente-content-area">
                <header class="paciente-header">
                    <div class="paciente-header-content">
                        <p class="paciente-eyebrow">Portal del paciente</p>
                        <h1>Bienvenido, {{ auth()->user()->name }}</h1>
                        <p>Aquí encontrarás tus citas, historial médico y opciones de atención.</p>
                    </div>
                    <div class="paciente-header-actions">
                        <a href="{{ route('paciente.consulta') }}" class="paciente-btn-primary">Solicitar consulta</a>
                    </div>
                </header>

                <section class="paciente-section" aria-labelledby="paciente-summary-title">
                    <p class="paciente-eyebrow">Resumen personal</p>
                    <h2 id="paciente-summary-title" class="paciente-section-title">Resumen de citas</h2>
                    <div class="paciente-grid paciente-dashboard-grid paciente-dashboard-grid-cols-3">
                        <article class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--forest" aria-hidden="true">AG</span>
                            <span class="paciente-stat-label">Próxima cita</span>
                            <span class="paciente-stat-value">Sin datos</span>
                        </article>
                        <article class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--sage" aria-hidden="true">PE</span>
                            <span class="paciente-stat-label">Pendientes</span>
                            <span class="paciente-stat-value">Sin datos</span>
                        </article>
                        <article class="paciente-stat-card">
                            <span class="paciente-stat-icon paciente-stat-icon--mint" aria-hidden="true">CO</span>
                            <span class="paciente-stat-label">Confirmadas</span>
                            <span class="paciente-stat-value">Sin datos</span>
                        </article>
                    </div>
                </section>

                <section class="paciente-section" aria-labelledby="paciente-next-title">
                    <p class="paciente-eyebrow">Agenda</p>
                    <h2 id="paciente-next-title" class="paciente-section-title">Tu próxima cita</h2>
                    <article class="paciente-appointment-card">
                        <div class="paciente-appointment-header">
                            <div>
                                <span class="paciente-appointment-date">Próxima cita</span>
                                <span class="paciente-appointment-doctor">Aún no hay una cita registrada</span>
                                <span class="paciente-appointment-specialty">Los detalles aparecerán aquí cuando haya una cita disponible.</span>
                            </div>
                            <span class="paciente-appointment-badge paciente-badge">Sin datos</span>
                        </div>
                        <a href="{{ route('paciente.agenda') }}" class="paciente-btn-link">Consultar agenda</a>
                    </article>
                </section>

                <section class="paciente-section" aria-labelledby="paciente-actions-title">
                    <p class="paciente-eyebrow">Acceso directo</p>
                    <h2 id="paciente-actions-title" class="paciente-section-title">Acciones rápidas</h2>
                    <div class="paciente-grid paciente-actions-grid paciente-actions-grid-cols-2 paciente-actions-grid-cols-4">
                        <a href="{{ route('paciente.consulta') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">CS</span>
                            <span>Solicitar consulta</span>
                        </a>
                        <a href="{{ route('paciente.doctores') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">DR</span>
                            <span>Buscar doctores</span>
                        </a>
                        <a href="{{ route('paciente.agenda') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">AG</span>
                            <span>Ver agenda</span>
                        </a>
                        <a href="{{ route('paciente.historial') }}" class="paciente-action-btn">
                            <span class="paciente-action-btn-icon" aria-hidden="true">HI</span>
                            <span>Historial médico</span>
                        </a>
                    </div>
                </section>

                <section class="paciente-section" aria-labelledby="paciente-upcoming-title">
                    <div class="paciente-section-heading">
                        <div>
                            <p class="paciente-eyebrow">Seguimiento</p>
                            <h2 id="paciente-upcoming-title" class="paciente-section-title">Próximas citas</h2>
                        </div>
                        <a href="{{ route('paciente.agenda') }}" class="paciente-btn-link">Abrir agenda</a>
                    </div>
                    <div class="paciente-cita-list">
                        <div class="paciente-empty-state">
                            <strong>No hay próximas citas para mostrar</strong>
                            <p>Cuando se registren citas, verás aquí la fecha, el profesional y el estado.</p>
                        </div>
                    </div>
                </section>

                <section class="paciente-section" aria-labelledby="paciente-notifications-title">
                    <div class="paciente-section-heading">
                        <div>
                            <p class="paciente-eyebrow">Centro de avisos</p>
                            <h2 id="paciente-notifications-title" class="paciente-section-title">Notificaciones recientes</h2>
                        </div>
                        <a href="{{ route('paciente.notificaciones') }}" class="paciente-btn-link">Ver todas</a>
                    </div>
                    <div class="paciente-notifications-list">
                        <div class="paciente-empty-state">
                            <strong>No tienes notificaciones recientes</strong>
                            <p>Los recordatorios y avisos sobre tu atención aparecerán aquí.</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
