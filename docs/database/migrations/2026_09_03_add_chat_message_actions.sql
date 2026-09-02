ALTER TABLE conversation_messages ADD COLUMN edited_at DATETIME NULL AFTER body;
CREATE TABLE IF NOT EXISTS conversation_message_reactions (
    conversation_message_reaction_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_message_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    reaction VARCHAR(30) NOT NULL DEFAULT 'like',
    created_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    UNIQUE KEY uq_conversation_message_reaction (conversation_message_id,user_id,reaction),
    INDEX idx_conversation_reaction_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
