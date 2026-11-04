@forelse ($consultas as $consulta)
    <article class="coordinacion-cita-card" data-consulta-id="{{ $consulta->id }}" data-consulta-estado="{{ $consulta->estado }}">
        <div class="coordinacion-cita-card__header">
            <div class="coordinacion-cita-card__when">
                <span class="coordinacion-cita-card__date">{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</span>
                <span class="coordinacion-cita-card__time">{{ \Carbon\Carbon::parse($consulta->hora)->format('h:i A') }}</span>
            </div>
            <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
        </div>

        <div class="coordinacion-cita-card__details">
            <div>
                <span class="coordinacion-cita-card__label">Paciente</span>
                <strong>{{ $consulta->user->name }}</strong>
                <span class="coordinacion-cita-card__muted">{{ $consulta->user->email }}</span>
            </div>
            <div>
                <span class="coordinacion-cita-card__label">Médico</span>
                <strong>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</strong>
                <span class="coordinacion-cita-card__muted">{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</span>
            </div>
            <div class="coordinacion-cita-card__reason">
                <span class="coordinacion-cita-card__label">Motivo de consulta</span>
                <p>{{ $consulta->motivo ?: 'Sin motivo registrado' }}</p>
            </div>
        </div>

        <div class="coordinacion-cita-card__actions">
            @if ($consulta->estado === 'pendiente')
                <form method="POST" action="{{ route('coordinacion.citas.asignar-medico', $consulta) }}" class="coordinacion-cita-card__assign-form">
                    @csrf
                    <label class="visually-hidden" for="medico-{{ $consulta->id }}">Asignar médico</label>
                    <select id="medico-{{ $consulta->id }}" name="medico_id" class="coordinacion-medico-select" required>
                        <option value="">Asignar médico</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->id }}" {{ old('medico_id', $consulta->medico_id) === $medico->id ? 'selected' : '' }}>{{ $medico->user->name }} ({{ $medico->especialidad?->name }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--asignar">Asignar</button>
                </form>
            @endif

            @if ($consulta->estado === 'confirmada')
                <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}">
                    @csrf
                    <input type="hidden" name="estado" value="completada">
                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--complete">Completar</button>
                </form>
            @elseif ($consulta->estado === 'pendiente')
                <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}">
                    @csrf
                    <input type="hidden" name="estado" value="confirmada">
                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--confirm">Confirmar cita</button>
                </form>
            @endif

            @if (in_array($consulta->estado, ['pendiente', 'confirmada'], true))
                <form method="POST" action="{{ route('coordinacion.citas.estado', $consulta) }}" onsubmit="return confirm('¿Cancelar esta cita?')">
                    @csrf
                    <input type="hidden" name="estado" value="cancelada">
                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--cancel">Cancelar cita</button>
                </form>
            @endif

            @if (! $consulta->historialClinico)
                <form method="POST" action="{{ route('coordinacion.citas.destroy', $consulta) }}" onsubmit="return confirm('¿Eliminar esta cita de forma permanente? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="coordinacion-citas-btn coordinacion-citas-btn--delete">Eliminar cita</button>
                </form>
            @else
                <span class="coordinacion-cita-card__protected">Protegida por historial clínico</span>
            @endif
        </div>
    </article>
@empty
    <div class="coordinacion-empty-state">
        @if ($search || $filtro_estado || $filtro_fecha)
            <strong>No se encontraron citas</strong>
            <p>No se encontraron citas que coincidan con los filtros seleccionados.</p>
        @else
            <strong>No hay consultas registradas</strong>
            <p>Cuando los pacientes registren solicitudes de consulta, aparecerán aquí.</p>
        @endif
    </div>
@endforelse

<div class="coordinacion-pagination">
    {{ $consultas->appends(request()->except('page'))->links() }}
</div>
