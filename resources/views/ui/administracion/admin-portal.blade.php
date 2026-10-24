<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Administración | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-page" data-initial-date="{{ today()->toDateString() }}" data-invite-doctor-url="{{ route('admin.medicos.invitar') }}" data-invite-patient-url="{{ route('admin.pacientes.invitar') }}" data-create-specialty-url="{{ route('admin.especialidades.store') }}" data-create-appointment-url="{{ route('admin.consultas.store') }}" data-create-schedule-url="{{ route('admin.horarios.store') }}" data-update-specialty-url="{{ url('/admin/especialidades') }}" data-update-patient-url="{{ url('/admin/pacientes') }}">
    <div class="admin-shell">
        <aside class="admin-rail" aria-label="Navegación administrativa">
            <a class="admin-mark" href="#resumen" aria-label="SIGC">S</a>
            <nav class="admin-nav">
                <button class="admin-nav-item is-active" data-admin-view="resumen" title="Resumen" aria-label="Resumen">▦</button>
                <button class="admin-nav-item" data-admin-view="pacientes" title="Pacientes" aria-label="Pacientes">♧</button>
                <button class="admin-nav-item" data-admin-view="equipo" title="Médicos y especialidades" aria-label="Médicos y especialidades">✳</button>
                <button class="admin-nav-item" data-admin-view="consultas" title="Consultas y horarios" aria-label="Consultas y horarios">▤</button>
                <button class="admin-nav-item" data-admin-view="agenda" title="Agenda" aria-label="Agenda">◷</button>
            </nav>
            <button class="admin-rail-bottom" title="Configuración" aria-label="Configuración">⚙</button>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-wordmark"><span class="admin-wordmark-icon">S</span><strong>sigc</strong><span class="admin-topbar-divider"></span><span>Administración</span></div>
                <div class="admin-topbar-actions">
                    <a class="admin-home-link" href="{{ url('/') }}">← Portal principal</a>
                    <label class="admin-search"><span aria-hidden="true">⌕</span><input type="search" placeholder="Buscar en el panel" aria-label="Buscar en el panel"></label>
                    <button class="admin-icon-button" title="Notificaciones" aria-label="Notificaciones">♧<i></i></button>
                    <button class="admin-user"><span class="admin-avatar">{{ mb_substr($user->name, 0, 1) }}{{ mb_substr($user->apellido ?? '', 0, 1) }}</span><span><strong>{{ $user->name }} {{ $user->apellido }}</strong><small>Administración</small></span><span class="admin-user-chevron">⌄</span></button>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="admin-icon-button" title="Cerrar sesión" aria-label="Cerrar sesión">␡</button></form>
                </div>
            </header>
            <main class="admin-content">
                @if (session('success'))<div class="admin-feedback admin-feedback-success" role="status">{{ session('success') }}</div>@endif
                @if (session('invite_url'))<div class="admin-feedback admin-feedback-success" role="status">Enlace de activación: <a href="{{ session('invite_url') }}">{{ session('invite_url') }}</a></div>@endif
                @if ($errors->any())<div class="admin-feedback admin-feedback-error" role="alert">{{ $errors->first() }}</div>@endif
                <section class="admin-view is-visible" id="admin-view-resumen" data-view-title="Resumen">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">{{ mb_strtoupper(now()->locale('es')->translatedFormat('l, d \\d\\e F \\d\\e Y')) }}</p><h1>Buenos días, {{ $user->name }}</h1><p class="admin-subtitle">Aquí tienes el pulso de tu clínica hoy.</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-metrics">
                        <x-admin.metric-card label="Pacientes activos" value="{{ number_format($resumen['pacientes_activos']) }}" note="Cuentas activas" icon="♧" tone="mint" />
                        <x-admin.metric-card label="Consultas de hoy" value="{{ $resumen['consultas_hoy'] }}" note="{{ $resumen['pendientes'] }} pendientes en total" icon="◷" tone="peach" />
                        <x-admin.metric-card label="Médicos activos" value="{{ $resumen['medicos'] }}" note="En {{ $resumen['especialidades'] }} especialidades" icon="✳" tone="yellow" />
                        <x-admin.metric-card label="Consultas pendientes" value="{{ $resumen['pendientes'] }}" note="Requieren confirmación" icon="↗" tone="lavender" />
                    </div>
                    <div class="admin-overview-grid">
                        <section class="admin-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">AGENDA · HOY</p><h2>Consultas próximas</h2></div><button class="admin-text-button" data-go-view="agenda">Ver agenda <span>→</span></button></div>
                            <div class="admin-timeline">
                                @forelse ($consultasHoy->take(5) as $consulta)
                                    @php($statusLabel = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'completada' => 'Completada', 'cancelada' => 'Cancelada'][$consulta->estado] ?? $consulta->estado)
                                    <article class="admin-timeline-row"><time>{{ $consulta->fecha_hora->format('H:i') }}</time><span class="admin-timeline-line"></span><div class="admin-event {{ $consulta->estado === 'pendiente' ? 'event-coral' : 'event-green' }}"><strong>{{ $consulta->paciente?->user?->name ?? 'Paciente' }} {{ $consulta->paciente?->user?->apellido }}</strong><span>{{ $consulta->medico?->user?->name ?? 'Médico' }} · {{ $consulta->medico?->especialidad?->nombre ?? 'Sin especialidad' }}</span></div><span class="admin-pill pill-{{ $consulta->estado === 'pendiente' ? 'pending' : ($consulta->estado === 'completada' ? 'completed' : 'confirmed') }}">{{ $statusLabel }}</span></article>
                                @empty
                                    <p class="admin-empty-inline">No hay consultas registradas para hoy.</p>
                                @endforelse
                            </div>
                        </section>
                        <section class="admin-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">EQUIPO CLÍNICO</p><h2>Médicos registrados</h2></div><button class="admin-more-button" aria-label="Más opciones">•••</button></div>
                            <div class="admin-duty-list">
                                @forelse ($medicos->take(4) as $medico)
                                    <div class="admin-duty-person"><span class="admin-doctor-avatar avatar-sage">{{ collect(explode(' ', $medico->user?->name ?? 'Médico'))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('') }}</span><div><strong>{{ $medico->user?->name ?? 'Médico' }} {{ $medico->user?->apellido }}</strong><span>{{ $medico->especialidad?->nombre ?? 'Sin especialidad' }} · Consultorio {{ $medico->consultorio ?? 'Sin asignar' }}</span></div><i class="duty-dot"></i></div>
                                @empty
                                    <p class="admin-empty-inline">Todavía no hay médicos registrados.</p>
                                @endforelse
                            </div><button class="admin-outline-button admin-wide-button" data-go-view="equipo">Ver equipo completo <span>→</span></button>
                        </section>
                    </div>
                    <section class="admin-panel admin-recent-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">ACTIVIDAD RECIENTE</p><h2>Últimos movimientos</h2></div><button class="admin-text-button" data-go-view="consultas">Ver consultas <span>→</span></button></div>
                        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>PACIENTE</th><th>CONSULTA</th><th>MÉDICO</th><th>FECHA Y HORA</th><th>ESTADO</th></tr></thead><tbody>
                            @forelse ($consultas->sortByDesc('fecha_hora')->take(5) as $consulta)
                                <tr><td>{{ $consulta->paciente?->user?->name ?? 'Paciente' }} {{ $consulta->paciente?->user?->apellido }}</td><td>{{ $consulta->motivo ?: 'Consulta general' }}</td><td>{{ $consulta->medico?->user?->name ?? 'Médico' }}</td><td>{{ $consulta->fecha_hora->format('d M, H:i') }}</td><td><span class="admin-pill pill-{{ $consulta->estado === 'pendiente' ? 'pending' : ($consulta->estado === 'completada' ? 'completed' : 'confirmed') }}">{{ ucfirst($consulta->estado) }}</span></td></tr>
                            @empty
                                <tr><td colspan="5" class="admin-empty-inline">Todavía no hay actividad de consultas.</td></tr>
                            @endforelse
                        </tbody></table></div>
                    </section>
                </section>

                <section class="admin-view" id="admin-view-pacientes" data-view-title="Pacientes">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">DIRECTORIO CLÍNICO</p><h1>Pacientes</h1><p class="admin-subtitle">{{ $pacientes->count() }} pacientes registrados.</p></div><button class="admin-button admin-button-primary" data-open-modal="paciente-invite"><span>＋</span> Invitar paciente</button></div>
                    <div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><label class="admin-filter-search"><span>⌕</span><input type="search" data-filter-input="patients" placeholder="Buscar paciente por nombre o correo"></label><select class="admin-select" data-filter-select="patients" aria-label="Filtrar pacientes"><option value="all">Todos los pacientes</option><option value="active">Activos</option><option value="inactive">Inactivos</option></select></div>
                        <div class="admin-table-wrap"><table class="admin-table" data-filter-table="patients"><thead><tr><th>PACIENTE</th><th>CONTACTO</th><th>ÚLTIMA VISITA</th><th>CONSULTAS</th><th>ESTADO</th><th></th></tr></thead><tbody>
                            @forelse ($pacientes as $paciente)
                                @php($patientActive = (bool) $paciente->user?->activo)
                                @php($lastVisit = $paciente->consultas->sortByDesc('fecha_hora')->first())
                                <tr data-status="{{ $patientActive ? 'active' : 'inactive' }}"><td><span class="table-person"><span class="table-avatar">{{ mb_substr($paciente->user?->name ?? 'P', 0, 1) }}{{ mb_substr($paciente->user?->apellido ?? '', 0, 1) }}</span><span>{{ $paciente->user?->name }} {{ $paciente->user?->apellido }}<small>Expediente {{ $paciente->expediente_numero ?: 'pendiente' }}</small></span></span></td><td>{{ $paciente->user?->email }}<br><small>{{ $paciente->user?->telefono ?: 'Sin teléfono' }}</small></td><td>{{ $lastVisit?->fecha_hora?->format('d M Y') ?? 'Sin visitas' }}</td><td>{{ $paciente->consultas_count }}</td><td><span class="admin-pill {{ $patientActive ? 'pill-confirmed' : 'pill-completed' }}">{{ $patientActive ? 'Activo' : 'Inactivo' }}</span></td><td><div class="admin-patient-actions"><button class="admin-row-menu" type="button" data-edit-patient="{{ $paciente->id }}" data-name="{{ $paciente->user?->name }}" data-surname="{{ $paciente->user?->apellido }}" data-email="{{ $paciente->user?->email }}" data-phone="{{ $paciente->user?->telefono }}">Editar</button><form method="POST" action="{{ route('admin.pacientes.estado', $paciente) }}">@csrf @method('PATCH')<input type="hidden" name="activo" value="{{ $patientActive ? 0 : 1 }}"><button class="admin-row-menu" type="submit">{{ $patientActive ? 'Desactivar' : 'Activar' }}</button></form></div></td></tr>
                            @empty
                                <tr><td colspan="6" class="admin-empty-inline">Todavía no hay pacientes registrados.</td></tr>
                            @endforelse
                            <tr class="admin-empty-row" hidden><td colspan="6">No hay pacientes que coincidan con la búsqueda.</td></tr>
                        </tbody></table></div><div class="admin-pagination"><span>{{ $pacientes->count() }} pacientes de la base de datos</span></div>
                    </div>
                </section>

                <section class="admin-view" id="admin-view-equipo" data-view-title="Médicos y especialidades">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">DIRECTORIO Y ÁREAS CLÍNICAS</p><h1>Médicos y especialidades</h1><p class="admin-subtitle">{{ $medicos->count() }} médicos · {{ $especialidades->count() }} especialidades</p></div><button class="admin-button admin-button-primary" data-open-modal="medico"><span>＋</span> Invitar médico</button></div>
                    <div class="admin-tabbar" role="tablist" aria-label="Secciones del equipo"><button class="admin-tab is-active" data-local-tab="doctors">Médicos <span>{{ $medicos->count() }}</span></button><button class="admin-tab" data-local-tab="specialties">Especialidades <span>{{ $especialidades->count() }}</span></button></div>
                    <div class="admin-local-panel is-visible" data-tab-panel="doctors"><div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><label class="admin-filter-search"><span>⌕</span><input type="search" data-filter-input="doctors" placeholder="Buscar médico o especialidad"></label><select class="admin-select" data-doctor-specialty><option value="all">Todas las especialidades</option>@foreach ($especialidades as $especialidad)<option value="{{ $especialidad->nombre }}">{{ $especialidad->nombre }}</option>@endforeach</select></div><div class="admin-doctor-grid">
                        @forelse ($medicos as $medico)
                            <article class="admin-doctor-card"><div class="admin-doctor-card-top"><span class="admin-doctor-avatar avatar-sage">{{ mb_substr($medico->user?->name ?? 'M', 0, 1) }}{{ mb_substr($medico->user?->apellido ?? '', 0, 1) }}</span><span class="admin-pill {{ $medico->user?->activo ? 'pill-confirmed' : 'pill-completed' }}">{{ $medico->user?->activo ? 'Activo' : 'Inactivo' }}</span></div><h3>{{ $medico->user?->name }} {{ $medico->user?->apellido }}</h3><p>{{ $medico->especialidad?->nombre ?? 'Sin especialidad' }}</p><div class="admin-doctor-card-meta"><span>Consultorio {{ $medico->consultorio ?: 'sin asignar' }}</span><span>{{ $medico->anios_experiencia ?: 0 }} años</span></div><div class="admin-doctor-card-bottom">{{ $medico->consultas_count }} consultas registradas</div></article>
                        @empty
                            <p class="admin-empty-inline">No hay médicos registrados. Invita a un médico para iniciar.</p>
                        @endforelse
                    </div><p class="admin-filter-empty" hidden>No hay médicos que coincidan con la búsqueda.</p></div></div>
                    <div class="admin-local-panel" data-tab-panel="specialties"><div class="admin-panel admin-specialty-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">CATÁLOGO DE SERVICIOS</p><h2>Especialidades disponibles</h2></div><button class="admin-outline-button" data-open-modal="especialidad"><span>＋</span> Nueva especialidad</button></div><div class="admin-specialty-grid">
                        @forelse ($especialidades as $especialidad)
                            <article class="admin-specialty-card"><span class="specialty-symbol symbol-green">✳</span><div><h3>{{ $especialidad->nombre }}</h3><p>{{ $especialidad->medicos_count }} médicos</p></div><button class="admin-row-menu" type="button" data-edit-specialty="{{ $especialidad->id }}" data-specialty-name="{{ $especialidad->nombre }}" data-specialty-description="{{ $especialidad->descripcion }}">Editar</button></article>
                        @empty
                            <p class="admin-empty-inline">No hay especialidades registradas.</p>
                        @endforelse
                    </div></div></div>
                </section>

                <section class="admin-view" id="admin-view-consultas" data-view-title="Consultas y horarios">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">OPERACIÓN CLÍNICA</p><h1>Consultas y horarios</h1><p class="admin-subtitle">{{ $consultas->count() }} consultas · {{ $horarios->count() }} horarios registrados.</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-stat-strip"><div><span>Por confirmar</span><strong>{{ $consultas->where('estado', 'pendiente')->count() }}</strong></div><div><span>Confirmadas hoy</span><strong>{{ $consultasHoy->where('estado', 'confirmada')->count() }}</strong></div><div><span>Completadas</span><strong>{{ $consultas->where('estado', 'completada')->count() }}</strong></div><div><span>Canceladas</span><strong>{{ $consultas->where('estado', 'cancelada')->count() }}</strong></div></div>
                    <div class="admin-tabbar" role="tablist" aria-label="Consultas y horarios"><button class="admin-tab is-active" data-local-tab="appointments">Consultas <span>{{ $consultas->count() }}</span></button><button class="admin-tab" data-local-tab="hours">Horarios por médico <span>{{ $horarios->count() }}</span></button></div>
                    <div class="admin-local-panel is-visible" data-tab-panel="appointments"><div class="admin-panel admin-list-panel"><div class="admin-list-toolbar"><div class="admin-filter-tabs" data-status-filter="appointments"><button class="is-active" data-status="all">Todas</button><button data-status="pending">Pendientes</button><button data-status="confirmed">Confirmadas</button><button data-status="completed">Completadas</button><button data-status="cancelled">Canceladas</button></div><label class="admin-filter-search compact-search"><span>⌕</span><input type="search" data-filter-input="appointments" placeholder="Buscar"></label></div><div class="admin-table-wrap"><table class="admin-table" data-filter-table="appointments"><thead><tr><th>PACIENTE</th><th>MÉDICO Y ÁREA</th><th>FECHA Y HORA</th><th>MOTIVO</th><th>ESTADO</th><th></th></tr></thead><tbody>
                        @forelse ($consultas as $consulta)
                            @php($appointmentStatus = ['pendiente' => 'pending', 'confirmada' => 'confirmed', 'completada' => 'completed', 'cancelada' => 'cancelled'][$consulta->estado] ?? 'pending')
                            <tr data-status="{{ $appointmentStatus }}"><td><span class="table-person"><span class="table-avatar">{{ mb_substr($consulta->paciente?->user?->name ?? 'P', 0, 1) }}{{ mb_substr($consulta->paciente?->user?->apellido ?? '', 0, 1) }}</span>{{ $consulta->paciente?->user?->name }} {{ $consulta->paciente?->user?->apellido }}</span></td><td>{{ $consulta->medico?->user?->name }} {{ $consulta->medico?->user?->apellido }}<small>{{ $consulta->medico?->especialidad?->nombre ?? 'Sin especialidad' }}</small></td><td>{{ $consulta->fecha_hora->format('d M Y · H:i') }}</td><td>{{ $consulta->motivo ?: 'Consulta general' }}</td><td><span class="admin-pill pill-{{ $appointmentStatus === 'pending' ? 'pending' : ($appointmentStatus === 'completed' || $appointmentStatus === 'cancelled' ? 'completed' : 'confirmed') }}">{{ ucfirst($consulta->estado) }}</span></td><td><form class="admin-inline-form" method="POST" action="{{ route('admin.consultas.update', $consulta) }}">@csrf @method('PUT')<select name="estado" aria-label="Estado de la consulta de {{ $consulta->paciente?->user?->name }}"><option value="pendiente" @selected($consulta->estado === 'pendiente')>Pendiente</option><option value="confirmada" @selected($consulta->estado === 'confirmada')>Confirmada</option><option value="completada" @selected($consulta->estado === 'completada')>Completada</option><option value="cancelada" @selected($consulta->estado === 'cancelada')>Cancelada</option></select><button class="admin-row-menu" type="submit">Guardar</button></form></td></tr>
                        @empty
                            <tr><td colspan="6" class="admin-empty-inline">No hay consultas registradas.</td></tr>
                        @endforelse
                        <tr class="admin-empty-row" hidden><td colspan="6">No hay consultas que coincidan con los filtros.</td></tr>
                    </tbody></table></div><div class="admin-pagination"><span>{{ $consultas->count() }} consultas de la base de datos</span></div></div></div>
                    <div class="admin-local-panel" data-tab-panel="hours"><div class="admin-panel admin-hours-panel"><div class="admin-panel-heading"><div><p class="admin-eyebrow">DISPONIBILIDAD SEMANAL</p><h2>Horarios por médico</h2></div><button class="admin-outline-button" data-open-modal="horario"><span>＋</span> Agregar horario</button></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>MÉDICO</th><th>DÍA</th><th>HORARIO</th><th>DURACIÓN</th><th>ESTADO</th><th></th></tr></thead><tbody>
                        @forelse ($horarios as $horario)
                            <tr><td><span class="table-person"><span class="table-avatar">{{ mb_substr($horario->medico?->user?->name ?? 'M', 0, 1) }}{{ mb_substr($horario->medico?->user?->apellido ?? '', 0, 1) }}</span>{{ $horario->medico?->user?->name }} {{ $horario->medico?->user?->apellido }}</span><small>{{ $horario->medico?->especialidad?->nombre ?? 'Sin especialidad' }} · Consultorio {{ $horario->medico?->consultorio ?: 'sin asignar' }}</small></td><td>{{ $horario->dia_semana }}</td><td>{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</td><td>{{ $horario->duracion_minutos }} min</td><td><span class="admin-pill {{ $horario->activo ? 'pill-confirmed' : 'pill-completed' }}">{{ $horario->activo ? 'Activo' : 'Inactivo' }}</span></td><td><form method="POST" action="{{ route('admin.horarios.estado', $horario) }}">@csrf @method('PATCH')<input type="hidden" name="activo" value="{{ $horario->activo ? 0 : 1 }}"><button class="admin-row-menu" type="submit">{{ $horario->activo ? 'Desactivar' : 'Activar' }}</button></form></td></tr>
                        @empty
                            <tr><td colspan="6" class="admin-empty-inline">Todavía no hay horarios definidos.</td></tr>
                        @endforelse
                    </tbody></table></div></div></div>
                </section>

                <section class="admin-view" id="admin-view-agenda" data-view-title="Agenda">
                    <div class="admin-page-heading"><div><p class="admin-eyebrow">AGENDA CLÍNICA</p><h1>Agenda de consultas</h1><p class="admin-subtitle">Vista de equipo · horarios y citas ficticias</p></div><button class="admin-button admin-button-primary" data-open-modal="consulta"><span>＋</span> Nueva consulta</button></div>
                    <div class="admin-panel admin-calendar-panel">
                        <div class="admin-calendar-toolbar"><div class="admin-calendar-date"><div class="calendar-month-mark"><span>OCT</span><strong>23</strong></div><div><h2 data-calendar-heading>Viernes, 23 de octubre</h2><span>Semana 43 · 2026</span></div></div><div class="admin-calendar-controls"><button class="calendar-arrow" data-calendar-step="-1" aria-label="Periodo anterior">‹</button><button class="admin-today-button" data-calendar-today>Hoy</button><button class="calendar-arrow" data-calendar-step="1" aria-label="Periodo siguiente">›</button><div class="admin-segmented" role="group" aria-label="Vista de agenda"><button class="is-active" data-calendar-mode="day">Día</button><button data-calendar-mode="week">Semana</button></div></div></div>
                        <div class="admin-calendar-legend"><span><i class="legend-dot dot-green"></i>Confirmada</span><span><i class="legend-dot dot-coral"></i>Pendiente</span><span><i class="legend-dot dot-yellow"></i>Completada</span><label><select class="admin-select" data-calendar-doctor-filter aria-label="Filtrar agenda por médico"><option value="all">Todos los médicos</option>@foreach ($medicos as $medico)<option value="{{ $medico->id }}">{{ $medico->user?->name }} {{ $medico->user?->apellido }}</option>@endforeach</select></label></div>
                        <div class="admin-calendar-scroll"><div class="admin-calendar admin-calendar-day" data-calendar-grid></div><div class="admin-calendar admin-calendar-week" data-calendar-week hidden></div></div><div class="admin-calendar-footer"><span data-agenda-count>{{ $consultas->count() }} consultas registradas</span><i class="duty-dot"></i><span>{{ $medicos->count() }} médicos</span><button class="admin-text-button" data-go-view="consultas">Administrar consultas <span>→</span></button></div>
                    </div>
                </section>
                <footer class="admin-footer"><span>Vista de administración · Operaciones según tus permisos</span><span>SIGC · Gestión clínica</span></footer>
            </main>
        </div>
    </div>
    <script type="application/json" id="admin-form-options">@json($formOptions)</script>
    <script type="application/json" id="admin-agenda-data">@json($agendaData)</script>
    <dialog class="admin-modal" aria-labelledby="admin-modal-title"><form method="POST" class="admin-modal-card" data-admin-form>@csrf<input type="hidden" name="_method" value="POST" data-admin-method><button type="button" class="admin-modal-close" data-close-admin-modal aria-label="Cerrar">×</button><p class="admin-eyebrow">GESTIÓN DE CLÍNICA</p><h2 id="admin-modal-title">Nuevo registro</h2><p class="admin-modal-description">Los cambios se guardan en el sistema.</p><div class="admin-modal-fields" data-modal-fields></div><div class="admin-modal-actions"><button type="button" class="admin-outline-button" data-close-admin-modal>Cancelar</button><button type="submit" class="admin-button admin-button-primary" data-admin-submit>Guardar</button></div></form></dialog>
    <div class="admin-toast" role="status" aria-live="polite"></div>
</body>
</html>