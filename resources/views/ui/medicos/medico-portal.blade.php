<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Consultorio | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/doctor.css', 'resources/js/doctor.js'])
</head>
<body class="doctor-page">
    <div class="doctor-shell">
        <aside class="doctor-rail" aria-label="Navegación del consultorio">
            <a class="doctor-mark" href="{{ url('/') }}" aria-label="Volver al portal principal">S</a>
            <nav class="doctor-nav" aria-label="Secciones médicas">
                <button class="doctor-nav-item is-active" type="button" data-doctor-view="agenda" aria-label="Mi agenda" title="Mi agenda">◷</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="pacientes" aria-label="Mis pacientes" title="Mis pacientes">♧</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="notas" aria-label="Notas clínicas" title="Notas clínicas">▤</button>
                <button class="doctor-nav-item" type="button" data-doctor-view="horario" aria-label="Mi disponibilidad" title="Mi disponibilidad">◫</button>
            </nav>
            <a class="doctor-home-link" href="{{ url('/') }}" aria-label="Volver al portal principal" title="Portal principal"><span>⌂</span><small>Portal</small></a>
        </aside>

        <main class="doctor-main">
            <header class="doctor-topbar">
                <div class="doctor-brand"><span class="doctor-brand-mark">S</span><strong>SIGC</strong><span class="doctor-divider"></span><span>Consultorio médico</span></div>
                <div class="doctor-topbar-tools"><a class="doctor-return-link" href="{{ url('/') }}">← Portal principal</a><span class="doctor-account"><span class="doctor-avatar doctor-avatar-sage">{{ mb_substr($user->name, 0, 1) }}{{ mb_substr($user->apellido ?? '', 0, 1) }}</span><span><strong>{{ $user->name }} {{ $user->apellido }}</strong><small>{{ $medico->especialidad?->nombre }} · {{ $medico->clinica?->nombre ?? 'Sin clínica asignada' }}</small></span></span><form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="doctor-secondary-button" title="Cerrar sesión" aria-label="Cerrar sesión">Cerrar sesión</button></form></div>
            </header>

            <div class="doctor-content">
                @if (session('success'))
                    <div class="doctor-feedback doctor-feedback-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="doctor-feedback doctor-feedback-error" role="alert">{{ $errors->first() }}</div>
                @endif

                @php
                    $consultasHoy = $consultas->filter(fn ($c) => $c->fecha_hora->isToday());
                    $consultasPendientes = $consultas->where('estado', 'pendiente');
                    $statusLabel = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'completada' => 'Completada', 'cancelada' => 'Cancelada'];
                    $pacientes = $consultas->pluck('paciente')->unique('id')->values();
                @endphp

                <!-- Agenda -->
                <section class="doctor-view is-active" id="doctor-view-agenda" data-title="Mi agenda">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">{{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}</p><h1>Tu jornada, {{ $user->name }}</h1><p class="doctor-subtitle">Agenda y seguimiento de tus pacientes.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <div class="doctor-metrics">
                        <div class="doctor-metric tone-mint"><span>Consultas de hoy</span><strong>{{ $consultasHoy->count() }}</strong><small>{{ $consultasHoy->first()?->fecha_hora->format('H:i') ?: 'Sin citas hoy' }} primera cita</small></div>
                        <div class="doctor-metric tone-peach"><span>Por atender</span><strong>{{ $consultasPendientes->count() }}</strong><small>{{ $consultasPendientes->first()?->fecha_hora->format('H:i') ?: 'Sin pendientes' }} siguiente</small></div>
                        <div class="doctor-metric tone-yellow"><span>Notas pendientes</span><strong>{{ $notasPendientes->count() }}</strong><small>Expedientes por completar</small></div>
                    </div>
                    <div class="doctor-work-grid">
                        <section class="doctor-panel doctor-appointments-panel">
                            <div class="doctor-panel-heading"><div><p class="doctor-eyebrow">AGENDA PERSONAL</p><h2>Consultas programadas</h2></div><div class="doctor-day-selector"><span data-doctor-date>{{ now()->locale('es')->translatedFormat('l, d \d\e M') }}</span></div></div>
                            <div class="doctor-appointment-list">
                                @forelse ($consultas as $consulta)
                                    @php($statusClass = $consulta->estado === 'pendiente' ? 'status-pending' : ($consulta->estado === 'completada' ? 'status-done' : 'status-confirmed'))
                                    @php($statusText = $statusLabel[$consulta->estado] ?? $consulta->estado)
                                    <article class="doctor-appointment{{ $consulta->estado === 'completada' ? ' appointment-complete' : '' }}" data-appointment-status="{{ $consulta->estado }}" data-consulta-id="{{ $consulta->id }}">
                                        <time>{{ $consulta->fecha_hora->format('H:i') }}</time><span class="doctor-time-line"></span>
                                        <div class="doctor-appointment-person"><span class="doctor-avatar doctor-avatar-peach">{{ mb_substr($consulta->paciente?->user?->name ?? 'P', 0, 1) }}{{ mb_substr($consulta->paciente?->user?->apellido ?? '', 0, 1) }}</span><div><strong>{{ $consulta->paciente?->user?->name ?? 'Paciente' }} {{ $consulta->paciente?->user?->apellido }}</strong><span>{{ $consulta->motivo ?: 'Consulta general' }} · {{ $consulta->clinica?->nombre ?? ($consulta->medico?->consultorio ? 'Consultorio ' . $consulta->medico->consultorio : 'Sin consultorio') }}</span></div></div>
                                        <span class="doctor-status {{ $statusClass }}">{{ $statusText }}</span>
                                        <button class="doctor-row-action" type="button" data-open-record="{{ $consulta->paciente?->user?->name ?? '' }} {{ $consulta->paciente?->user?->apellido }}">Expediente</button>
                                        @if ($consulta->estado !== 'completada')
                                            <form method="POST" action="{{ route('medico.consulta.atender', $consulta) }}" style="display:inline">@csrf<button class="doctor-complete-action" type="submit">Atendida</button></form>
                                        @else
                                            <button class="doctor-complete-action" type="button" disabled>Completada</button>
                                        @endif
                                    </article>
                                @empty
                                    <p class="doctor-empty">No hay consultas programadas.</p>
                                @endforelse
                            </div>
                        </section>
                        <aside class="doctor-panel doctor-focus-panel">
                            <div class="doctor-panel-heading"><div><p class="doctor-eyebrow">SIGUIENTE PACIENTE</p><h2>{{ $consultasPendientes->first()?->paciente?->user?->name ?: 'Sin pendientes' }} {{ $consultasPendientes->first()?->paciente?->user?->apellido }}</h2></div><span class="doctor-next-mark">{{ $consultasPendientes->first()?->fecha_hora->format('H:i') ?: '--:--' }}</span></div>
                            <div class="doctor-focus-detail"><span class="doctor-avatar doctor-avatar-peach doctor-avatar-large">{{ mb_substr($consultasPendientes->first()?->paciente?->user?->name ?? 'P', 0, 1) }}{{ mb_substr($consultasPendientes->first()?->paciente?->user?->apellido ?? '', 0, 1) }}</span><div><strong>{{ $consultasPendientes->first()?->motivo ?: 'Consulta general' }}</strong><span>{{ $consultasPendientes->first()?->fecha_hora->format('d M Y, H:i') ?: 'Sin cita asignada' }} · {{ $consultasPendientes->first()?->duracion_minutos ?: 30 }} min</span></div></div>
                            @if ($consultasPendientes->first())
                                <button class="doctor-secondary-button" type="button" data-open-record="{{ $consultasPendientes->first()->paciente->user->name }} {{ $consultasPendientes->first()->paciente->user->apellido }}">Abrir expediente <span>→</span></button>
                            @endif
                        </aside>
                    </div>
                    <section class="doctor-panel doctor-notes-strip"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">PARA CERRAR HOY</p><h2>Notas clínicas pendientes</h2></div><button class="doctor-link-button" type="button" data-doctor-view-link="notas">Ver notas <span>→</span></button></div>
                        @forelse ($notasPendientes as $nota)
                            <div class="doctor-pending-note"><span class="doctor-note-dot"></span><span><strong>{{ $nota->paciente?->user?->name ?? 'Paciente' }} {{ $nota->paciente?->user?->apellido }}</strong><small>{{ $nota->consulta?->motivo ?: 'Consulta general' }} · Borrador pendiente</small></span><button type="button" data-open-note="{{ $nota->paciente?->user?->name }} {{ $nota->paciente?->user?->apellido }}">Continuar nota</button></div>
                        @empty
                            <div class="doctor-pending-note"><span>No hay notas pendientes.</span></div>
                        @endforelse
                    </section>
                </section>

                <!-- Mis pacientes -->
                <section class="doctor-view" id="doctor-view-pacientes" data-title="Mis pacientes">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">SEGUIMIENTO CLÍNICO</p><h1>Mis pacientes</h1><p class="doctor-subtitle">Pacientes atendidos por tu consultorio.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <section class="doctor-panel doctor-directory-panel"><div class="doctor-list-toolbar"><label class="doctor-search"><span>⌕</span><input type="search" data-patient-search placeholder="Buscar en mis pacientes" aria-label="Buscar en mis pacientes"></label><select data-patient-filter aria-label="Filtrar pacientes"><option value="all">Todos</option><option value="today">Con cita hoy</option><option value="pending">Por atender</option></select></div>
                        <div class="doctor-table-wrap"><table class="doctor-table"><thead><tr><th>PACIENTE</th><th>ÚLTIMA CONSULTA</th><th>PRÓXIMA CITA</th><th>ESTADO</th><th></th></tr></thead><tbody>
                            @forelse ($pacientes as $paciente)
                                @php
                                    $pConsultas = $consultas->where('paciente_id', $paciente->id)->sortByDesc('fecha_hora');
                                    $last = $pConsultas->first();
                                    $next = $pConsultas->where('estado', '!=', 'completada')->where('fecha_hora', '>', now())->sortBy('fecha_hora')->first();
                                    $pending = $pConsultas->where('estado', 'pendiente')->count();
                                @endphp
                                <tr data-patient-row data-today="{{ $last && $last->fecha_hora->isToday() ? 'yes' : 'no' }}" data-pending="{{ $pending > 0 ? 'yes' : 'no' }}">
                                    <td><span class="doctor-table-person"><span class="doctor-avatar doctor-avatar-peach">{{ mb_substr($paciente->user?->name ?? 'P', 0, 1) }}{{ mb_substr($paciente->user?->apellido ?? '', 0, 1) }}</span><span>{{ $paciente->user?->name ?? 'Paciente' }} {{ $paciente->user?->apellido }}<small>Expediente {{ $paciente->expediente_numero ?: 'pendiente' }}</small></span></span></td>
                                    <td>{{ $last?->fecha_hora->format('d M Y') ?: 'Sin consultas' }}</td>
                                    <td>{{ $next?->fecha_hora->format('d M Y \a \l\a\s H:i') ?: 'Sin cita próxima' }}</td>
                                    <td><span class="doctor-status {{ $pending > 0 ? 'status-pending' : 'status-confirmed' }}">{{ $pending > 0 ? 'Por atender' : 'Al día' }}</span></td>
                                    <td><button class="doctor-row-action" type="button" data-open-record="{{ $paciente->user?->name }} {{ $paciente->user?->apellido }}">Expediente</button></td>
                                </tr>
                            @empty
                                <tr data-patient-empty hidden><td colspan="5">No hay pacientes que coincidan con el filtro.</td></tr>
                            @endforelse
                        </tbody></table></div>
                    </section>
                </section>

                <!-- Notas clínicas -->
                <section class="doctor-view" id="doctor-view-notas" data-title="Notas clínicas">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">DOCUMENTACIÓN DE CONSULTA</p><h1>Notas clínicas</h1><p class="doctor-subtitle">Borradores y notas recientes de tu consultorio.</p></div><button class="doctor-primary-button" type="button" data-doctor-action="new-note">＋ Nueva nota clínica</button></div>
                    <section class="doctor-panel doctor-notes-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">POR COMPLETAR</p><h2>Borradores pendientes</h2></div><span class="doctor-count">{{ $notasPendientes->count() }} pendiente{{ $notasPendientes->count() > 1 ? 's' : '' }}</span></div>
                        @forelse ($notasPendientes as $nota)
                            <article class="doctor-clinical-note"><span class="doctor-avatar doctor-avatar-peach">{{ mb_substr($nota->paciente?->user?->name ?? 'P', 0, 1) }}{{ mb_substr($nota->paciente?->user?->apellido ?? '', 0, 1) }}</span><div><strong>{{ $nota->paciente?->user?->name ?? 'Paciente' }} {{ $nota->paciente?->user?->apellido }}</strong><p>{{ $nota->consulta?->motivo ?: 'Consulta general' }} · {{ $nota->consulta?->fecha_hora->format('d M Y, H:i') ?: 'Sin fecha' }}</p><small>Nota clínica pendiente de firma</small></div><button type="button" class="doctor-primary-button" data-open-note="{{ $nota->paciente?->user?->name }} {{ $nota->paciente?->user?->apellido }}">Continuar nota</button></article>
                        @empty
                            <p class="doctor-empty">No hay notas pendientes de completar.</p>
                        @endforelse
                    </section>
                    <section class="doctor-panel doctor-notes-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">REGISTRO RECIENTE</p><h2>Notas firmadas</h2></div></div>
                        @forelse ($notasFirmadas as $nota)
                            <article class="doctor-saved-note"><div><strong>{{ $nota->paciente?->user?->name ?? 'Paciente' }} {{ $nota->paciente?->user?->apellido }}</strong><span>{{ $nota->firmada_at->format('d M Y') ?: 'Sin fecha' }} · {{ $nota->consulta?->motivo ?: 'Consulta general' }}</span></div><p>{{ $nota->contenido ?: 'Sin contenido registrado.' }}</p><button type="button" class="doctor-link-button" data-open-record="{{ $nota->paciente?->user?->name }} {{ $nota->paciente?->user?->apellido }}">Ver expediente <span>→</span></button></article>
                        @empty
                            <p class="doctor-empty">No hay notas firmadas recientemente.</p>
                        @endforelse
                    </section>
                </section>

                <!-- Mi disponibilidad -->
                <section class="doctor-view" id="doctor-view-horario" data-title="Mi disponibilidad">
                    <div class="doctor-heading"><div><p class="doctor-eyebrow">HORARIO INDIVIDUAL</p><h1>Mi disponibilidad</h1><p class="doctor-subtitle">Turnos de consulta del consultorio de {{ $medico->especialidad?->nombre }}.</p></div><button class="doctor-secondary-button" type="button" data-doctor-action="hours">Editar disponibilidad <span>↗</span></button></div>
                    <section class="doctor-panel doctor-hours-panel"><div class="doctor-panel-heading"><div><p class="doctor-eyebrow">SEMANA ACTUAL</p><h2>Horario de atención</h2></div><span class="doctor-status status-confirmed">Vigente</span></div><div class="doctor-hours-grid">
                        @forelse ($medico->horarios as $horario)
                            <div><strong>{{ $horario->dia_semana }}</strong><span>{{ substr($horario->hora_inicio, 0, 5) }} – {{ substr($horario->hora_fin, 0, 5) }}</span><small>{{ $horario->duracion_minutos }} min por bloque · {{ $horario->activo ? 'Activo' : 'Inactivo' }}</small></div>
                        @empty
                            <p class="doctor-empty">No hay horarios definidos para tu consultorio.</p>
                        @endforelse
                    </div></section>
                    <section class="doctor-panel doctor-room-panel"><p class="doctor-eyebrow">TU CONSULTORIO</p><h2>{{ $user->name }} {{ $user->apellido }}</h2><p>{{ $medico->especialidad?->nombre }} · {{ $medico->consultorio ?: 'Sin consultorio asignado' }}</p><div><span>Duración habitual</span><strong>{{ $medico->horarios->first()?->duracion_minutos ?: 30 }} minutos</strong></div></section>
                </section>
                <footer class="doctor-footer"><span>Panel médico · Datos reales de tu consultorio</span><span>SIGC · Gestión clínica</span></footer>
            </div>
        </main>
    </div>

    <dialog class="doctor-dialog" data-doctor-dialog aria-labelledby="doctor-dialog-title">
        <form method="POST" action="{{ route('medico.nota.crear') }}" class="doctor-dialog-card" data-doctor-form>
            @csrf
            <button type="button" class="doctor-dialog-close" data-close-doctor-dialog aria-label="Cerrar">×</button>
            <p class="doctor-eyebrow">NUEVA NOTA CLÍNICA</p>
            <h2 id="doctor-dialog-title">Registrar nota</h2>
            <label class="doctor-note-field"><span>Paciente</span><select name="paciente_id" required>
                <option value="">Seleccionar paciente</option>
                @foreach ($pacientes as $paciente)
                    <option value="{{ $paciente->id }}">{{ $paciente->user?->name }} {{ $paciente->user?->apellido }}</option>
                @endforeach
            </select></label>
            <label class="doctor-note-field"><span>Consulta (opcional)</span><select name="consulta_id">
                <option value="">Sin consulta asociada</option>
                @foreach ($consultas as $consulta)
                    <option value="{{ $consulta->id }}">{{ $consulta->paciente?->user?->name }} {{ $consulta->paciente?->user?->apellido }} · {{ $consulta->fecha_hora->format('d M Y, H:i') }}</option>
                @endforeach
            </select></label>
            <label class="doctor-note-field"><span>Contenido</span><textarea name="contenido" rows="5" required></textarea></label>
            <label class="doctor-note-field"><span>Diagnóstico</span><textarea name="diagnostico" rows="3"></textarea></label>
            <label class="doctor-note-field"><span>Tratamiento</span><textarea name="tratamiento" rows="3"></textarea></label>
            <div class="doctor-dialog-actions"><button type="button" class="doctor-secondary-button" data-close-doctor-dialog>Cancelar</button><button type="submit" class="doctor-primary-button">Guardar nota</button></div>
        </form>
    </dialog>
    <div class="doctor-toast" role="status" aria-live="polite"></div>
</body>
</html>
