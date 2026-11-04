@if (isset($consultas) && $consultas->isNotEmpty())
    @foreach ($consultas as $consulta)
        <tr data-consulta-id="{{ $consulta->id }}" data-consulta-estado="{{ $consulta->estado }}">
            <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($consulta->hora)->format('h:i A') }}</td>
            <td>{{ $consulta->user->name ?? 'N/A' }}</td>
            <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
            <td>{{ $consulta->medico?->user?->name ?? 'Pendiente de asignación' }}</td>
            <td>
                <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6">
            <div class="coordinacion-empty-state">
                @if ($filtro === 'hoy')
                    <strong>No hay pacientes en recepción hoy</strong>
                    <p>Cuando haya citas programadas para hoy, aparecerán aquí.</p>
                @elseif ($filtro === 'semana')
                    <strong>No hay pacientes en recepción esta semana</strong>
                    <p>Cuando haya citas programadas para la semana, aparecerán aquí.</p>
                @else
                    <strong>No hay pacientes en recepción</strong>
                    <p>Cuando haya citas programadas para el periodo seleccionado, aparecerán aquí.</p>
                @endif
            </div>
        </td>
    </tr>
@endif
