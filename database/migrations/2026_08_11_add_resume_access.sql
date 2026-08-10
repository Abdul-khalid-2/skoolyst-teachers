-- =====================================================================
-- Migration: add teachers.resume_access
-- Lets a teacher choose whether their resume/CV can be downloaded by
-- everyone, or only by logged-in teachers.
-- Run against an EXISTING database (new installs get this from schema.sql):
--
--   mysql -u USER -p skoolyst_teachers < database/migrations/2026_08_11_add_resume_access.sql
-- =====================================================================

USE `skoolyst_teachers`;

ALTER TABLE `teachers`
    ADD COLUMN `resume_access` ENUM('everyone','login_required') NOT NULL DEFAULT 'everyone'
    COMMENT 'Teacher-controlled: who can download the resume/CV'
    AFTER `resume_file`;
