ALTER TABLE `user_availabilities`
    ADD COLUMN `member_id` BIGINT UNSIGNED NULL AFTER `user_id`,
    ADD INDEX `idx_user_availabilities_member` (`member_id`);

UPDATE `user_availabilities` ua
JOIN (
    SELECT user_id, MIN(member_id) AS member_id
    FROM academy_branch_members
    WHERE deleted_at IS NULL
    GROUP BY user_id
) member_map ON member_map.user_id = ua.user_id
SET ua.member_id = member_map.member_id
WHERE ua.member_id IS NULL;
