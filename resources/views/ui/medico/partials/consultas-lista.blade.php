<table class="medico-agenda-table">
    <thead>
        <tr>
            <th>Paciente</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Especialidad</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @if ($consultas->isEmpty())
            <tr>
                <td colspan="6">
                    <div class="medico-empty-state">
                        @if ($search || $filtro_estado)
                            <strong>No se encontraron consultas</strong>
                            <p>No se encontraron consultas que coincidan con los filtros seleccionados.</p>
                        @else
                            <strong>No tiene consultas asignadas</strong>
                            <p>Cuando los pacientes le asignen una consulta, aparecerá aquí con fecha, hora y estado.</p>
                        @endif
                    </div>
                </td>
            </tr>
        @else
            @foreach ($consultas as $consulta)
                <tr data-consulta-id="{{ $consulta->id }}" data-consulta-estado="{{ $consulta->estado }}">
                    <td>{{ $consulta->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($consulta->hora)->format('h:i A') }}</td>
                    <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
                    <td>
                        <span class="medico-badge medico-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                    </td>
                    <td>
                        <div class="medico-agenda-actions">
                            @if ($consulta->estado === 'confirmada')
                                <form method="POST" action="{{ route('medico.consultas.completar', $consulta) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="medico-agenda-btn medico-agenda-btn--completar">Completar</button>
                                </form>
                            @elseif ($consulta->estado === 'completada' && ! $consulta->historialClinico)
                                <a href="{{ route('medico.historial.clinico.create', $consulta) }}" class="medico-agenda-btn medico-agenda-btn--completar">Registrar nota</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="medico-pagination">
    {{ $consultas->appends(request()->except('page'))->links() }}
</div>
