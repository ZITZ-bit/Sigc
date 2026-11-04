@if ($medicos->count() > 0)
    <div class="coordinacion-table-scroll" role="region" aria-label="Tabla de médicos; desplázate horizontalmente para ver todas las columnas" tabindex="0">
        <table class="coordinacion-medicos-table">
            <thead>
                <tr>
                    <th>Médico</th>
                    <th>Correo</th>
                    <th>Especialidad</th>
                    <th>Departamento</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicos as $medico)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--coordinacion-color-ink);">{{ $medico->user->name }}</div>
                            <div style="font-size: 0.8125rem; color: var(--coordinacion-color-text-muted); font-family: monospace;">ID: {{ $medico->id }}</div>
                        </td>
                        <td>{{ $medico->user->email }}</td>
                        <td>{{ $medico->especialidad?->name ?? 'No asignada' }}</td>
                        <td>{{ $medico->departamento?->name ?? 'No asignado' }}</td>
                        <td>{{ $medico->telefono ?? '—' }}</td>
                        <td>
                            <span class="coordinacion-badge
                                {{ $medico->estado === 'activo' ? 'coordinacion-badge--confirmed' : ($medico->estado === 'pendiente' ? 'coordinacion-badge--pending' : 'coordinacion-badge--cancelled') }}
                            ">
                                {{ ucfirst($medico->estado) }}
                            </span>
                        </td>
                        <td>
                            <div class="coordinacion-medicos-actions">
                                @if (Auth::user()->role?->slug === 'coordinador')
                                    <a href="{{ route('coordinacion.medicos.horarios', $medico) }}" class="coordinacion-medicos-btn coordinacion-medicos-btn--horarios" aria-label="Gestionar horarios">
                                        <i data-lucide="calendar-clock" aria-hidden="true"></i>
                                        <span>Horarios</span>
                                    </a>
                                    <a href="{{ route('coordinacion.medicos.editar', $medico->id) }}" class="coordinacion-medicos-btn coordinacion-medicos-btn--edit" aria-label="Editar">Editar</a>
                                    <form method="POST" action="{{ route('coordinacion.medicos.remove', $medico) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="coordinacion-medicos-btn coordinacion-medicos-btn--delete" aria-label="Eliminar" onclick="return confirm('¿Eliminar permanentemente la cuenta de este médico? Esta acción no se puede deshacer.')">Eliminar</button>
                                    </form>
                                    @if ($medico->estado === 'pendiente')
                                        <form method="POST" action="{{ route('coordinacion.medicos.invite', $medico->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="coordinacion-medicos-btn coordinacion-medicos-btn--invite" aria-label="Enviar invitación">Invitar</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="coordinacion-pagination">
        {{ $medicos->appends(request()->except('page'))->links() }}
    </div>
@else
    <div class="coordinacion-medicos-empty">
        <span class="coordinacion-medicos-empty-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
        @if ($search || $filtro_especialidad || $filtro_departamento || $filtro_estado)
            <p>No se encontraron médicos con los criterios seleccionados.</p>
        @else
            <p>No hay médicos registrados.</p>
        @endif
        @if (Auth::user()->role->slug === 'coordinador')
            <a href="{{ route('coordinacion.medicos.registrar') }}" class="coordinacion-btn-primary">Registrar primer médico</a>
        @endif
    </div>
@endif
