START TRANSACTION;

INSERT INTO `categories` (`category_id`, `name`, `group`, `slug`, `approved_at`)
VALUES
    (97,  'موسیقی ایران',             'posts', 'iranian-music',       NOW()),
    (98,  'تاریخ موسیقی',             'posts', 'music-history',       NOW()),
    (99,  'زندگینامه موسیقی دانان',   'posts', 'musician-biography', NOW()),
    (100, 'خرید ساز',                  'posts', 'instrument-purchase', NOW()),
    (101, 'مفاهیم اولیه',             'posts', 'early-meaning',       NOW()),
    (102, 'فرم های موسیقی',            'posts', 'music-forms',         NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `group` = VALUES(`group`),
    `slug` = VALUES(`slug`),
    `deleted_at` = NULL,
    `deleted_by` = NULL;

INSERT INTO `translations` (`table_name`, `table_id`, `locale`, `field`, `value`, `version`)
SELECT 'categories', seed.category_id, seed.locale, 'title', seed.title, 1
FROM (
    SELECT 97 category_id, 'fa' locale, 'موسیقی ایران' title UNION ALL
    SELECT 97, 'en', 'Iranian Music' UNION ALL
    SELECT 98, 'fa', 'تاریخ موسیقی' UNION ALL
    SELECT 98, 'en', 'Music History' UNION ALL
    SELECT 99, 'fa', 'زندگینامه موسیقی دانان' UNION ALL
    SELECT 99, 'en', 'Musician Biography' UNION ALL
    SELECT 100, 'fa', 'خرید ساز' UNION ALL
    SELECT 100, 'en', 'Instrument Purchase' UNION ALL
    SELECT 101, 'fa', 'مفاهیم اولیه' UNION ALL
    SELECT 101, 'en', 'Early Concepts' UNION ALL
    SELECT 102, 'fa', 'فرم های موسیقی' UNION ALL
    SELECT 102, 'en', 'Music Forms'
) seed
WHERE NOT EXISTS (
    SELECT 1 FROM `translations` t
    WHERE t.`table_name` = 'categories' AND t.`table_id` = seed.category_id
      AND t.`locale` = seed.locale AND t.`field` = 'title' AND t.`deleted_at` IS NULL
);

COMMIT;
