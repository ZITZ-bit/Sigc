<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Editar Médico — {{ config('app.name') }}</title>
    <meta name="description" content="Editar datos del médico.">
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
                        <h1>Editar médico</h1>
                        <p>Modifique los datos del médico {{ $medico->user->name }}.</p>
                    </div>
                    <div class="coordinacion-header-actions">
                        <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-btn-secondary">Volver al listado</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="coordinacion-card coordinacion-mb-2" style="background-color: var(--coordinacion-color-success-soft); border-color: var(--coordinacion-color-success);">
                        <p style="color: var(--coordinacion-color-success); margin: 0; font-size: 0.9375rem;">{{ session('success') }}</p>
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
                    <form method="POST" action="{{ route('coordinacion.medicos.update', $medico->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="coordinacion-card">
                            <h3 class="coordinacion-section-title">Datos personales</h3>
                            <div class="coordinacion-grid coordinacion-grid-cols-2">
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Nombre</label>
                                    <input type="text" name="name" class="coordinacion-configuracion-input" value="{{ old('name', $medico->user->name) }}" required>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Apellido</label>
                                    <input type="text" name="apellido" class="coordinacion-configuracion-input" value="{{ old('apellido', $medico->user->name) }}" required>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Documento de identidad</label>
                                    <input type="text" name="documento_identidad" class="coordinacion-configuracion-input" value="{{ old('documento_identidad') }}">
                                    <p class="coordinacion-configuracion-help">Identificador único del médico.</p>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Correo electrónico</label>
                                    <input type="email" name="email" class="coordinacion-configuracion-input" value="{{ old('email', $medico->user->email) }}" required>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Teléfono</label>
                                    <input type="tel" name="telefono" class="coordinacion-configuracion-input" value="{{ old('telefono', $medico->telefono) }}">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Dirección</label>
                                    <input type="text" name="direccion" class="coordinacion-configuracion-input" value="{{ old('direccion', $medico->direccion) }}">
                                </div>
                            </div>
                        </div>

                        <div class="coordinacion-card coordinacion-mt-2">
                            <h3 class="coordinacion-section-title">Datos profesionales</h3>
                            <div class="coordinacion-grid coordinacion-grid-cols-2">
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Número de licencia</label>
                                    <input type="text" name="numero_licencia" class="coordinacion-configuracion-input" value="{{ old('numero_licencia', $medico->numero_licencia) }}">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Especialidad</label>
                                    <select name="especialidad_id" class="coordinacion-configuracion-input">
                                        <option value="">Seleccionar especialidad</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->id }}" {{ old('especialidad_id', $medico->especialidad_id) == $esp->id ? 'selected' : '' }}>{{ $esp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Departamento</label>
                                    <select name="departamento_id" class="coordinacion-configuracion-input">
                                        <option value="">Seleccionar departamento</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep->id }}" {{ old('departamento_id', $medico->departamento_id) == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Fecha de incorporación</label>
                                    <input type="date" name="fecha_incorporacion" class="coordinacion-configuracion-input" value="{{ old('fecha_incorporacion', $medico->fecha_incorporacion ? \Carbon\Carbon::parse($medico->fecha_incorporacion)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Estado de la cuenta</label>
                                    <select name="estado" class="coordinacion-configuracion-input">
                                        <option value="activo" {{ old('estado', $medico->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado', $medico->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="pendiente" {{ old('estado', $medico->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="coordinacion-medicos-actions coordinacion-mt-2">
                            <button type="submit" class="coordinacion-btn-primary">Guardar cambios</button>
                            <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-btn-secondary">Cancelar</a>
                        </div>
                    </form>
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