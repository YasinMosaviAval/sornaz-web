ALTER TABLE conversation_messages
    ADD COLUMN reply_to_id BIGINT UNSIGNED NULL AFTER body,
    ADD COLUMN message_kind VARCHAR(20) NOT NULL DEFAULT 'text' AFTER reply_to_id,
    ADD INDEX idx_conversation_reply (reply_to_id);

ALTER TABLE social_posts ADD COLUMN shared_post_id BIGINT UNSIGNED NULL;

-- Earlier group membership events were stored as plain text by ChatService.
UPDATE conversation_messages m
JOIN conversations c ON c.conversation_id = m.conversation_id
SET m.message_kind = 'system'
WHERE c.type = 'group' AND m.attachment_path IS NULL AND m.edited_at IS NULL
  AND m.body REGEXP '^.+ توسط .+ (به گروه اضافه شد|از گروه حذف شد)[.]$|^.+ گروه را ترک کرد[.]$';
