<div class="medico-pacientes-grid">
    @forelse ($pacientes as $paciente)
        <article class="medico-paciente-card">
            <div class="medico-paciente-avatar" aria-hidden="true">{{ strtoupper(substr($paciente->name, 0, 1)) }}</div>
            <p class="medico-paciente-name">{{ $paciente->name }}</p>
            <span class="medico-paciente-id">ID: {{ $paciente->id }}</span>
            <p class="medico-paciente-info">{{ $paciente->email }}</p>
            <div class="medico-paciente-actions">
                <a href="{{ route('medico.consultas', ['search' => $paciente->name]) }}" class="medico-paciente-btn medico-paciente-btn--view">Ver consultas</a>
            </div>
        </article>
    @empty
        <div class="medico-empty-state">
            @if ($search)
                <strong>No se encontraron pacientes</strong>
                <p>No se encontraron pacientes que coincidan con "{{ $search }}".</p>
            @else
                <strong>No tienes pacientes asignados</strong>
                <p>Los pacientes vinculados a tus consultas aparecerán aquí.</p>
            @endif
        </div>
    @endforelse
</div>

@if ($pacientes instanceof \Illuminate\Contracts\Pagination\Paginator && $pacientes->isNotEmpty())
    <div style="margin-top:1.5rem;">
        {{ $pacientes->appends(request()->except('page'))->links() }}
    </div>
@endif
