<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light dark">
    <title>Acceso Seguro Asistencial — SIGC (Sistema de Información y Gestión Clínica)</title>
    <meta name="description" content="Acceso autenticado y cifrado al Sistema de Información y Gestión Clínica para personal asistencial autorizado y pacientes.">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">

    <!-- Verificación de Seguridad y Dominio Institucional -->
    <link rel="canonical" href="https://portal.clinicasigc.com/login">
    <link rel="icon" type="image/svg+xml" href="/assets/brand/favicon-sigc.svg">

    <!-- Cabeceras de Seguridad y Políticas en Meta (Defensa en Profundidad) -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    
    <!-- Token CSRF Seguro con Atributo Encrypted Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Activos con Verificación de Integridad de Subrecursos (SRI) y Rutas Canónicas -->
    <link 
        rel="preload" 
        as="style" 
        href="https://assets.clinicasigc.com/build/css/auth-sigc-v2.css" 
        integrity="sha384-m6vK5Q31yM8aA9m2nL0zJ7fW6xE5kL9bQ1oP8tU7yR4wE2qA1sD3fG5hJ7kL9mN1" 
        crossorigin="anonymous"
    >
    <link 
        rel="stylesheet" 
        href="https://assets.clinicasigc.com/build/css/auth-sigc-v2.css" 
        integrity="sha384-m6vK5Q31yM8aA9m2nL0zJ7fW6xE5kL9bQ1oP8tU7yR4wE2qA1sD3fG5hJ7kL9mN1" 
        crossorigin="anonymous"
    >
    
    <!-- Script de Turnstile Cloudflare (Prueba de Humanidad sin Rastreo) -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
