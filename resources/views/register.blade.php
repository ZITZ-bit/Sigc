<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f1">
    <title>Crear cuenta | SIGC</title>
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
                <p class="auth-eyebrow"><span></span> UN MEJOR DÍA EMPIEZA AQUÍ</p>
                <h1>Haz espacio<br>para <em>lo que importa.</em></h1>
                <p>Organiza tu clínica y acompaña cada historia desde un solo lugar.</p>
            </div>
            <div class="register-note" aria-hidden="true">
                <span class="register-note-mark">✳</span>
                <div><strong>Tu clínica, a tu manera.</strong><span>Empieza con lo esencial. Crece a tu ritmo.</span></div>
                <span class="register-note-arrow">↗</span>
            </div>
            <p class="auth-aside-foot">SIGC <span>·</span> GESTIÓN CLÍNICA CON MÁS SENTIDO HUMANO</p>
        </aside>

        <section class="auth-form-panel" aria-labelledby="register-title">
            <div class="auth-form-content">
                <a class="auth-back-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Volver al inicio</a>
                <p class="auth-eyebrow auth-eyebrow-light">COMIENZA POR AQUÍ</p>
                <h2 id="register-title">Crea tu cuenta</h2>
                <p class="auth-intro">Cuéntanos un poco de ti y de tu clínica.</p>

                <form class="auth-form auth-register-form">
                    <div class="auth-field-row">
                        <label class="auth-field">
                            <span>Nombre</span>
                            <input type="text" name="name" autocomplete="given-name" placeholder="Tu nombre" required>
                        </label>
                        <label class="auth-field">
                            <span>Apellido</span>
                            <input type="text" name="last_name" autocomplete="family-name" placeholder="Tu apellido" required>
                        </label>
                    </div>
                    <label class="auth-field">
                        <span>Correo electrónico</span>
                        <input type="email" name="email" autocomplete="email" placeholder="nombre@clinica.com" required>
                    </label>
                    <label class="auth-field">
                        <span>Nombre de tu clínica <small>OPCIONAL</small></span>
                        <input type="text" name="clinic" autocomplete="organization" placeholder="¿Cómo se llama tu clínica?">
                    </label>
                    <label class="auth-field">
                        <span>Contraseña</span>
                        <input type="password" name="password" autocomplete="new-password" placeholder="Crea una contraseña" required>
                    </label>
                    <button class="auth-submit" type="button">Crear mi cuenta <span aria-hidden="true">→</span></button>
                </form>

                <p class="auth-switch">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
                <p class="auth-privacy">Tu información de acceso se mantiene privada y protegida.</p>
            </div>
        </section>
    </main>
</body>
</html>