-- Continue with Google: link local teacher accounts to their Google
-- account id (OAuth "sub"). Run once on existing databases.
ALTER TABLE `teachers`
    ADD COLUMN `google_id` VARCHAR(191) NULL COMMENT 'Google account id / sub (Continue with Google)' AFTER `skoolyst_id`,
    ADD UNIQUE KEY `uq_teachers_google_id` (`google_id`);
