<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Activar cuenta — {{ config('app.name') }}</title>
    <meta name="description" content="Active su cuenta de médico.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/auth.css', 'resources/js/app.js'])
</head>
<body>

    <div class="auth-page auth-page--activation">
        <main class="auth-shell auth-shell--activation">
            <aside class="auth-aside" aria-label="Bienvenida al equipo médico">
                <a href="{{ route('portal') }}" class="auth-brand" aria-label="{{ config('app.name') }} - Inicio">
                    <span class="auth-brand-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
                    <span>{{ config('app.name') }}</span>
                </a>
                <div class="auth-aside-content">
                    <p class="auth-eyebrow">BIENVENIDO AL EQUIPO</p>
                    <h2>Tu experiencia también cuida.</h2>
                    <p>Activa tu cuenta para acceder a tu agenda y comenzar a brindar atención a tus pacientes.</p>
                </div>
                <div class="auth-aside-note">
                    <span class="auth-aside-note-icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
                    <span>Tu cuenta y la información clínica están protegidas.</span>
                </div>
                <span class="auth-aside-index" aria-hidden="true">CUENTA MÉDICA</span>
            </aside>

            <section class="auth-panel" aria-labelledby="auth-title">
                <div class="auth-panel-top">
                    <a href="{{ route('portal') }}" class="auth-back-link"><i data-lucide="arrow-left" aria-hidden="true"></i><span>Volver al portal</span></a>
                    <span class="auth-secure-label"><i data-lucide="lock-keyhole" aria-hidden="true"></i> Activación segura</span>
                </div>

                <div class="auth-header">
                    <p class="auth-eyebrow">UN ÚLTIMO PASO</p>
                    <h1 id="auth-title">Activa tu cuenta</h1>
                    <p>Confirma tus datos y crea una contraseña segura para empezar.</p>
                </div>

                @php
                    $activationErrors = $errors->all();
                    if (! empty($error ?? null)) {
                        array_unshift($activationErrors, $error);
                    }
                @endphp
                @include('portal::auth.errors', ['messages' => $activationErrors])

                @if ($token ?? null)
                    <div class="auth-activation-summary">
                        <span class="auth-activation-summary-icon" aria-hidden="true"><i data-lucide="stethoscope"></i></span>
                        <div>
                            <strong>{{ $medicoNombre ?? 'Médico' }}</strong>
                            <span>{{ $especialidad ?? 'Profesional médico' }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('medico.activar', $token) }}" class="auth-form">
                        @csrf

                        <div class="auth-field">
                            <label for="activation-email" class="auth-field-label">Correo electrónico</label>
                            <input id="activation-email" type="email" value="{{ $email ?? old('email') }}" readonly aria-readonly="true" class="auth-input auth-input--readonly">
                        </div>

                        @include('portal::auth.field', [
                            'name' => 'password',
                            'type' => 'password',
                            'label' => 'Nueva contraseña',
                            'required' => true,
                            'error' => $errors->first('password'),
                            'help' => 'Usa al menos 8 caracteres.',
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Crea una contraseña segura',
                        ])

                        @include('portal::auth.field', [
                            'name' => 'password_confirmation',
                            'type' => 'password',
                            'label' => 'Confirma tu contraseña',
                            'required' => true,
                            'error' => $errors->first('password_confirmation'),
                            'help' => null,
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Escribe de nuevo tu contraseña',
                        ])

                        @include('portal::auth.button', [
                            'label' => 'Activar mi cuenta',
                            'icon' => 'arrow-right',
                            'class' => 'auth-button--activation',
                        ])
                    </form>

                    <div class="auth-switch">
                        <span>¿Ya activaste tu cuenta?</span>
                        <a href="{{ route('login') }}" class="auth-button-link">Iniciar sesión <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                @else
                    <div class="auth-switch auth-activation-help">
                        <span>Si necesitas una nueva invitación, comunícate con coordinación.</span>
                        <a href="{{ route('login') }}" class="auth-button-link">Ir a iniciar sesión <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                @endif

                <p class="auth-legal-note">Acceso privado para el equipo médico de {{ config('app.name') }}.</p>
            </section>
        </main>
    </div>

</body>
</html>
