<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Aceptar invitación | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/components.css', 'resources/css/register.css'])
</head>
<body class="auth-page">
    <main class="auth-layout auth-layout-register">
        <aside class="auth-aside auth-aside-register">
            <x-brand class="auth-aside-brand" href="{{ url('/') }}" label="SIGC, volver al inicio" />
            <div class="auth-aside-copy">
                <p class="auth-eyebrow"><span></span> INVITACIÓN DE LA CLÍNICA</p>
                <h1>Activa<br>tu <em>cuenta.</em></h1>
                <p>Completa tu perfil para acceder a tu espacio según el rol asignado.</p>
            </div>
            <p class="auth-aside-foot">SIGC <span>·</span> GESTIÓN CLÍNICA CON MÁS SENTIDO HUMANO</p>
        </aside>
        <section class="auth-form-panel" aria-labelledby="invite-title">
            <div class="auth-form-content">
                <a class="auth-back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Volver al inicio</a>
                <p class="auth-eyebrow auth-eyebrow-light">BIENVENIDO/A</p>
                <h2 id="invite-title">Aceptar invitación</h2>
                <p class="auth-intro">Correo: {{ $invite->email }} · Rol: {{ $invite->role->display_name ?? $invite->role->nombre }}</p>

                <form class="auth-form auth-register-form" method="POST" action="{{ route('invite.accept', $invite->token) }}">
                    @csrf
                    <div class="auth-field-row">
                        <label class="auth-field">
                            <span>Nombre</span>
                            <input type="text" name="name" required>
                        </label>
                        <label class="auth-field">
                            <span>Apellido</span>
                            <input type="text" name="apellido" required>
                        </label>
                    </div>
                    <label class="auth-field">
                        <span>Usuario (opcional)</span>
                        <input type="text" name="username">
                    </label>
                    @if($invite->role->nombre === 'medico')
                        <div class="auth-field-row">
                            <label class="auth-field">
                                <span>Cédula profesional (opcional)</span>
                                <input type="text" name="cedula_profesional">
                            </label>
                            <label class="auth-field">
                                <span>Años de experiencia (opcional)</span>
                                <input type="number" name="anios_experiencia" min="0">
                            </label>
                        </div>
                        <label class="auth-field">
                            <span>Consultorio (opcional)</span>
                            <input type="text" name="consultorio">
                        </label>
                    @endif
                    <label class="auth-field">
                        <span>Contraseña</span>
                        <input type="password" name="password" required minlength="8">
                    </label>
                    <label class="auth-field">
                        <span>Confirmar contraseña</span>
                        <input type="password" name="password_confirmation" required minlength="8">
                    </label>
                    <button class="auth-submit" type="submit">Activar cuenta <span aria-hidden="true">→</span></button>
                </form>
                @if($errors->any())
                    <div style="margin-top:16px; color:#b91c1c;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
