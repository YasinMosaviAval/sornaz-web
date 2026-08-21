-- Move legacy public comment content to the translation table.
INSERT INTO translations
    (table_name, table_id, field, locale, value, version, created_at, created_by, updated_at, updated_by)
SELECT
    'comments', c.comment_id, 'content', 'fa', c.content, 1,
    COALESCE(c.created_at, CURRENT_TIMESTAMP), c.created_by,
    COALESCE(c.updated_at, CURRENT_TIMESTAMP), c.updated_by
FROM comments c
LEFT JOIN translations t
    ON t.table_name = 'comments'
   AND t.table_id = c.comment_id
   AND t.field = 'content'
   AND t.locale = 'fa'
   AND t.deleted_at IS NULL
WHERE c.content IS NOT NULL
  AND c.content <> ''
  AND c.deleted_at IS NULL
  AND t.translation_id IS NULL;

UPDATE comments c
SET c.content = NULL
WHERE c.content IS NOT NULL
  AND EXISTS (
      SELECT 1
      FROM translations t
      WHERE t.table_name = 'comments'
        AND t.table_id = c.comment_id
        AND t.field = 'content'
        AND t.locale = 'fa'
        AND t.deleted_at IS NULL
  );
