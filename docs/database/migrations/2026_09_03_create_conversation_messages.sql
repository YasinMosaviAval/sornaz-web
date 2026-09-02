CREATE TABLE IF NOT EXISTS conversation_messages (
    conversation_message_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL,
    body TEXT NULL,
    reply_to_message_id BIGINT UNSIGNED NULL,
    attachment_path TEXT NULL,
    attachment_name VARCHAR(255) NULL,
    attachment_mime VARCHAR(120) NULL,
    attachment_size BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_by BIGINT UNSIGNED NULL,
    INDEX idx_conversation_messages_conversation (conversation_id, conversation_message_id, deleted_at),
    INDEX idx_conversation_messages_sender (sender_id, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
