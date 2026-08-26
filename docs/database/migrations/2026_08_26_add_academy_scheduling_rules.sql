ALTER TABLE `academy_branch_scheduling_rules`
  ADD COLUMN `academy_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `branch_id`,
  ADD KEY `idx_scheduling_rules_academy` (`academy_id`);
