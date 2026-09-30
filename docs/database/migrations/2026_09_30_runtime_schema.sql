-- Apply on the test database before deploying the matching PHP files.
-- Apply each statement only after inspecting the current schema and taking a backup.
CREATE TABLE IF NOT EXISTS article_comment_receipts (
    comment_id BIGINT UNSIGNED PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Run this ALTER only when posts.type is still the old ENUM without 'page'.
-- ALTER TABLE posts MODIFY type ENUM('post','product','music_theory','page') DEFAULT 'post';

-- public_ratings is installed by 2026_09_01_add_public_ratings.sql.

-- Run this migration once on the test database, after backing up classroom_types.
-- It preserves existing ENUM labels as strings and lets category edits use ordinary DML.
ALTER TABLE classroom_types MODIFY `type` VARCHAR(50) NULL DEFAULT NULL;
