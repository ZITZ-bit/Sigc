<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Médicos y Horarios — {{ config('app.name') }}</title>
    <meta name="description" content="Gestión de médicos del sistema.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/coordinacion.css', 'resources/js/app.js'])
</head>
<body>

    <div class="coordinacion-page">
        @include('portal::coordinacion.sidebar')

        <div class="coordinacion-main">
            <div class="coordinacion-content-area">
                <div class="coordinacion-header">
                    <div class="coordinacion-header-content">
                        <h1>Médicos y horarios</h1>
                        <p>Administre el registro de médicos, sus especialidades y estado de cuenta.</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <a href="{{ route('coordinacion.medicos.registrar') }}" class="coordinacion-btn-primary">Registrar médico</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="coordinacion-card coordinacion-mb-2" style="background-color: var(--coordinacion-color-success-soft); border-color: var(--coordinacion-color-success);">
                        <p style="color: var(--coordinacion-color-success); margin: 0; font-size: 0.9375rem;">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('invitacion_url'))
                    <div class="coordinacion-card coordinacion-mb-2" style="background-color: var(--coordinacion-color-warning-soft, #fff8e1); border-color: var(--coordinacion-color-warning, #f0ad4e);">
                        <p style="margin: 0 0 0.5rem; font-size: 0.9375rem;"><strong>Enlace de activación (visible solo una vez):</strong></p>
                        <a href="{{ session('invitacion_url') }}" style="word-break: break-all; font-size: 0.875rem;">{{ session('invitacion_url') }}</a>
                        <p style="margin: 0.5rem 0 0; font-size: 0.8125rem;">Compártalo con el médico para que establezca su contraseña. Caduca en 7 días.</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="coordinacion-card coordinacion-mb-2" style="background-color: var(--coordinacion-color-error-soft); border-color: var(--coordinacion-color-error);">
                        <ul style="color: var(--coordinacion-color-error); margin: 0; padding-left: 1rem; font-size: 0.9375rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="coordinacion-section">
                    <div class="coordinacion-medicos-header">
                        <form method="GET" action="{{ route('coordinacion.medicos') }}" class="coordinacion-medicos-filters" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
                            <div style="flex: 1; min-width: 200px;">
                                <input type="text" name="search" class="coordinacion-configuracion-input" placeholder="Buscar por nombre, correo o documento..." value="{{ old('search', $search) }}">
                            </div>
                            <div style="min-width: 150px;">
                                <select name="especialidad_id" class="coordinacion-configuracion-input">
                                    <option value="">Todas las especialidades</option>
                                    @foreach ($especialidades as $esp)
                                        <option value="{{ $esp->id }}" {{ $filtro_especialidad == $esp->id ? 'selected' : '' }}>{{ $esp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 150px;">
                                <select name="departamento_id" class="coordinacion-configuracion-input">
                                    <option value="">Todos los departamentos</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" {{ $filtro_departamento == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 140px;">
                                <select name="estado" class="coordinacion-configuracion-input">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ $filtro_estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="activo" {{ $filtro_estado === 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ $filtro_estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                            <button type="submit" class="coordinacion-btn-primary coordinacion-btn-sm">Filtrar</button>
                            @if ($search || $filtro_especialidad || $filtro_departamento || $filtro_estado)
                                <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-btn-secondary coordinacion-btn-sm">Limpiar</a>
                            @endif
                        </form>
                    </div>

                    @if ($medicos->count() > 0)
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
                                                    <a href="{{ route('coordinacion.medicos.horarios', $medico) }}" class="coordinacion-medicos-btn coordinacion-medicos-btn--horarios" aria-label="Gestionar horarios">Horarios</a>
                                                    <a href="{{ route('coordinacion.medicos.editar', $medico->id) }}" class="coordinacion-medicos-btn coordinacion-medicos-btn--edit" aria-label="Editar">Editar</a>
                                                    @if ($medico->estado === 'activo')
                                                        <form method="POST" action="{{ route('coordinacion.medicos.destroy', $medico->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="coordinacion-medicos-btn coordinacion-medicos-btn--deactivate" aria-label="Desactivar" onclick="return confirm('¿Está seguro de que desea desactivar a este médico?')">Desactivar</button>
                                                        </form>
                                                    @elseif ($medico->estado === 'inactivo')
                                                        <form method="POST" action="{{ route('coordinacion.medicos.activate', $medico->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="coordinacion-medicos-btn coordinacion-medicos-btn--activate" aria-label="Activar">Activar</button>
                                                        </form>
                                                    @endif
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

                        <div class="coordinacion-text-center coordinacion-mt-2">
                            {{ $medicos->links() }}
                        </div>
                    @else
                        <div class="coordinacion-medicos-empty">
                            <span class="coordinacion-medicos-empty-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                            <p>No se encontraron médicos con los criterios seleccionados.</p>
                            @if (Auth::user()->role->slug === 'coordinador')
                                <a href="{{ route('coordinacion.medicos.registrar') }}" class="coordinacion-btn-primary">Registrar primer médico</a>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.coordinacion-mobile-toggle');
            var close = document.querySelector('.coordinacion-mobile-close');
            var sidebar = document.getElementById('coordinacion-mobile-sidebar');
            var overlay = document.querySelector('.coordinacion-sidebar-overlay');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    if (overlay) overlay.classList.toggle('open');
                });
            }

            if (close && sidebar) {
                close.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
            }
        });
    </script>
</body>
</html>