-- برای جدول قدیمی user_merges که پیش از قابلیت درخواست ادغام ایجاد شده است.
ALTER TABLE user_merges
    ADD COLUMN member_id BIGINT UNSIGNED NULL AFTER to_user_id,
    ADD COLUMN status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending' AFTER member_id,
    ADD COLUMN admin_note TEXT NULL AFTER reason;

ALTER TABLE user_merges
    ADD INDEX idx_user_merges_member_status (member_id, status),
    ADD INDEX idx_user_merges_target_status (to_user_id, status),
    ADD INDEX idx_user_merges_source (from_user_id);
