CREATE TABLE IF NOT EXISTS user_merges (
    user_merge_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_user_id BIGINT UNSIGNED NOT NULL,
    to_user_id BIGINT UNSIGNED NOT NULL,
    reason TEXT NULL,
    merged_at DATETIME NULL,
    merged_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    approved_at DATETIME NULL,
    approved_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_by BIGINT UNSIGNED NULL,
    INDEX idx_user_merges_target (to_user_id),
    INDEX idx_user_merges_source_base (from_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
