CREATE TABLE IF NOT EXISTS `legacy_import_map` (
    `legacy_import_map_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `source_system` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `source_id` BIGINT UNSIGNED NOT NULL,
    `target_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`legacy_import_map_id`),
    UNIQUE KEY `legacy_import_map_source_unique` (`source_system`, `entity_type`, `source_id`),
    KEY `legacy_import_map_target_index` (`entity_type`, `target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
