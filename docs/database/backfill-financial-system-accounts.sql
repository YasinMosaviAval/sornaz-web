INSERT INTO financial_system_accounts (account_id, user_id, type, balance, status)
SELECT
    u.user_id,
    u.user_id,
    CASE
        WHEN u.type = 'academy' THEN 'academy_main'
        ELSE 'student_wallet'
    END,
    0,
    'active'
FROM users u
WHERE u.deleted_at IS NULL
  AND NOT EXISTS (
      SELECT 1
      FROM financial_system_accounts a
      WHERE a.user_id = u.user_id
        AND a.deleted_at IS NULL
  );
