<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Iniciar sesión | SIGC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/components.css', 'resources/css/login.css'])
</head>
<body class="auth-page">
    <main class="auth-layout">

        <aside class="auth-aside">

            <x-brand class="auth-aside-brand" href="{{ url('/') }}" label="SIGC, volver al inicio" />
            <div class="auth-aside-copy">
                <p class="auth-eyebrow"><span></span> TU CLÍNICA, EN SINTONÍA</p>
                <h1>Qué bueno<br>tenerte <em>de vuelta.</em></h1>
                <p>Tu equipo y tus pacientes están a un solo paso.</p>
            </div>
            <div class="auth-agenda" aria-hidden="true">
                <div class="auth-agenda-top"><span>HOY EN TU CLÍNICA</span><span>15 OCT</span></div>
                <div class="auth-agenda-row"><time>09:00</time><span><i></i><strong>Consulta inicial</strong><small>Mariana López</small></span><b>EN CURSO</b></div>
                <div class="auth-agenda-row"><time>10:30</time><span><i></i><strong>Seguimiento</strong><small>Diego Ramírez</small></span><b>PRÓXIMA</b></div>
                <div class="auth-agenda-footer"><span>Agenda organizada</span><span>✓</span></div>
            </div>
            
            <p class="auth-aside-foot">SIGC <span>·</span> GESTIÓN CLÍNICA CON MÁS SENTIDO HUMANO</p>
        
        </aside>

        <section class="auth-form-panel" aria-labelledby="login-title">
            <div class="auth-form-content">
                
                <a class="auth-back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Volver al inicio</a>
                <p class="auth-eyebrow auth-eyebrow-light">ACCESO A TU ESPACIO</p>
                <h2 id="login-title">Inicia sesión</h2>
                <p class="auth-intro">Ingresa tus datos para continuar con tu día.</p>

                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <label class="auth-field">
                        <span>Correo electrónico</span>
                        <input type="email" name="email" autocomplete="email" placeholder="nombre@clinica.com" value="{{ old('email') }}" required>
                    </label>
                    <label class="auth-field">
                        <span>Contraseña</span>
                        <input type="password" name="password" autocomplete="current-password" placeholder="Escribe tu contraseña" required>
                    </label>

                    <label class="auth-checkbox"><input type="checkbox" name="remember"><span>Recordar mi sesión</span></label>
                    <button class="auth-submit" type="submit">Entrar a SIGC <span aria-hidden="true">→</span></button>
                </form>
                @if($errors->any())
                    <div style="margin-top:16px; color:#b91c1c;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <p class="auth-switch">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
                <p class="auth-privacy">Tu información clínica se mantiene privada y protegida.</p>
            
            </div>
        </section>

    </main>
</body>
</html>