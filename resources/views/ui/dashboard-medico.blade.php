<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Panel médico — {{ config('app.name') }}</title>
    <meta name="description" content="Panel privado del médico para gestionar citas, pacientes y consultas.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/medico.css', 'resources/js/app.js'])
</head>
<body>
    <div class="medico-page">
        @include('portal::medico.sidebar')

        <main class="medico-main">
            <div class="medico-content-area">
                <header class="medico-header">
                    <div class="medico-header-content">
                        <p class="medico-eyebrow">Área clínica</p>
                        <h1>Panel del médico</h1>
                        <p>Bienvenido, {{ auth()->user()->name }}. Gestiona tu jornada y accede a tus herramientas clínicas.</p>
                    </div>
                    <div class="medico-header-actions">
                        <a href="{{ route('medico.agenda') }}" class="medico-btn-primary">Mi agenda</a>
                    </div>
                </header>

                <section class="medico-section" aria-labelledby="medico-summary-title">
                    <p class="medico-eyebrow">Jornada</p>
                    <h2 id="medico-summary-title" class="medico-section-title">Resumen de agenda</h2>
                    <div class="medico-grid medico-dashboard-grid medico-dashboard-grid-cols-3">
                        <article class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--forest" aria-hidden="true">AG</span>
                            <span class="medico-stat-label">Citas de hoy</span>
                            <span class="medico-stat-value">Sin datos</span>
                        </article>
                        <article class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--sage" aria-hidden="true">PE</span>
                            <span class="medico-stat-label">Pendientes</span>
                            <span class="medico-stat-value">Sin datos</span>
                        </article>
                        <article class="medico-stat-card">
                            <span class="medico-stat-icon medico-stat-icon--mint" aria-hidden="true">FI</span>
                            <span class="medico-stat-label">Completadas</span>
                            <span class="medico-stat-value">Sin datos</span>
                        </article>
                    </div>
                </section>

                <section class="medico-section" aria-labelledby="medico-next-title">
                    <div class="medico-section-heading">
                        <div>
                            <p class="medico-eyebrow">Siguiente atención</p>
                            <h2 id="medico-next-title" class="medico-section-title">Próxima cita</h2>
                        </div>
                        <a href="{{ route('medico.agenda') }}" class="medico-btn-link">Abrir agenda</a>
                    </div>
                    <article class="medico-appointment-card">
                        <div class="medico-appointment-header">
                            <div>
                                <span class="medico-appointment-date">Próxima cita</span>
                                <span class="medico-appointment-patient">No hay citas asignadas para mostrar</span>
                                <span class="medico-appointment-reason">Los detalles de atención se mostrarán cuando haya registros disponibles.</span>
                            </div>
                            <span class="medico-badge">Sin datos</span>
                        </div>
                        <a href="{{ route('medico.agenda') }}" class="medico-btn-link">Consultar agenda</a>
                    </article>
                </section>

                <section class="medico-section" aria-labelledby="medico-actions-title">
                    <p class="medico-eyebrow">Atención clínica</p>
                    <h2 id="medico-actions-title" class="medico-section-title">Acciones rápidas</h2>
                    <div class="medico-grid medico-actions-grid medico-actions-grid-cols-2 medico-actions-grid-cols-4">
                        <a href="{{ route('medico.agenda') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">AG</span>
                            <span>Mi agenda</span>
                        </a>
                        <a href="{{ route('medico.pacientes') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">PA</span>
                            <span>Pacientes autorizados</span>
                        </a>
                        <a href="{{ route('medico.consultas') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">CS</span>
                            <span>Consultas médicas</span>
                        </a>
                        <a href="{{ route('medico.historial') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">HI</span>
                            <span>Historial clínico</span>
                        </a>
                        <a href="{{ route('medico.horario') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">HO</span>
                            <span>Horarios</span>
                        </a>
                        <a href="{{ route('medico.notificaciones') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">AV</span>
                            <span>Notificaciones</span>
                        </a>
                        <a href="{{ route('medico.perfil') }}" class="medico-action-btn">
                            <span class="medico-action-btn-icon" aria-hidden="true">PF</span>
                            <span>Mi perfil</span>
                        </a>
                    </div>
                </section>

                <section class="medico-section" aria-labelledby="medico-notifications-title">
                    <div class="medico-section-heading">
                        <div>
                            <p class="medico-eyebrow">Centro de avisos</p>
                            <h2 id="medico-notifications-title" class="medico-section-title">Notificaciones recientes</h2>
                        </div>
                        <a href="{{ route('medico.notificaciones') }}" class="medico-btn-link">Ver todas</a>
                    </div>
                    <div class="medico-notifications-list">
                        <div class="medico-empty-state">
                            <strong>No tienes notificaciones recientes</strong>
                            <p>Los avisos de agenda y atención aparecerán aquí cuando estén disponibles.</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
