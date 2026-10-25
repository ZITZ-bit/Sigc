-- Base de datos SIGC - Sistema de Gestión de Clínica
-- Este archivo contiene el esquema completo y datos iniciales de prueba

CREATE DATABASE IF NOT EXISTS sigc;
USE sigc;

-- -----------------------------------------------------
-- Tabla roles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla users
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `role_id` bigint UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `users_role_id_foreign` (`role_id`),
    CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla especialidades
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `especialidades` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla departamentos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `departamentos` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla medicos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `medicos` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint UNSIGNED NOT NULL,
    `especialidad_id` bigint UNSIGNED DEFAULT NULL,
    `departamento_id` bigint UNSIGNED DEFAULT NULL,
    `numero_licencia` varchar(100) DEFAULT NULL,
    `telefono` varchar(50) DEFAULT NULL,
    `direccion` varchar(255) DEFAULT NULL,
    `fecha_incorporacion` date DEFAULT NULL,
    `estado` enum('pendiente','activo','inactivo') NOT NULL DEFAULT 'pendiente',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `medicos_user_id_foreign` (`user_id`),
    KEY `medicos_especialidad_id_foreign` (`especialidad_id`),
    KEY `medicos_departamento_id_foreign` (`departamento_id`),
    CONSTRAINT `medicos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `medicos_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE SET NULL,
    CONSTRAINT `medicos_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla invitaciones
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `invitaciones` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `medico_id` bigint UNSIGNED NOT NULL,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL UNIQUE,
    `token_hash` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL,
    `used_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `invitaciones_medico_id_foreign` (`medico_id`),
    CONSTRAINT `invitaciones_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `medicos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla cache (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla cache_locks (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` varchar(255) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla failed_jobs (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) NOT NULL UNIQUE,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla job_batches (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_batches` (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `total_jobs` int NOT NULL,
    `pending_jobs` int NOT NULL,
    `failed_jobs` int NOT NULL,
    `failed_job_ids` longtext NOT NULL,
    `options` mediumtext DEFAULT NULL,
    `cancelled_at` int DEFAULT NULL,
    `created_at` int NOT NULL,
    `finished_at` int DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla jobs (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint UNSIGNED NOT NULL,
    `reserved_at` int UNSIGNED DEFAULT NULL,
    `available_at` int UNSIGNED NOT NULL,
    `created_at` int UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabla migrations (Laravel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Insertar roles
-- -----------------------------------------------------
INSERT INTO `roles` (`name`, `slug`, `created_at`, `updated_at`) VALUES
('Paciente', 'paciente', NOW(), NOW()),
('Médico', 'medico', NOW(), NOW()),
('Coordinador', 'coordinador', NOW(), NOW());

-- -----------------------------------------------------
-- Insertar especialidades de ejemplo
-- -----------------------------------------------------
INSERT INTO `especialidades` (`name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
('Medicina General', 'medicina-general', 'Atención primaria y control general de salud', NOW(), NOW()),
('Cardiología', 'cardiologia', 'Especialista en salud cardiovascular', NOW(), NOW()),
('Pediatría', 'pediatria', 'Cuidado integral de la salud infantil', NOW(), NOW()),
('Medicina Interna', 'medicina-interna', 'Diagnóstico y tratamiento de enfermedades en adultos', NOW(), NOW());

-- -----------------------------------------------------
-- Insertar departamentos de ejemplo
-- -----------------------------------------------------
INSERT INTO `departamentos` (`name`, `slug`, `created_at`, `updated_at`) VALUES
('Consultas Externas', 'consultas-externas', NOW(), NOW()),
('Urgencias', 'urgencias', NOW(), NOW()),
('Hospitalización', 'hospitalizacion', NOW(), NOW());

-- -----------------------------------------------------
-- Insertar usuarios de prueba (contraseñas hasheadas con bcrypt)
-- -----------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
('Paciente Demo', 'paciente@sigc.test', '$2y$12$45kSmgpYTvdeiAJViw1yAudqK/C7YKC9irFtlIb2ZCF588Tj9MolO', 1, NOW(), NOW()),
('Medico Demo', 'medico@sigc.test', '$2y$12$RQApAkdSiFEeqYpjcmd7vOT769g3aYYkF6OXeXf17/Tx38/dUsoZu', 2, NOW(), NOW()),
('Coordinador Demo', 'coordinador@sigc.test', '$2y$12$gTzcv8WIvwriMxO5pUYkVOgc/Iz7zuwKmVjrAz5FfupbCVeXLv1..', 3, NOW(), NOW());