-- =====================================================================
-- Migration: add contact_calls
-- Run this against an EXISTING database that was created before this
-- migration (new installs already get this table from schema.sql).
--
--   mysql -u USER -p skoolyst_teachers < database/migrations/2026_08_11_add_contact_calls.sql
-- =====================================================================

USE `skoolyst_teachers`;

CREATE TABLE IF NOT EXISTS `contact_calls` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `teacher_id`    BIGINT UNSIGNED NOT NULL COMMENT 'The teacher who was called (owner of the portfolio)',
    `caller_id`     BIGINT UNSIGNED NOT NULL COMMENT 'The logged-in user who clicked Call Me',
    `created_at`    DATETIME        NOT NULL,

    PRIMARY KEY (`id`),
    KEY `idx_contact_calls_teacher` (`teacher_id`, `created_at`),
    KEY `idx_contact_calls_caller` (`caller_id`, `created_at`),
    CONSTRAINT `fk_contact_calls_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_contact_calls_caller` FOREIGN KEY (`caller_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
