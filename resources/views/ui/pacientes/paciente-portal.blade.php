<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Panel | Paciente | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/components.css', 'resources/css/portal.css', 'resources/js/pacientes.js'])
</head>
<body class="patient-page">
    <main class="section-shell">
        <section class="dashboard-window" aria-label="Panel del paciente">
            <div class="dashboard-content">
                <aside class="dashboard-sidebar" aria-label="Secciones del paciente">
                    <button class="side-icon side-icon-active" type="button" data-view="perfil" title="Perfil" aria-label="Perfil" aria-current="page">▦</button>
                    <button class="side-icon" type="button" data-view="medicos" title="Médicos" aria-label="Médicos">⌕</button>
                    <button class="side-icon" type="button" data-view="horarios" title="Horarios" aria-label="Horarios">◷</button>
                    <button class="side-icon" type="button" data-view="consultas" title="Consultas" aria-label="Consultas">♡</button>
                    <button class="side-icon" type="button" data-view="notificaciones" title="Notificaciones" aria-label="Notificaciones">▤</button>
                    <a class="patient-home-link" href="{{ url('/') }}" title="Volver al portal principal" aria-label="Volver al portal principal"><span aria-hidden="true">⌂</span><small>Portal</small></a>
                <form action="{{ route('logout') }}" method="POST" style="margin-top:auto;">@csrf<button type="submit" class="button button-primary" style="width:100%;margin-top:14px;">Cerrar sesión</button></form>
                </aside>

                <div class="panel-body">
                    @if (session('success'))
                        <div class="patient-feedback patient-feedback-success" role="status">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="patient-feedback patient-feedback-error" role="alert">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="patient-feedback patient-feedback-error" role="alert">{{ $errors->first() }}</div>
                    @endif

                    <!-- Perfil / datos personales -->
                    <section class="panel-view active" id="view-perfil">
                        <div class="panel-header">
                            <p class="dashboard-kicker">DATOS PERSONALES</p>
                            <h2>Mi perfil</h2>
                        </div>
                        <div class="profile-layout">
                            <div class="profile-summary">
                                <div class="avatar avatar-large">{{ mb_substr($user->name, 0, 1) }}{{ mb_substr($user->apellido ?? '', 0, 1) }}</div>
                                <h3>{{ $user->name }} {{ $user->apellido }}</h3>
                                <p>Paciente</p>
                                <div class="patient-divider"></div>
                                <div class="detail-row"><span>Correo</span><strong>{{ $user->email }}</strong></div>
                                <div class="detail-row"><span>Teléfono</span><strong>{{ $user->telefono ?? 'Sin teléfono' }}</strong></div>
                                <div class="detail-row"><span>Expediente</span><strong>{{ $paciente?->expediente_numero ?? 'Sin expediente asignado' }}</strong></div>
                                <div class="patient-status"><span></span> Datos del perfil</div>
                                <button class="button button-primary profile-edit" type="button">Editar mis datos <span aria-hidden="true">→</span></button>
                            </div>
                            <form class="profile-form" data-profile-form method="POST" action="{{ route('paciente.perfil.update') }}">
                                @csrf
                                <p class="dashboard-kicker">ACTUALIZAR INFORMACIÓN</p>
                                <h3>Editar datos personales</h3>
                                <div class="auth-field-row">
                                    <label class="auth-field"><span>Nombre</span><input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
                                    <label class="auth-field"><span>Apellido</span><input type="text" name="apellido" value="{{ old('apellido', $user->apellido) }}" required></label>
                                </div>
                                <label class="auth-field"><span>Correo electrónico</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
                                <label class="auth-field"><span>Teléfono</span><input type="tel" name="telefono" value="{{ old('telefono', $user->telefono) }}"></label>
                                <label class="auth-field"><span>Fecha de nacimiento</span><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}"></label>
                                <label class="auth-field"><span>Tipo de sangre</span><select name="tipo_sangre">
                                    <option value="" {{ old('tipo_sangre', $user->tipo_sangre) === '' ? 'selected' : '' }}>Seleccionar</option>
                                    @foreach (['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ old('tipo_sangre', $user->tipo_sangre) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select></label>
                                <button class="button button-primary" type="submit">Guardar cambios <span aria-hidden="true">→</span></button>
                            </form>
                        </div>
                    </section>

                    <!-- Médicos por especialidad -->
                    <section class="panel-view" id="view-medicos">
                        <div class="panel-header">
                            <p class="dashboard-kicker">BÚSQUEDA DE ESPECIALISTAS</p>
                            <h2>Médicos por especialidad</h2>
                        </div>
                        <label class="search-field">
                            <span>⌕</span>
                            <input type="search" data-doctor-search placeholder="Buscar por especialidad o nombre del médico..." aria-label="Buscar médicos por nombre o especialidad">
                        </label>
                        <p class="eyebrow" style="margin:22px 0 8px;">ESPECIALIDADES DISPONIBLES</p>
                        <div class="specialty-grid">
                            @php
                                $medicosPorEspecialidad = $medicos->groupBy(fn ($medico) => $medico->especialidad?->nombre ?? 'Sin especialidad');
                            @endphp
                            @foreach ($medicosPorEspecialidad as $especialidad => $grupo)
                                <button class="specialty-chip" type="button" data-specialty-filter="{{ $especialidad }}">
                                    <span>{{ $especialidad }}</span><strong>{{ $grupo->count() }} médico{{ $grupo->count() > 1 ? 's' : '' }}</strong>
                                </button>
                            @endforeach
                        </div>
                        <button class="patient-clear-filter" type="button" data-clear-doctor-filter hidden>Limpiar filtro</button>
                        <p class="eyebrow" style="margin:30px 0 8px;">RESULTADOS</p>
                        <div class="doctors-list">
                            @forelse ($medicos as $medico)
                                <article class="doctor-card" data-doctor="{{ $medico->user?->name ?? 'Médico' }} {{ $medico->user?->apellido }}" data-specialty="{{ $medico->especialidad?->nombre ?? 'Sin especialidad' }}" data-medico-id="{{ $medico->id }}">
                                    <div class="doctor-avatar">{{ mb_substr($medico->user?->name ?? 'M', 0, 1) }}{{ mb_substr($medico->user?->apellido ?? '', 0, 1) }}</div>
                                    <div>
                                        <div class="doctor-meta"><strong>{{ $medico->user?->name ?? 'Médico' }} {{ $medico->user?->apellido }}</strong><span>{{ $medico->especialidad?->nombre ?? 'Sin especialidad' }} · {{ $medico->anios_experiencia ?? 0 }} años{{ $medico->consultorio ? ' · Consultorio ' . $medico->consultorio : '' }}</span></div>
                                        <button class="button button-primary doctor-btn" type="button" data-view-horarios data-medico-id="{{ $medico->id }}">Ver horario</button>
                                    </div>
                                </article>
                            @empty
                                <p class="patient-empty-state" data-doctor-empty>No hay médicos registrados en el sistema.</p>
                            @endforelse
                        </div>
                        <p class="patient-empty-state" data-doctor-empty hidden>No hay médicos para esta búsqueda.</p>
                    </section>

                    <!-- Horarios disponibles -->
                    <section class="panel-view" id="view-horarios">
                        <div class="panel-header">
                            <p class="dashboard-kicker">DISPONIBILIDAD</p>
                            <h2>Horarios disponibles</h2>
                        </div>
                        <div class="schedule-date"><strong data-schedule-date>{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}</strong><span class="view-toggle" role="group" aria-label="Vista de horarios"><button type="button" class="is-active" data-schedule-mode="day">Día</button></span></div>
                        <div class="calendar-grid">
                            <div class="time-column"></div>
                            <div class="appointments">
                                @forelse ($horarios as $horario)
                                    <div class="slot slot-available" data-medico-id="{{ $horario->medico_id }}" data-horario-id="{{ $horario->id }}">
                                        <span>{{ $horario->medico?->user?->name ?? 'Médico' }} {{ $horario->medico?->user?->apellido }}</span>
                                        <small>{{ $horario->medico?->especialidad?->nombre ?? 'Sin especialidad' }} · {{ $horario->dia_semana }} {{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}{{ $horario->medico?->consultorio ? ' · Consultorio ' . $horario->medico->consultorio : '' }}</small>
                                        <button type="button" data-reserve data-medico-id="{{ $horario->medico_id }}">Solicitar cita</button>
                                    </div>
                                @empty
                                    <p class="patient-empty-state">No hay horarios disponibles registrados.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="patient-week-scroll"><div class="patient-week-grid" data-week-grid hidden></div></div>
                    </section>

                    <!-- Mis consultas -->
                    <section class="panel-view" id="view-consultas">
                        <div class="panel-header">
                            <p class="dashboard-kicker">MIS CITAS</p>
                            <h2>Consultas solicitadas</h2>
                        </div>
                        <div class="consultas-tabs">
                            <button class="consulta-tab active" type="button" data-consultation-filter="all">Todas</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="pendiente">Pendientes</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="confirmada">Confirmadas</button>
                            <button class="consulta-tab" type="button" data-consultation-filter="completada">Completadas</button>
                        </div>
                        <div class="consultas-list">
                            @forelse ($paciente?->consultas as $consulta)
                                @php
                                    $estadoLabel = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'completada' => 'Completada', 'cancelada' => 'Cancelada'][$consulta->estado] ?? $consulta->estado;
                                @endphp
                                <div class="consulta-card" data-consultation-status="{{ $consulta->estado }}">
                                    <div class="consulta-info">
                                        <strong>{{ $consulta->motivo ?: 'Consulta general' }}</strong>
                                        <span>{{ $consulta->medico?->user?->name ?? 'Médico' }} {{ $consulta->medico?->user?->apellido }} · {{ $consulta->medico?->especialidad?->nombre ?? 'Sin especialidad' }}</span>
                                    </div>
                                    <div class="consulta-meta">
                                        <span>{{ $consulta->fecha_hora->format('d M \d\e Y \a \l\a\s H:i') }}</span><span>{{ $consulta->medico?->consultorio ?: 'Sin consultorio' }}</span>
                                    </div>
                                    <span class="consulta-status status-{{ $consulta->estado }}">{{ $estadoLabel }}</span>
                                </div>
                            @empty
                                <p class="patient-empty-state">No tienes consultas registradas.</p>
                            @endforelse
                        </div>
                        <p class="patient-empty-state" data-consultations-empty hidden>No hay consultas en esta categoría.</p>
                    </section>

                    <!-- Notificaciones -->
                    <section class="panel-view" id="view-notificaciones">
                        <div class="panel-header">
                            <p class="dashboard-kicker">AVISOS Y RECUERDOS</p>
                            <h2>Notificaciones</h2>
                        </div>
                        <div class="notifications-list">
                            @forelse ($notificaciones as $notificacion)
                                <div class="notification-item{{ $notificacion->leida ? '' : ' notification-unread' }}" data-notification-id="{{ $notificacion->id }}">
                                    <div class="notification-dot"></div>
                                    <div class="notification-content">
                                        <strong>{{ $notificacion->titulo }}</strong>
                                        <span>{{ $notificacion->mensaje }}</span>
                                        <time>{{ $notificacion->created_at->format('d M Y, H:i') }}</time>
                                    </div>
                                </div>
                            @empty
                                <p class="patient-empty-state">No hay notificaciones.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                <aside class="patient-card">
                    <div class="patient-card-heading"><span>MI PERFIL</span><button type="button" class="patient-profile-link" data-view="perfil" aria-label="Abrir mi perfil">•••</button></div>
                    <div class="avatar avatar-large">{{ mb_substr($user->name, 0, 1) }}{{ mb_substr($user->apellido ?? '', 0, 1) }}</div>
                    <h3>{{ $user->name }} {{ $user->apellido }}</h3>
                    <p>Paciente</p>
                    <div class="patient-divider"></div>
                    <div class="detail-row"><span>Correo</span><strong>{{ $user->email }}</strong></div>
                    <div class="detail-row"><span>Teléfono</span><strong>{{ $user->telefono ?? 'Sin teléfono' }}</strong></div>
                    <div class="detail-row"><span>Notificaciones</span><strong>{{ $notificaciones->where('leida', false)->count() }} sin leer</strong></div>
                </aside>
            </div>
        </section>
    </main>

    <dialog class="patient-dialog" data-reservation-dialog aria-labelledby="reservation-title">
        <form class="patient-dialog-content" method="POST" action="{{ route('paciente.consulta.solicitar') }}" data-reservation-form>
            @csrf
            <p class="dashboard-kicker">SOLICITAR CITA</p>
            <h2 id="reservation-title">Nueva solicitud de consulta</h2>
            <input type="hidden" name="medico_id" data-reservation-medico>
            <label class="auth-field"><span>Médico</span><input type="text" name="medico_nombre" readonly data-reservation-medico-name></label>
            <label class="auth-field"><span>Fecha y hora</span><input type="datetime-local" name="fecha_hora" required></label>
            <label class="auth-field"><span>Motivo</span><textarea name="motivo" rows="3" placeholder="Motivo de la consulta"></textarea></label>
            <div class="patient-dialog-actions"><button type="button" data-close-reservation>Cancelar</button><button type="submit">Confirmar solicitud</button></div>
        </form>
    </dialog>
    <div class="patient-toast" role="status" aria-live="polite"></div>
</body>
</html>
