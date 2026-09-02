-- Two-sided rewards for article comments, comment replies, and profile ratings.
INSERT INTO user_point_rules
    (title, point_type, category, points, source, action, repeat_mode, daily_cap, cooldown_minutes, status, created_at, updated_at)
SELECT seed.title, seed.point_type, 'social', seed.points, 'database', seed.action, 'event', seed.daily_cap, 0, 'active', NOW(), NOW()
FROM (
    SELECT 'دریافت نظر برای مقاله' title, 'general' point_type, 4 points, 'public.article.comment.received' action, 20 daily_cap
    UNION ALL SELECT 'دریافت پاسخ برای نظر مقاله', 'general', 3, 'public.comment.reply.received', 20
    UNION ALL SELECT 'امتیاز دادن به پروفایل کاربر', 'general', 3, 'public.profile.rate', 10
    UNION ALL SELECT 'دریافت امتیاز برای پروفایل', 'professional', 5, 'public.profile.rating.received', 20
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM user_point_rules rules
    WHERE rules.source = 'database'
      AND rules.action = seed.action
      AND rules.academy_id IS NULL
      AND rules.branch_id IS NULL
      AND rules.deleted_at IS NULL
);
