UPDATE `academy_branch_scheduling_rules` AS `rules`
INNER JOIN `academy_branches` AS `branches` ON `branches`.`branch_id` = `rules`.`branch_id`
SET `rules`.`academy_id` = `branches`.`academy_id`
WHERE `rules`.`branch_id` IS NOT NULL
  AND (`rules`.`academy_id` IS NULL OR `rules`.`academy_id` <> `branches`.`academy_id`);
