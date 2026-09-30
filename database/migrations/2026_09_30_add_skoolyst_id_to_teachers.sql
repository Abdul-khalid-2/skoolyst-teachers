-- Login with Skoolyst: link local teacher accounts to their central
-- skoolyst.com account id. Run once on existing databases.
ALTER TABLE `teachers`
    ADD COLUMN `skoolyst_id` BIGINT UNSIGNED NULL COMMENT 'Central Skoolyst account id (Login with Skoolyst)' AFTER `email_verified_at`,
    ADD UNIQUE KEY `uq_teachers_skoolyst_id` (`skoolyst_id`);
