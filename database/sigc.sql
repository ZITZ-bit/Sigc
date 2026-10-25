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
-- Insertar usuarios de prueba (contraseñas hasheadas con bcrypt)
-- -----------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
('Paciente Demo', 'paciente@sigc.test', '$2y$12$45kSmgpYTvdeiAJViw1yAudqK/C7YKC9irFtlIb2ZCF588Tj9MolO', 1, NOW(), NOW()),
('Medico Demo', 'medico@sigc.test', '$2y$12$RQApAkdSiFEeqYpjcmd7vOT769g3aYYkF6OXeXf17/Tx38/dUsoZu', 2, NOW(), NOW()),
('Coordinador Demo', 'coordinador@sigc.test', '$2y$12$gTzcv8WIvwriMxO5pUYkVOgc/Iz7zuwKmVjrAz5FfupbCVeXLv1..', 3, NOW(), NOW());