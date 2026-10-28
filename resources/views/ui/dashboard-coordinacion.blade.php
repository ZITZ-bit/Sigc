<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Panel de coordinación — {{ config('app.name') }}</title>
    <meta name="description" content="Panel de coordinación clínica para gestionar citas, pacientes y personal.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/coordinacion.css', 'resources/js/app.js'])
</head>
<body>
    <div class="coordinacion-page">
        @include('portal::coordinacion.sidebar')

        <main class="coordinacion-main">
            <div class="coordinacion-content-area">
                <header class="coordinacion-header">
                    <div class="coordinacion-header-content">
                        <p class="coordinacion-eyebrow">Administración clínica</p>
                        <h1>Panel de coordinación</h1>
                        <p>Bienvenido, {{ auth()->user()->name }}. Gestione la operación diaria de la clínica desde este panel.</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <a href="{{ route('coordinacion.citas') }}" class="coordinacion-btn-primary">Ver citas del día</a>
                    </div>
                </header>

                <section class="coordinacion-section" aria-labelledby="coordinacion-summary-title">
                    <p class="coordinacion-eyebrow">Indicadores</p>
                    <h2 id="coordinacion-summary-title" class="coordinacion-section-title">Resumen del día</h2>
                    <div class="coordinacion-grid coordinacion-dashboard-grid coordinacion-dashboard-grid-cols-3">
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--forest" aria-hidden="true">AG</span>
                            <span class="coordinacion-stat-label">Citas hoy</span>
                            <span class="coordinacion-stat-value">{{ $citasHoy ?? 0 }}</span>
                        </article>
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--sage" aria-hidden="true">PE</span>
                            <span class="coordinacion-stat-label">Pendientes de confirmación</span>
                            <span class="coordinacion-stat-value">{{ $pendientes ?? 0 }}</span>
                        </article>
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--mint" aria-hidden="true">CO</span>
                            <span class="coordinacion-stat-label">Citas completadas</span>
                            <span class="coordinacion-stat-value">{{ $completadas ?? 0 }}</span>
                        </article>
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--forest" aria-hidden="true">PA</span>
                            <span class="coordinacion-stat-label">Pacientes programados</span>
                            <span class="coordinacion-stat-value">{{ $pacientesProgramados ?? 0 }}</span>
                        </article>
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--sage" aria-hidden="true">ME</span>
                            <span class="coordinacion-stat-label">Médicos disponibles</span>
                            <span class="coordinacion-stat-value">{{ $medicosDisponibles ?? 0 }}</span>
                        </article>
                        <article class="coordinacion-stat-card">
                            <span class="coordinacion-stat-icon coordinacion-stat-icon--mint" aria-hidden="true">AU</span>
                            <span class="coordinacion-stat-label">Ausencias</span>
                            <span class="coordinacion-stat-value">{{ $ausencias ?? 0 }}</span>
                        </article>
                    </div>
                </section>

                <section class="coordinacion-section" aria-labelledby="coordinacion-appointments-title">
                    <div class="coordinacion-section-heading">
                        <div>
                            <p class="coordinacion-eyebrow">Agenda</p>
                            <h2 id="coordinacion-appointments-title" class="coordinacion-section-title">Próximas citas</h2>
                        </div>
                        <a href="{{ route('coordinacion.citas') }}" class="coordinacion-btn-link">Abrir agenda</a>
                    </div>
                    <div class="coordinacion-cita-list">
                        @if (isset($proximas) && $proximas->isNotEmpty())
                            <table class="coordinacion-citas-table">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Médico</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proximas as $consulta)
                                        <tr>
                                            <td>{{ $consulta->hora }}</td>
                                            <td>{{ $consulta->user->name ?? 'N/A' }}</td>
                                            <td>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</td>
                                            <td>{{ ucfirst($consulta->estado) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="coordinacion-empty-state">
                                <strong>No hay citas para mostrar</strong>
                                <p>Las citas registradas para hoy aparecerán aquí con horario, paciente, profesional y estado.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="coordinacion-section" aria-labelledby="coordinacion-alerts-title">
                    <div class="coordinacion-section-heading">
                        <div>
                            <p class="coordinacion-eyebrow">Seguimiento operativo</p>
                            <h2 id="coordinacion-alerts-title" class="coordinacion-section-title">Alertas y avisos</h2>
                        </div>
                        <a href="{{ route('coordinacion.notificaciones') }}" class="coordinacion-btn-link">Ver notificaciones</a>
                    </div>
                    <div class="coordinacion-notifications-list">
                        @forelse ($notificaciones as $notification)
                            <div class="coordinacion-notification-item">
                                <div class="coordinacion-notification-content">
                                    <p class="coordinacion-notification-title">{{ $notification->data['message'] ?? 'Notificación' }}</p>
                                    <p class="coordinacion-notification-text">{{ is_null($notification->read_at) ? 'Sin leer' : 'Leída' }} · {{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="coordinacion-empty-state">
                                <strong>No hay alertas recientes</strong>
                                <p>Las notificaciones de citas y disponibilidad aparecerán aquí cuando existan registros.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="coordinacion-section" aria-labelledby="coordinacion-actions-title">
                    <p class="coordinacion-eyebrow">Administración</p>
                    <h2 id="coordinacion-actions-title" class="coordinacion-section-title">Accesos rápidos</h2>
                    <div class="coordinacion-grid coordinacion-actions-grid coordinacion-actions-grid-cols-2 coordinacion-actions-grid-cols-4">
                        <a href="{{ route('coordinacion.citas') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">AG</span>
                            <span>Citas y agenda</span>
                        </a>
                        <a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">PA</span>
                            <span>Pacientes</span>
                        </a>
                        <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">MH</span>
                            <span>Médicos y horarios</span>
                        </a>
                        <a href="{{ route('coordinacion.recepcion') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">RE</span>
                            <span>Recepción y seguimiento</span>
                        </a>
                        <a href="{{ route('coordinacion.reportes') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">RP</span>
                            <span>Reportes y estadísticas</span>
                        </a>
                        <a href="{{ route('coordinacion.notificaciones') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">AV</span>
                            <span>Notificaciones</span>
                        </a>
                        <a href="{{ route('coordinacion.perfil') }}" class="coordinacion-action-btn">
                            <span class="coordinacion-action-btn-icon" aria-hidden="true">PF</span>
                            <span>Mi perfil</span>
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
