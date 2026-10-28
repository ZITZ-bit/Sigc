<div class="coordinacion-notificaciones-list">
    @forelse ($notifications as $notification)
        <article class="coordinacion-notificacion-item{{ is_null($notification->read_at) ? ' coordinacion-notificacion-item--unread' : '' }}">
            <span class="coordinacion-notificacion-icon" aria-hidden="true"><i data-lucide="clipboard-plus"></i></span>
            <div class="coordinacion-notificacion-content">
                <h2 class="coordinacion-notificacion-title">{{ $notification->data['message'] ?? 'Aviso de consulta' }}</h2>
                @if (!empty($notification->data['paciente']))
                    <p class="coordinacion-notificacion-text"><i data-lucide="user-round" aria-hidden="true"></i><strong>Paciente:</strong> {{ $notification->data['paciente'] }}</p>
                @endif
                @if (!empty($notification->data['especialidad']))
                    <p class="coordinacion-notificacion-text"><i data-lucide="stethoscope" aria-hidden="true"></i><strong>Especialidad:</strong> {{ $notification->data['especialidad'] }}</p>
                @endif
                @if (!empty($notification->data['fecha']))
                    <p class="coordinacion-notificacion-appointment">
                        <i data-lucide="calendar-days" aria-hidden="true"></i>
                        <span>{{ \Carbon\Carbon::parse($notification->data['fecha'])->locale('es')->translatedFormat('l d \d\e F \d\e Y') }}</span>
                        @if (!empty($notification->data['hora']))
                            <span class="coordinacion-notificacion-appointment-time">{{ \Carbon\Carbon::parse($notification->data['hora'])->format('H:i') }}</span>
                        @endif
                    </p>
                @endif
                <time class="coordinacion-notificacion-time" datetime="{{ $notification->created_at->toIso8601String() }}">
                    Aviso recibido el {{ $notification->created_at->format('d/m/Y H:i') }}
                    · {{ is_null($notification->read_at) ? 'Sin leer' : 'Leída' }}
                </time>
            </div>
            @if (is_null($notification->read_at))
                <form method="POST" action="{{ route('coordinacion.notificaciones.leer', $notification->id) }}">
                    @csrf
                    <button type="submit" class="coordinacion-filter-btn">Marcar como leída</button>
                </form>
            @endif
        </article>
    @empty
        <div class="coordinacion-notificaciones-empty">
            <div class="coordinacion-notificaciones-empty-icon" aria-hidden="true">♡</div>
            <strong>No hay solicitudes nuevas</strong>
            <p>Cuando un paciente solicite una consulta, recibirás aquí los datos para darle seguimiento.</p>
        </div>
    @endforelse

    @if ($notifications->hasPages())
        {{ $notifications->links() }}
    @endif
</div>
