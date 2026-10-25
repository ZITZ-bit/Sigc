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

    <div class="auth-page auth-page--login">
        <main class="auth-shell">
            <aside class="auth-aside" aria-label="Información de la clínica">
                <a href="{{ route('portal') }}" class="auth-brand" aria-label="{{ config('app.name') }} - Inicio">
                    <span class="auth-brand-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
                    <span>{{ config('app.name') }}</span>
                </a>
                <div class="auth-aside-content">
                    <p class="auth-eyebrow">CUIDAMOS DE TI</p>
                    <h2>Tu salud, en buenas manos.</h2>
                    <p>Un espacio seguro para acompañar cada paso de tu atención médica.</p>
                </div>
                <div class="auth-aside-note">
                    <span class="auth-aside-note-icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <span>Atención cercana, información protegida.</span>
                </div>
                <span class="auth-aside-index" aria-hidden="true">01 / 02</span>
            </aside>

            <section class="auth-panel" aria-labelledby="auth-title">
                <div class="auth-panel-top">
                    <a href="{{ route('portal') }}" class="auth-back-link"><i data-lucide="arrow-left" aria-hidden="true"></i><span>Volver al portal</span></a>
                    <span class="auth-secure-label"><i data-lucide="lock-keyhole" aria-hidden="true"></i> Acceso seguro</span>
                </div>

                <div class="auth-header">
                    <p class="auth-eyebrow">BIENVENIDO DE NUEVO</p>
                    <h1 id="auth-title">Iniciar sesión</h1>
                    <p>Accede para gestionar tu información y atención médica.</p>
                </div>

                @include('portal::auth.errors', ['messages' => $errors->all()])

                <form method="POST" action="{{ route('login.post') }}" class="auth-form">
                    @csrf

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
                        'help' => null,
                        'autocomplete' => 'current-password',
                        'placeholder' => 'Ingresa tu contraseña',
                    ])

                    <div class="auth-form-options">
                        <label class="auth-checkbox" for="remember">
                            <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Recordar sesión</span>
                        </label>
                    </div>

                    @include('portal::auth.button', [
                        'label' => 'Iniciar sesión',
                        'icon' => 'arrow-right',
                    ])
                </form>

                <div class="auth-switch">
                    <span>¿Aún no tienes una cuenta?</span>
                    <a href="{{ route('register') }}" class="auth-button-link">Crear cuenta <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                </div>

                <p class="auth-legal-note">Al continuar, accedes a un entorno privado de {{ config('app.name') }}.</p>
            </section>
        </main>
    </div>

</body>
</html>