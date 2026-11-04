@if ($pacientes->isEmpty())
    <div class="coordinacion-empty-state">
        @if ($search)
            <strong>No se encontraron pacientes</strong>
            <p>No se encontraron pacientes que coincidan con "{{ $search }}".</p>
        @else
            <strong>No hay pacientes registrados</strong>
            <p>Los pacientes aparecerán aquí cuando se registren solicitudes de consulta.</p>
        @endif
    </div>
@else
    <div class="coordinacion-pacientes-list">
        <div class="coordinacion-table-scroll" role="region" aria-label="Tabla de pacientes; desplázate horizontalmente para ver todas las columnas" tabindex="0">
            <table class="coordinacion-pacientes-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo electrónico</th>
                        <th>Fecha de registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pacientes as $paciente)
                        <tr>
                            <td>{{ $paciente->name }}</td>
                            <td>{{ $paciente->email }}</td>
                            <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('coordinacion.pacientes.show', $paciente) }}" class="coordinacion-paciente-btn coordinacion-paciente-btn--ver">Ver detalle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="coordinacion-pagination">
            {{ $pacientes->appends(request()->except('page'))->links() }}
        </div>
    </div>
@endif
