CREATE TABLE `media_files` (
  `media_file_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `disk` enum('local','public','private','s3','wasabi','google') DEFAULT 'local',
  `directory` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `type` enum('image','video','audio','document','archive','other') DEFAULT 'image',
  `collection` enum('logo','cover','gallery','document','certificate','license','teacher_avatar','teacher_gallery','student_avatar','academy_video','academy_audio','branch_logo','branch_cover','branch_gallery','intro_video') DEFAULT NULL,
  `path` text DEFAULT NULL,
  `thumbnail_path` text DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `fileable_type` varchar(255) DEFAULT NULL,
  `fileable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `duration` int(11) UNSIGNED DEFAULT NULL,
  `width` int(11) UNSIGNED DEFAULT NULL,
  `height` int(11) UNSIGNED DEFAULT NULL,
  `checksum` varchar(255) DEFAULT NULL,
  `visibility` enum('public','private','academy_only') DEFAULT 'public',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `media_files`
  ADD PRIMARY KEY (`media_file_id`);

ALTER TABLE `media_files`
  MODIFY `media_file_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
