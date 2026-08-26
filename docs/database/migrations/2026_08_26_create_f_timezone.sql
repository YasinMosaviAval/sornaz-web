CREATE TABLE IF NOT EXISTS `f_timezone` (
  `timezone_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `timezone` varchar(100) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(), `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(), `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL, `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`timezone_id`), UNIQUE KEY `uq_f_timezone_timezone` (`timezone`), KEY `idx_f_timezone_status_sort` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `f_timezone` (`timezone_id`, `timezone`, `sort_order`, `status`, `created_by`, `updated_by`) VALUES
(1,'Asia/Tehran',1,'active',1,1),(2,'Asia/Dubai',2,'active',1,1),(3,'Asia/Istanbul',3,'active',1,1),
(4,'Europe/London',4,'active',1,1),(5,'Europe/Paris',5,'active',1,1),(6,'Europe/Berlin',6,'active',1,1),
(7,'Europe/Rome',7,'active',1,1),(8,'Europe/Amsterdam',8,'active',1,1),(9,'America/New_York',9,'active',1,1),
(10,'America/Chicago',10,'active',1,1),(11,'America/Los_Angeles',11,'active',1,1),(12,'America/Toronto',12,'active',1,1),(13,'UTC',13,'active',1,1)
ON DUPLICATE KEY UPDATE `timezone`=VALUES(`timezone`),`sort_order`=VALUES(`sort_order`),`status`='active',`deleted_at`=NULL,`deleted_by`=NULL;

INSERT INTO `f_translations` (`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_by`,`updated_by`)
SELECT 'f_timezone',s.timezone_id,s.locale,s.code,'title',s.title,1,1,1 FROM (
SELECT 1 timezone_id,'fa' locale,'timezone.asia_tehran' code,'تهران' title UNION ALL SELECT 1,'en','timezone.asia_tehran','Tehran'
UNION ALL SELECT 2,'fa','timezone.asia_dubai','دبی' UNION ALL SELECT 2,'en','timezone.asia_dubai','Dubai'
UNION ALL SELECT 3,'fa','timezone.asia_istanbul','استانبول' UNION ALL SELECT 3,'en','timezone.asia_istanbul','Istanbul'
UNION ALL SELECT 4,'fa','timezone.europe_london','لندن' UNION ALL SELECT 4,'en','timezone.europe_london','London'
UNION ALL SELECT 5,'fa','timezone.europe_paris','پاریس' UNION ALL SELECT 5,'en','timezone.europe_paris','Paris'
UNION ALL SELECT 6,'fa','timezone.europe_berlin','برلین' UNION ALL SELECT 6,'en','timezone.europe_berlin','Berlin'
UNION ALL SELECT 7,'fa','timezone.europe_rome','رم' UNION ALL SELECT 7,'en','timezone.europe_rome','Rome'
UNION ALL SELECT 8,'fa','timezone.europe_amsterdam','آمستردام' UNION ALL SELECT 8,'en','timezone.europe_amsterdam','Amsterdam'
UNION ALL SELECT 9,'fa','timezone.america_new_york','نیویورک' UNION ALL SELECT 9,'en','timezone.america_new_york','New York'
UNION ALL SELECT 10,'fa','timezone.america_chicago','شیکاگو' UNION ALL SELECT 10,'en','timezone.america_chicago','Chicago'
UNION ALL SELECT 11,'fa','timezone.america_los_angeles','لس‌آنجلس' UNION ALL SELECT 11,'en','timezone.america_los_angeles','Los Angeles'
UNION ALL SELECT 12,'fa','timezone.america_toronto','تورنتو' UNION ALL SELECT 12,'en','timezone.america_toronto','Toronto'
UNION ALL SELECT 13,'fa','timezone.utc','زمان هماهنگ جهانی' UNION ALL SELECT 13,'en','timezone.utc','UTC'
) s WHERE NOT EXISTS (SELECT 1 FROM `f_translations` t WHERE t.table_name='f_timezone' AND t.table_id=s.timezone_id AND t.locale=s.locale AND t.field='title' AND t.deleted_at IS NULL);
