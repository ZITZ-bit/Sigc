<?php

use Illuminate\Support\Str;

/**
 * config/session.php — Configuración de Sesiones Médicas de Alta Seguridad
 * Estándar HIPAA § 164.312(a)(2)(iii) - Automatic Logoff
 */

return [

    // Almacenamiento en Base de Datos Cifrada o Redis en VPC Aislada
    'driver' => env('SESSION_DRIVER', 'database'),

    // Tiempo máximo de inactividad: 15 minutos en estaciones clínicas hospitalarias
    'lifetime' => (int) env('SESSION_LIFETIME', 15),

    // Cerrar sesión al cerrar el navegador (Previene persistencia en equipos compartidos)
    'expire_on_close' => true,

    // Cifrar el payload de la sesión con AES-256-GCM
    'encrypt' => true,

    // Configuración estricta de cookies HTTP
    'path' => '/',

    // Dominio institucional propio (Desvinculado de *.onrender.com)
    'domain' => env('SESSION_DOMAIN', '.clinicasigc.com'),

    // Obligatorio HTTPS
    'secure' => true,

    // Prohibido acceso por JavaScript (Mitigación total contra robo XSS)
    'http_only' => true,

    // SameSite=Strict previene peticiones cruzadas (Mitigación total CSRF)
    'same_site' => 'strict',

    'partitioned' => false,

];
