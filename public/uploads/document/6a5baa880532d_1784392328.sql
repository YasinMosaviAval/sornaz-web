CREATE TABLE `user_contacts` (
  `user_contact_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode` enum('phone','email','social') DEFAULT NULL,
  `platform` enum('instagram','whats-app','youtube','spotify','soundcloud','telegram','linkedin','x','website','zoom','google-meet','skype','custom','other') DEFAULT NULL,
  `value` text DEFAULT NULL,
  `priority` enum('primary','secondary','emergency','ledger','support','other') DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `status` enum('pending','active','deactive','blocked','inactive') DEFAULT 'pending',
  `last_called_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `user_contacts`
  ADD PRIMARY KEY (`user_contact_id`);
ALTER TABLE `user_contacts`
  MODIFY `user_contact_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;