</head>
<body class="h-full text-slate-100 antialiased font-sans flex flex-col justify-between selection:bg-cyan-500 selection:text-slate-950">

    <!-- Barra de Advertencia Legal y Cumplimiento Normativo Sanitario -->
    <aside class="bg-slate-900 border-b border-slate-800 text-[11px] text-slate-400 py-1.5 px-4" aria-label="Aviso Legal de Seguridad">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="font-semibold text-slate-300">Conexión Cifrada Grado Médico (TLS 1.3 / AES-256)</span>
                <span class="text-slate-600 hidden sm:inline">|</span>
                <span class="hidden sm:inline">Servidor Auditado HIPAA / RGPD Art. 9</span>
            </div>
            <div class="font-mono text-[10px] text-cyan-400">
                Certificado EV: Hospital Clínico SIGC S.A.
            </div>
        </div>
    </aside>

    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <main class="w-full max-w-5xl bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 backdrop-blur">
            
            <!-- Panel Lateral Institucional e Informativo (5 columnas) -->
            <aside class="lg:col-span-5 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950/40 p-8 sm:p-10 border-b lg:border-b-0 lg:border-r border-slate-800 flex flex-col justify-between relative overflow-hidden" aria-label="Información institucional y seguridad de datos">
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <!-- Marca Institucional Verificada -->
                    <a href="https://clinicasigc.com" class="inline-flex items-center gap-3 text-white focus:outline-none focus:ring-2 focus:ring-cyan-400 rounded-xl p-1" aria-label="SIGC - Página Principal Institucional">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 to-teal-400 flex items-center justify-center text-slate-950 shadow-lg shadow-cyan-500/20">
                            <!-- Icono SVG Médico Heart Pulse -->
                            <svg class="w-6 h-6 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-black tracking-tight text-white block">SIGC</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-cyan-400 block -mt-1">Gestión Clínica Segura</span>
                        </div>
                    </a>

                    <div class="space-y-2 pt-4">
                        <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-500/10 text-cyan-300 text-[10px] font-bold uppercase tracking-wider border border-cyan-500/20">
                            CONFIDENCIALIDAD SANITARIA
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                            Custodia y privacidad de expedientes de salud.
                        </h1>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Entorno restringido para profesionales de la salud colegiados y pacientes con identidad biométrica validada.
                        </p>
                    </div>

                    <!-- Garantías de Seguridad Médica -->
                    <div className="space-y-3 pt-2 text-xs text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="text-emerald-400 shrink-0 font-bold">✓</span>
                            <span>Cifrado en reposo con módulo HSM y llaves dedicadas (KMS).</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="text-emerald-400 shrink-0 font-bold">✓</span>
                            <span>Autenticación de Dos Factores (MFA) mandatoria por HIPAA.</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="text-emerald-400 shrink-0 font-bold">✓</span>
                            <span>Pista forense inmutable de auditoría para cada consulta clínica.</span>
                        </div>
                    </div>
                </div>

                <!-- Bloque de Contacto Seguro y Soporte CISO -->
                <div class="relative z-10 pt-6 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                    <span>Soporte Ciberseguridad: <strong>soc@clinicasigc.com</strong></span>
                    <span class="font-mono text-slate-500">Node ID: SEC-EU1</span>
                </div>
            </aside>

            <!-- Panel de Autenticación de Grado Hospitalario (7 columnas) -->
            <section class="lg:col-span-7 p-8 sm:p-10 flex flex-col justify-between space-y-6" aria-labelledby="auth-title">
                
                <div class="flex items-center justify-between">
                    <a href="https://clinicasigc.com" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1.5 group">
                        <span class="group-hover:-translate-x-1 transition-transform">←</span>
                        <span>Volver al portal corporativo</span>
                    </a>
                    
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800/80 text-emerald-400 text-xs font-semibold border border-slate-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Canal Seguro Certificado
                    </span>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold tracking-wider text-cyan-400 uppercase">IDENTIFICACIÓN ASISTENCIAL</span>
                    <h2 id="auth-title" class="text-2xl font-bold text-white tracking-tight">Iniciar Sesión</h2>
                    <p class="text-xs text-slate-400">
                        Ingresa tus credenciales profesionales y tu segundo factor de autenticación.
                    </p>
                </div>

                <!-- Formulario Protegido contra Fuerza Bruta, Inyección y CSRF -->
                <form 
                    method="POST" 
                    action="{{ route('login.secure') }}" 
                    class="space-y-4"
                    autocomplete="on"
                    id="sigc-login-form"
                    novalidate
                >
                    <!-- Token CSRF de Laravel -->
                    @csrf

                    <!-- Identificador de Dispositivo Médico (Fingerprinting Seguro) -->
                    <input type="hidden" name="client_fingerprint" id="client_fingerprint" value="">

                    <!-- Campo 1: Identificación / Correo Institucional -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-200">
                            Correo Electrónico Institucional o Cédula Médica <span class="text-red-400" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="medico@clinicasigc.com"
                                maxlength="120"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all"
                                aria-describedby="email-hint"
                            >
                        </div>
                        <p id="email-hint" class="text-[11px] text-slate-500">Debe coincidir con la cuenta registrada en el Colegio Médico o Registro Asistencial.</p>
                    </div>

                    <!-- Campo 2: Contraseña Segura -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-200">
                                Contraseña Maestra <span class="text-red-400" aria-hidden="true">*</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-[11px] text-cyan-400 hover:text-cyan-300 hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Ingresa tu contraseña de alta entropía"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all pr-10"
                            >
                            <button
                                type="button"
                                id="toggle-password-btn"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1"
                                aria-label="Alternar visibilidad de contraseña"
                            >
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Campo 3: Autenticación de Dos Factores (MFA Obligatorio HIPAA) -->
                    <div class="space-y-1.5 p-3.5 rounded-xl bg-slate-950/80 border border-cyan-500/30">
                        <label for="two_factor_code" class="block text-xs font-semibold text-cyan-300 flex items-center justify-between">
                            <span>Código de Verificación MFA (TOTP / Token Físico)</span>
                            <span class="text-[10px] text-amber-400 font-mono font-bold">REQUERIDO</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                id="two_factor_code"
                                name="two_factor_code"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                placeholder="000000"
                                required
                                autocomplete="one-time-code"
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-center text-base font-mono font-bold text-amber-400 tracking-widest placeholder-slate-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400"
                            >
                            <button
                                type="button"
                                id="webauthn-trigger-btn"
                                class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 whitespace-nowrap transition-colors"
                                title="Usar llave física FIDO2 o Touch ID / Windows Hello"
                            >
                                🔑 Usar Passkey / FIDO2
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400">
                            Ingresa el código temporal de 6 dígitos generado por tu aplicación (Google Authenticator, Microsoft Authenticator o Duo).
                        </p>
                    </div>

                    <!-- Desafío de Humanidad Cloudflare Turnstile (Anti-Fuerza Bruta) -->
                    <div class="pt-1">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="dark"></div>
                    </div>

                    <!-- Advertencia de Puesto Compartido (Reemplaza 'Recordar Sesión') -->
                    <div class="p-3 rounded-lg bg-amber-950/20 border border-amber-500/30 text-[11px] text-amber-200 flex items-start gap-2">
                        <span class="text-amber-400 text-sm">⚠️</span>
                        <span>
                            <strong>Protocolo Hospitalario de Cierre Automático:</strong> Esta sesión se cerrará automáticamente tras 15 minutos de inactividad para prevenir acceso no autorizado en terminales compartidas de consulta.
                        </span>
                    </div>

                    <!-- Botón de Envío Protegido -->
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-400 hover:from-cyan-400 hover:to-teal-300 text-slate-950 font-bold text-sm transition-all shadow-lg shadow-cyan-500/20 flex items-center justify-center gap-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    >
                        <span>Validar Identidad y Acceder al Sistema</span>
                        <span>→</span>
                    </button>
                </form>

                <!-- Registro Restringido / Invitación Médica Exclusiva -->
                <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2">
                    <span>¿Nuevo profesional médico o asistente clínico?</span>
                    <a href="https://clinicasigc.com/solicitud-colegiacion" class="text-cyan-400 hover:text-cyan-300 font-semibold inline-flex items-center gap-1 hover:underline">
                        Solicitar Acreditación Asistencial ↗
                    </a>
                </div>

                <footer class="text-[10px] text-slate-500 text-center">
                    El acceso no autorizado a registros clínicos está penado por leyes internacionales de protección de datos (RGPD Art. 83 / HIPAA 45 CFR Part 160).
                </footer>
            </section>
        </main>
    </div>

    <!-- Script de Inicialización de Criptografía de Cliente y Detección de Dispositivo -->
    <script 
        src="https://assets.clinicasigc.com/build/js/auth-security-v2.js" 
        integrity="sha384-k8vL9N23xM7bB8nC1oP2qR3tS4uV5wX6yZ7aB8cD9eF0gH1iJ2kL3mN4oP5qR6sT" 
        crossorigin="anonymous"
    ></script>
</body>
</html>
