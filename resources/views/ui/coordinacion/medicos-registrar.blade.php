<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Registrar Médico — {{ config('app.name') }}</title>
    <meta name="description" content="Registrar un nuevo médico en el sistema.">
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
                        <h1>Registrar médico</h1>
                        <p>Crea una nueva cuenta de médico en el sistema.</p>
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
                    <form method="POST" action="{{ route('coordinacion.medicos.store') }}">
                        @csrf

                        <div class="coordinacion-card">
                            <h3 class="coordinacion-section-title">Datos personales</h3>
                            <div class="coordinacion-grid coordinacion-grid-cols-2">
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Nombre</label>
                                    <input type="text" name="name" class="coordinacion-configuracion-input" value="{{ old('name') }}" required placeholder="Carlos">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Apellido</label>
                                    <input type="text" name="apellido" class="coordinacion-configuracion-input" value="{{ old('apellido') }}" required placeholder="Méndez">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Documento de identidad</label>
                                    <input type="text" name="documento_identidad" class="coordinacion-configuracion-input" value="{{ old('documento_identidad') }}" placeholder="12345678">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Correo electrónico profesional</label>
                                    <input type="email" name="email" class="coordinacion-configuracion-input" value="{{ old('email') }}" required placeholder="carlos.mendez@clinica.com">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Teléfono</label>
                                    <input type="tel" name="telefono" class="coordinacion-configuracion-input" value="{{ old('telefono') }}" placeholder="+34 600 000 000">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Dirección</label>
                                    <input type="text" name="direccion" class="coordinacion-configuracion-input" value="{{ old('direccion') }}" placeholder="Calle Mayor 123, 1º Izq">
                                </div>
                            </div>
                        </div>

                        <div class="coordinacion-card coordinacion-mt-2">
                            <h3 class="coordinacion-section-title">Datos profesionales</h3>
                            <div class="coordinacion-grid coordinacion-grid-cols-2">
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Número de licencia o registro</label>
                                    <input type="text" name="numero_licencia" class="coordinacion-configuracion-input" value="{{ old('numero_licencia') }}" placeholder="LIC-12345">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Especialidad</label>
                                    <select name="especialidad_id" class="coordinacion-configuracion-input">
                                        <option value="">Seleccionar especialidad</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->id }}" {{ old('especialidad_id') == $esp->id ? 'selected' : '' }}>{{ $esp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Departamento / servicio</label>
                                    <select name="departamento_id" class="coordinacion-configuracion-input">
                                        <option value="">Seleccionar departamento</option>
                                        @foreach ($departamentos as $dep)
                                            <option value="{{ $dep->id }}" {{ old('departamento_id') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Fecha de incorporación</label>
                                    <input type="date" name="fecha_incorporacion" class="coordinacion-configuracion-input" value="{{ old('fecha_incorporacion') }}">
                                </div>
                                <div class="coordinacion-form-field coordinacion-mb-2">
                                    <label class="coordinacion-form-label">Estado inicial de la cuenta</label>
                                    <select name="estado_inicial" class="coordinacion-configuracion-input">
                                        <option value="pendiente" selected>Pendiente (requiere activación)</option>
                                        <option value="activo" {{ old('estado_inicial') == 'activo' ? 'selected' : '' }}>Activo (puede iniciar sesión inmediatamente)</option>
                                    </select>
                                    <p class="coordinacion-configuracion-help" style="margin-top: 0.25rem;">Si se selecciona "Pendiente", se generará una invitación para que el médico establezca su propia contraseña.</p>
                                </div>
                            </div>
                        </div>

                        <div class="coordinacion-medicos-actions coordinacion-mt-2">
                            <button type="submit" class="coordinacion-btn-primary">Registrar médico</button>
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