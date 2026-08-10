-- =====================================================================
--  Teacher Portfolio Platform - Database Schema
--  Engine: InnoDB | Charset: utf8mb4
--  Single `teachers` table serves both roles: 'teacher' & 'super-admin'
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `skoolyst_teachers`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `skoolyst_teachers`;

CREATE TABLE `teachers` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`               CHAR(36)      NOT NULL,
    `slug`               VARCHAR(191)  NOT NULL COMMENT 'SEO-friendly public URL segment, derived from name',

    -- Auth / role
    `role`               ENUM('super-admin','teacher') NOT NULL DEFAULT 'teacher',
    `password`           VARCHAR(255)  NOT NULL,
    `email`              VARCHAR(191)  NOT NULL,
    `email_verified_at`  DATETIME      NULL,
    `status`             ENUM('active','inactive','pending') NOT NULL DEFAULT 'active',
    `is_public`          TINYINT(1)    NOT NULL DEFAULT 1 COMMENT 'Show/hide portfolio in public directory',

    -- Basic profile
    `full_name`          VARCHAR(150)  NOT NULL,
    `profession_title`   VARCHAR(150)  NULL COMMENT 'e.g. Mathematics Teacher, Assistant Professor',
    `teacher_type`       ENUM('school','college','university','technical','medical','science',
                               'mathematics','arts','computer_science','general','other') NULL,
    `subject`            VARCHAR(150)  NULL COMMENT 'Primary teaching subject, used for filtering',
    `qualification`      VARCHAR(150)  NULL COMMENT 'Highest qualification, used for filtering',
    `phone`              VARCHAR(30)   NULL,
    `city`               VARCHAR(100)  NULL,
    `country`            VARCHAR(100)  NULL,
    `gender`              ENUM('male','female','other') NULL,
    `birthday`           DATE          NULL,
    `bio`                TEXT          NULL,
    `website`            VARCHAR(191)  NULL,
    `years_experience`   SMALLINT UNSIGNED NULL,
    `freelance_status`   ENUM('available','not_available') NULL,

    -- Media
    `profile_photo`      VARCHAR(255)  NULL,
    `cover_photo`        VARCHAR(255)  NULL,
    `resume_file`        VARCHAR(255)  NULL COMMENT 'Optional uploaded PDF; otherwise resume is generated dynamically',

    -- Flexible JSON sections (arrays of objects) - schemaless & scalable per-teacher
    `educations`         JSON          NULL,
    `experiences`        JSON          NULL,
    `skills`              JSON          NULL,
    `certifications`      JSON          NULL,
    `projects`            JSON          NULL,
    `languages`           JSON          NULL,
    `awards`              JSON          NULL,
    `social_links`        JSON          NULL,
    `services`            JSON          NULL,

    -- Presentation
    `template`            VARCHAR(50)   NOT NULL DEFAULT 'default' COMMENT 'Portfolio theme key, more added later',
    `accent_color`        VARCHAR(20)   NULL COMMENT 'Optional theme color override',

    -- Metrics
    `views_count`         INT UNSIGNED  NOT NULL DEFAULT 0,

    `created_at`           DATETIME      NOT NULL,
    `updated_at`            DATETIME      NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_teachers_uuid` (`uuid`),
    UNIQUE KEY `uq_teachers_slug` (`slug`),
    UNIQUE KEY `uq_teachers_email` (`email`),

    -- Indexes to keep the public directory + filters fast at scale
    KEY `idx_teachers_role_status_public` (`role`, `status`, `is_public`),
    KEY `idx_teachers_subject` (`subject`),
    KEY `idx_teachers_city` (`city`),
    KEY `idx_teachers_qualification` (`qualification`),
    KEY `idx_teachers_teacher_type` (`teacher_type`),
    FULLTEXT KEY `ft_teachers_search` (`full_name`, `profession_title`, `bio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Seed default Super Admin (migrated record)
-- Change the email/password immediately after first login.
-- Default password below is: Admin@12345
-- =====================================================================
INSERT INTO `teachers`
    (`uuid`, `slug`, `role`, `password`, `email`, `status`, `is_public`,
     `full_name`, `created_at`, `updated_at`)
VALUES
    (UUID(), 'super-admin', 'super-admin',
     '$2y$10$iL8RbkHIQqQBefN3.EDuNe4hwed/SOy4emhs2MMkKTZ0X5ljJ8fQO',
     'admin@skoolyst.com', 'active', 0,
     'Skoolyst Admin', NOW(), NOW());

-- =====================================================================
-- contact_calls
-- Phone numbers are only revealed to logged-in users. Every time a
-- logged-in user taps "Call Me" on a portfolio, we log who called whom
-- and when, for the teacher's contact history.
-- =====================================================================
CREATE TABLE `contact_calls` (
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
