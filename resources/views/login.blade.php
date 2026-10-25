<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Iniciar sesión — {{ config('app.name') }}</title>
    <meta name="description" content="Accede a tu cuenta de {{ config('app.name') }}.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/auth.css', 'resources/js/app.js'])
</head>
<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-brand">
                    <span class="auth-brand-icon" aria-hidden="true">&#x271a;</span>
                    <span>{{ config('app.name') }}</span>
                </div>
                <h1>Iniciar sesión</h1>
                <p>Accede a tu cuenta para gestionar tu información médica.</p>
            </div>

            <nav class="auth-back-nav" aria-label="Navegación">
                <a href="{{ route('portal') }}" class="auth-button-link" style="font-size: 0.9375rem; color: var(--auth-color-forest-dark);">
                    &#x2190; Volver al portal
                </a>
            </nav>

            @include('portal::auth.errors', ['messages' => $errors->all()])

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                @include('portal::auth.field', [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Correo electrónico',
                    'required' => true,
                    'error' => $errors->first('email'),
                    'help' => 'Ingresa el correo electrónico registrado en la clínica.',
                    'autocomplete' => 'email',
                ])

                @include('portal::auth.field', [
                    'name' => 'password',
                    'type' => 'password',
                    'label' => 'Contraseña',
                    'required' => true,
                    'error' => $errors->first('password'),
                    'help' => 'Mínimo 8 caracteres.',
                    'autocomplete' => 'current-password',
                ])

                <div class="auth-field" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="auth-field-label" style="margin-bottom: 0; font-weight: 500; font-size: 0.9375rem;">
                        Recordar sesión
                    </label>
                </div>

                @include('portal::auth.button', [
                    'label' => 'Iniciar sesión',
                ])
            </form>

            <div class="auth-switch">
                <p>¿No tienes una cuenta?</p>
                <a href="{{ route('register') }}" class="auth-button-link" style="font-weight: 600; color: var(--auth-color-forest-dark); text-decoration: underline; text-decoration-color: transparent; text-underline-offset: 2px;">
                    Crear cuenta
                </a>
            </div>
        </div>
    </div>

</body>
</html>