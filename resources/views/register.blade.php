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

    <div class="auth-page auth-page--register">
        <main class="auth-shell auth-shell--register">
            <aside class="auth-aside" aria-label="Información de la clínica">
                <a href="{{ route('portal') }}" class="auth-brand" aria-label="{{ config('app.name') }} - Inicio">
                    <span class="auth-brand-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
                    <span>{{ config('app.name') }}</span>
                </a>
                <div class="auth-aside-content">
                    <p class="auth-eyebrow">EMPIEZA POR TI</p>
                    <h2>Una mejor atención comienza aquí.</h2>
                    <p>Crea tu cuenta para mantener tu información y tus citas en un mismo lugar.</p>
                </div>
                <div class="auth-aside-note">
                    <span class="auth-aside-note-icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <span>Tus datos personales se mantienen protegidos.</span>
                </div>
                <span class="auth-aside-index" aria-hidden="true">02 / 02</span>
            </aside>

            <section class="auth-panel" aria-labelledby="auth-title">
                <div class="auth-panel-top">
                    <a href="{{ route('portal') }}" class="auth-back-link"><i data-lucide="arrow-left" aria-hidden="true"></i><span>Volver al portal</span></a>
                    <span class="auth-secure-label"><i data-lucide="lock-keyhole" aria-hidden="true"></i> Registro seguro</span>
                </div>

                <div class="auth-header">
                    <p class="auth-eyebrow">TU ESPACIO PERSONAL</p>
                    <h1 id="auth-title">Crear cuenta</h1>
                    <p>Completa tus datos para empezar.</p>
                </div>

                @include('portal::auth.errors', ['messages' => $errors->all()])

                <form method="POST" action="{{ route('register.post') }}" class="auth-form auth-form--register">
                    @csrf

                    <div class="auth-fields-grid">
                        @include('portal::auth.field', [
                            'name' => 'name',
                            'type' => 'text',
                            'label' => 'Nombre completo',
                            'required' => true,
                            'error' => $errors->first('name'),
                            'help' => null,
                            'autocomplete' => 'name',
                            'placeholder' => 'Tu nombre y apellidos',
                        ])

                        @include('portal::auth.field', [
                            'name' => 'email',
                            'type' => 'email',
                            'label' => 'Correo electrónico',
                            'required' => true,
                            'error' => $errors->first('email'),
                            'help' => null,
                            'autocomplete' => 'email',
                            'placeholder' => 'nombre@correo.com',
                        ])

                        @include('portal::auth.field', [
                            'name' => 'password',
                            'type' => 'password',
                            'label' => 'Contraseña',
                            'required' => true,
                            'error' => $errors->first('password'),
                            'help' => 'Mínimo 8 caracteres.',
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Crea una contraseña',
                        ])

                        @include('portal::auth.field', [
                            'name' => 'password_confirmation',
                            'type' => 'password',
                            'label' => 'Confirmar contraseña',
                            'required' => true,
                            'error' => $errors->first('password_confirmation'),
                            'help' => null,
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Repite la contraseña',
                        ])
                    </div>

                    @include('portal::auth.button', [
                        'label' => 'Crear cuenta',
                        'icon' => 'arrow-right',
                    ])
                </form>

                <div class="auth-switch">
                    <span>¿Ya tienes una cuenta?</span>
                    <a href="{{ route('login') }}" class="auth-button-link">Iniciar sesión <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                </div>

                <p class="auth-legal-note">Al registrarte, tu cuenta quedará asociada a {{ config('app.name') }}.</p>
            </section>
        </main>
    </div>

</body>
</html>