<div class="medico-notificaciones-list">
    @forelse ($notifications as $notification)
        <article class="medico-notificacion-item{{ is_null($notification->read_at) ? ' medico-notificacion-item--unread' : '' }}">
            <span class="medico-notificacion-icon" aria-hidden="true"><i data-lucide="user-round-plus"></i></span>
            <div class="medico-notificacion-content">
                <h2 class="medico-notificacion-title">{{ $notification->data['message'] ?? 'Aviso de consulta' }}</h2>
                @if (!empty($notification->data['paciente']))
                    <p class="medico-notificacion-text"><i data-lucide="user-round" aria-hidden="true"></i><strong>Paciente:</strong> {{ $notification->data['paciente'] }}</p>
                @endif
                @if (!empty($notification->data['especialidad']))
                    <p class="medico-notificacion-text"><i data-lucide="stethoscope" aria-hidden="true"></i><strong>Especialidad:</strong> {{ $notification->data['especialidad'] }}</p>
                @endif
                @if (!empty($notification->data['fecha']))
                    <p class="medico-notificacion-appointment">
                        <i data-lucide="calendar-days" aria-hidden="true"></i>
                        <span>{{ \Carbon\Carbon::parse($notification->data['fecha'])->locale('es')->translatedFormat('l d \d\e F \d\e Y') }}</span>
                        @if (!empty($notification->data['hora']))
                            <span class="medico-notificacion-appointment-time">{{ \Carbon\Carbon::parse($notification->data['hora'])->format('H:i') }}</span>
                        @endif
                    </p>
                @endif
                <time class="medico-notificacion-time" datetime="{{ $notification->created_at->toIso8601String() }}">
                    Aviso recibido el {{ $notification->created_at->format('d/m/Y H:i') }}
                    · {{ is_null($notification->read_at) ? 'Sin leer' : 'Leída' }}
                </time>
            </div>
            @if (is_null($notification->read_at))
                <form method="POST" action="{{ route('medico.notificaciones.leer', $notification->id) }}">
                    @csrf
                    <button type="submit" class="medico-filter-btn">Marcar como leída</button>
                </form>
            @endif
        </article>
    @empty
        <div class="medico-notificaciones-empty">
            <div class="medico-notificaciones-empty-icon" aria-hidden="true">◷</div>
            <strong>No tienes consultas asignadas recientemente</strong>
            <p>Cuando coordinación te asigne una consulta, recibirás aquí los datos de la cita.</p>
        </div>
    @endforelse

    @if ($notifications->hasPages())
        {{ $notifications->links() }}
    @endif
</div>
