<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Crear cuenta — {{ config('app.name') }}</title>
    <meta name="description" content="Regístrate en {{ config('app.name') }} para acceder a nuestros servicios médicos.">
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
                <h1>Crear cuenta</h1>
                <p>Únete a {{ config('app.name') }} y accede a nuestros servicios de atención médica.</p>
            </div>

            @include('portal::auth.errors', ['messages' => $errors->all()])

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                @include('portal::auth.field', [
                    'name' => 'name',
                    'type' => 'text',
                    'label' => 'Nombre',
                    'required' => true,
                    'error' => $errors->first('name'),
                    'help' => 'Tu nombre completo.',
                    'autocomplete' => 'name',
                    'placeholder' => 'Juan',
                ])

                @include('portal::auth.field', [
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Correo electrónico',
                    'required' => true,
                    'error' => $errors->first('email'),
                    'help' => 'Se usará para iniciar sesión y recibir notificaciones.',
                    'autocomplete' => 'email',
                    'placeholder' => 'juan@ejemplo.com',
                ])

                @include('portal::auth.field', [
                    'name' => 'password',
                    'type' => 'password',
                    'label' => 'Contraseña',
                    'required' => true,
                    'error' => $errors->first('password'),
                    'help' => 'Mínimo 8 caracteres, al menos una letra y un número.',
                    'autocomplete' => 'new-password',
                    'placeholder' => '••••••••',
                ])

                @include('portal::auth.field', [
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'label' => 'Confirmar contraseña',
                    'required' => true,
                    'error' => $errors->first('password_confirmation'),
                    'help' => 'Repite la contraseña para confirmar.',
                    'autocomplete' => 'new-password',
                    'placeholder' => '••••••••',
                ])

                @include('portal::auth.button', [
                    'label' => 'Crear cuenta',
                ])
            </form>

            <div class="auth-switch">
                <p>¿Ya tienes una cuenta?</p>
                <a href="{{ route('login') }}" class="auth-button-link" style="font-weight: 600; color: var(--auth-color-forest-dark); text-decoration: underline; text-decoration-color: transparent; text-underline-offset: 2px;">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </div>

</body>
</html>