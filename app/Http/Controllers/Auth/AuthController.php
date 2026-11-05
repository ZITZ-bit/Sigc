<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

/**
 * Controlador de Autenticación de Grado Hospitalario SIGC
 * Implementa Controles Técnicos HIPAA § 164.312 y RGPD Art. 32
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Procesamiento seguro de inicio de sesión con MFA obligatorio y pistas de auditoría
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = (string) $request->input('email');
        $throttleKey = strtolower($email) . '|' . $request->ip();

        // 1. CONTROL ANTI-FUERZA BRUTA: Máximo 5 intentos cada 5 minutos
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Registro forense inmutable de seguridad
            Log::channel('security_audit')->alert('Bloqueo por Fuerza Bruta en SIGC Login', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'target_email' => $email,
                'retry_after_seconds' => $seconds,
                'event_type' => 'AUTHENTICATION_RATE_LIMITED'
            ]);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Por seguridad de los datos médicos, su acceso está bloqueado temporalmente por {$seconds} segundos."
            ]);
        }

        // 2. VERIFICACIÓN DE CREDENCIALES PRIMARIAS
        if (!Auth::validate($request->only('email', 'password'))) {
            RateLimiter::hit($throttleKey, 300); // 5 minutos de bloqueo progresivo

            Log::channel('security_audit')->warning('Fallo de autenticación de credenciales primarias en SIGC', [
                'ip' => $request->ip(),
                'target_email' => $email,
                'event_type' => 'AUTH_FAILED_PASSWORD'
            ]);

            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        $user = Auth::getProvider()->retrieveByCredentials($request->only('email'));

        // 3. ENFORCEMENT OBLIGATORIO DE MFA (HIPAA Security Rule)
        $google2fa = new Google2FA();
        $mfaCode = $request->input('two_factor_code');

        if (empty($user->two_factor_secret)) {
            // Usuario médico sin MFA configurado: redirigir a enrolamiento forzado
            session()->put('mfa_enrollment_user_id', $user->id);
            return redirect()->route('two-factor.setup');
        }

        $isValidMfa = $google2fa->verifyKey($user->two_factor_secret, $mfaCode, 2); // Ventana de tolerancia de 60s

        if (!$isValidMfa) {
            RateLimiter::hit($throttleKey, 300);

            Log::channel('security_audit')->error('Intento de acceso con contraseña correcta pero código MFA INVÁLIDO', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'event_type' => 'MFA_VERIFICATION_FAILED'
            ]);

            throw ValidationException::withMessages([
                'two_factor_code' => 'El código de autenticación de doble factor (MFA) es incorrecto o ha expirado.'
            ]);
        }

        // 4. LIMPIEZA DEL LIMITADOR Y LOGIN EFECTIVO
        RateLimiter::clear($throttleKey);
        Auth::login($user, false); // NUNCA recordar sesión indefinidamente en entorno clínico

        // 5. REGENERACIÓN ESTRICTA DE ID DE SESIÓN (Prevención Session Fixation)
        $request->session()->regenerate();
        $request->session()->put('auth_time', now()->timestamp);
        $request->session()->put('ip_address', $request->ip());

        // 6. REGISTRO EN PISTA DE AUDITORÍA INMUTABLE (HIPAA § 164.312(b))
        Log::channel('security_audit')->info('Acceso exitoso al sistema clínico SIGC', [
            'user_id' => $user->id,
            'role' => $user->role,
            'medical_license' => $user->medical_license ?? 'N/A',
            'ip' => $request->ip(),
            'session_id' => session()->getId(),
            'timestamp' => now()->toIso8601String(),
            'event_type' => 'CLINICAL_PORTAL_LOGIN_SUCCESS'
        ]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Cierre de sesión seguro con invalidación total de tokens y cookies
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Log::channel('security_audit')->info('Cierre de sesión asistencial', [
            'user_id' => $userId,
            'ip' => $request->ip(),
            'event_type' => 'LOGOUT'
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Sesión clínica cerrada de manera segura.');
    }
}
