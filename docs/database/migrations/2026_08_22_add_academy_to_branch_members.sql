ALTER TABLE `academy_branch_members`
  ADD COLUMN `academy_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `member_id`,
  ADD KEY `idx_academy_branch_members_academy` (`academy_id`),
  ADD KEY `idx_academy_branch_members_scope` (`academy_id`, `branch_id`, `user_id`);

UPDATE `academy_branch_members` AS `member`
JOIN `academy_branches` AS `branch` ON `branch`.`branch_id` = `member`.`branch_id`
SET `member`.`academy_id` = `branch`.`academy_id`
WHERE `member`.`academy_id` IS NULL;

ALTER TABLE `academy_branch_members`
  ADD CONSTRAINT `fk_academy_branch_members_academy`
    FOREIGN KEY (`academy_id`) REFERENCES `academies` (`academy_id`);
