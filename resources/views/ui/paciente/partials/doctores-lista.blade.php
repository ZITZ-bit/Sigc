<div class="paciente-doctores-grid">
    @forelse ($medicos as $medico)
        <article class="paciente-doctor-card">
            <div class="paciente-doctor-avatar" aria-hidden="true">{{ strtoupper(substr($medico->user->name ?? 'M', 0, 1)) }}</div>
            <p class="paciente-doctor-name">{{ $medico->user->name ?? 'Médico sin nombre' }}</p>
            <span class="paciente-doctor-specialty">{{ $medico->especialidad?->name ?? 'Sin especialidad asignada' }}</span>
            <p class="paciente-doctor-bio">{{ $medico->departamento?->name ?? 'Departamento no asignado' }}</p>
            <a href="{{ route('paciente.consulta') }}" class="paciente-doctor-btn">Solicitar consulta</a>
        </article>
    @empty
        <div class="paciente-empty-state">
            @if ($search)
                <strong>No se encontraron profesionales</strong>
                <p>No se encontraron profesionales que coincidan con "{{ $search }}".</p>
            @else
                <strong>No hay profesionales disponibles</strong>
                <p>Los médicos activos aparecerán aquí cuando estén disponibles.</p>
            @endif
        </div>
    @endforelse
</div>
