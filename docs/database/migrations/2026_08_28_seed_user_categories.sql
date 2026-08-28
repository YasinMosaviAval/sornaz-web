START TRANSACTION;

INSERT INTO `categories` (`name`, `group`, `slug`, `approved_at`)
SELECT seed.name, 'users', seed.slug, NOW()
FROM (
    SELECT 'اساتید' name, 'teacher' slug UNION ALL
    SELECT 'هنرجویان', 'student' UNION ALL
    SELECT 'مدیران', 'manager'
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM `categories` c WHERE c.`group` = 'users' AND c.`slug` = seed.slug AND c.`deleted_at` IS NULL
);

INSERT INTO `translations` (`table_name`, `table_id`, `locale`, `field`, `value`, `version`)
SELECT 'categories', c.category_id, seed.locale, 'title', seed.title, 1
FROM (
    SELECT 'teacher' slug, 'fa' locale, 'اساتید' title UNION ALL
    SELECT 'teacher', 'en', 'Teachers' UNION ALL
    SELECT 'student', 'fa', 'هنرجویان' UNION ALL
    SELECT 'student', 'en', 'Students' UNION ALL
    SELECT 'manager', 'fa', 'مدیران' UNION ALL
    SELECT 'manager', 'en', 'Managers'
) seed
JOIN `categories` c ON c.`group` = 'users' AND c.`slug` = seed.slug AND c.`deleted_at` IS NULL
WHERE NOT EXISTS (
    SELECT 1 FROM `translations` t
    WHERE t.`table_name` = 'categories' AND t.`table_id` = c.category_id
      AND t.`locale` = seed.locale AND t.`field` = 'title' AND t.`deleted_at` IS NULL
);

COMMIT;
