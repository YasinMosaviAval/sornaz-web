-- Repair the confirmed legacy site-owner record only. Safe to run again.
-- Does not approve other pending users or reactivate deleted/blocked accounts.
UPDATE users
SET status = 'approved'
WHERE user_id = 1 AND username = 'Sornaz'
  AND status = 'pending' AND deleted_at IS NULL;

SELECT user_id, username, status, deleted_at
FROM users
WHERE user_id = 1 OR username IN ('academy_1', 'academy_2');
