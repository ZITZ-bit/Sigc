<div class="paciente-notificaciones-list">
    @forelse ($notifications as $notification)
        <article class="paciente-notificacion-item{{ is_null($notification->read_at) ? ' paciente-notificacion-item--unread' : '' }}">
            <span class="paciente-notificacion-icon" aria-hidden="true"><i data-lucide="calendar-check-2"></i></span>
            <div class="paciente-notificacion-content">
                <h2 class="paciente-notificacion-title">{{ $notification->data['message'] ?? 'Actualización de tu consulta' }}</h2>
                <p class="paciente-notificacion-text">
                    Tu consulta fue aceptada
                    @if (!empty($notification->data['medico']))
                        · Médico asignado: {{ $notification->data['medico'] }}
                    @endif
                    @if (!empty($notification->data['especialidad']))
                        · Especialidad: {{ $notification->data['especialidad'] }}
                    @endif
                </p>
                @if (!empty($notification->data['fecha']))
                    <p class="paciente-notificacion-appointment">
                        <i data-lucide="calendar-days" aria-hidden="true"></i>
                        <span>{{ \Carbon\Carbon::parse($notification->data['fecha'])->locale('es')->translatedFormat('l d \d\e F \d\e Y') }}</span>
                        @if (!empty($notification->data['hora']))
                            <span class="paciente-notificacion-appointment-time">{{ \Carbon\Carbon::parse($notification->data['hora'])->format('H:i') }}</span>
                        @endif
                    </p>
                @endif
                <time class="paciente-notificacion-time" datetime="{{ $notification->created_at->toIso8601String() }}">
                    Aviso recibido el {{ $notification->created_at->format('d/m/Y H:i') }}
                    · {{ is_null($notification->read_at) ? 'Sin leer' : 'Leída' }}
                </time>
            </div>
            @if (is_null($notification->read_at))
                <form method="POST" action="{{ route('paciente.notificaciones.leer', $notification->id) }}">
                    @csrf
                    <button type="submit" class="paciente-filter-btn">Marcar como leída</button>
                </form>
            @endif
        </article>
    @empty
        <div class="paciente-notificaciones-empty">
            <div class="paciente-notificaciones-empty-icon" aria-hidden="true">♡</div>
            <strong>Aún no tienes consultas aceptadas</strong>
            <p>Cuando coordinación confirme una consulta, recibirás aquí la fecha, la hora y el médico asignado.</p>
        </div>
    @endforelse

    @if ($notifications->hasPages())
        {{ $notifications->links() }}
    @endif
</div>
