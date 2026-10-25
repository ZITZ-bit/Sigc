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

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-brand">
                    <span class="auth-brand-icon" aria-hidden="true">&#x271a;</span>
                    <span>{{ config('app.name') }}</span>
                </div>
                <h1>Activar cuenta</h1>
                <p>Establezca su contraseña para comenzar a usar el sistema.</p>
            </div>

            @if ($error ?? null)
                <div class="coordinacion-card" style="background-color: var(--auth-color-error-soft); border: 1px solid var(--auth-color-error); margin-bottom: 1.5rem;">
                    <p style="color: var(--auth-color-error); margin: 0; font-size: 0.9375rem;">{{ $error }}</p>
                </div>
            @endif

            @if ($token ?? null)
                <form method="POST" action="{{ route('medico.activar', $token) }}">
                    @csrf

                    <div class="auth-field">
                        <label class="auth-field-label">Correo electrónico</label>
                        <input type="email" value="{{ $email ?? old('email') }}" readonly class="auth-input" style="background-color: var(--auth-color-bg-muted);">
                    </div>

                    <div class="auth-field">
                        <label class="auth-field-label">Nueva contraseña</label>
                        <input type="password" name="password" class="auth-input{{ $errors->first('password') ? ' auth-input--error' : '' }}" required placeholder="••••••••" {{ $errors->first('password') ? 'aria-invalid="true"' : '' }}>
                        @if ($errors->first('password'))
                            <p class="auth-field-error">{{ $errors->first('password') }}</p>
                        @endif
                        <p class="auth-field-help">Mínimo 8 caracteres.</p>
                    </div>

                    <div class="auth-field">
                        <label class="auth-field-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="auth-input{{ $errors->first('password_confirmation') ? ' auth-input--error' : '' }}" required placeholder="••••••••" {{ $errors->first('password_confirmation') ? 'aria-invalid="true"' : '' }}>
                        @if ($errors->first('password_confirmation'))
                            <p class="auth-field-error">{{ $errors->first('password_confirmation') }}</p>
                        @endif
                    </div>

                    <button type="submit" class="portal-btn portal-btn-primary portal-btn-lg portal-btn-full">Activar cuenta</button>
                </form>

                <div class="auth-switch" style="margin-top: 1.5rem;">
                    <p>¿Ya tiene una cuenta?</p>
                    <a href="{{ route('login') }}" class="auth-button-link" style="font-weight: 600; color: var(--auth-color-forest-dark); text-decoration: underline; text-decoration-color: transparent; text-underline-offset: 2px;">
                        Iniciar sesión
                    </a>
                </div>
            @endif
        </div>
    </div>

</body>
</html>