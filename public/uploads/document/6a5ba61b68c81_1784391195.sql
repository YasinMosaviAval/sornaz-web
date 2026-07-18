CREATE TABLE `user_availabilities` (
  `user_availability_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `day_of_week` enum('saturday','sunday','monday','tuesday','wednesday','thursday','friday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `type` enum('available','unavailable') DEFAULT NULL,
  `is_repeating` tinyint(1) DEFAULT 1,
  `repeat_period` enum('week','2-week','3-week','4-week','month','year') DEFAULT 'week',
  `is_closed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_availability_exceptions` (
  `user_availability_exception_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `type` enum('holiday','closed','unavailable','busy','vacation','blocked') DEFAULT 'unavailable',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `user_availabilities`
  ADD PRIMARY KEY (`user_availability_id`);

ALTER TABLE `user_availability_exceptions`
  ADD PRIMARY KEY (`user_availability_exception_id`);

ALTER TABLE `user_availabilities`
  MODIFY `user_availability_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_availability_exceptions`
  MODIFY `user_availability_exception_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
