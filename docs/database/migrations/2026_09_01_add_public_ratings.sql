CREATE TABLE IF NOT EXISTS public_ratings (
    public_rating_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    target_type VARCHAR(30) NOT NULL,
    target_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    score TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_by BIGINT UNSIGNED NULL,
    UNIQUE KEY uq_public_rating_user_target (target_type, target_id, user_id),
    INDEX idx_public_rating_target (target_type, target_id, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
