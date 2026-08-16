-- Sornaz academy-scoped backup
-- academy_id: 7
-- generated_at: 2026-08-16T02:16:47+02:00
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `academies`;
CREATE TABLE `academies` (
  `academy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`academy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branches`;
CREATE TABLE `academy_branches` (
  `branch_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academy_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `academy_branch_type_id` tinyint(3) unsigned DEFAULT NULL,
  `mode` enum('online','physical','hybrid') DEFAULT NULL,
  `timezone` enum('Asia/Tehran') DEFAULT 'Asia/Tehran',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_bookings`;
CREATE TABLE `academy_branch_bookings` (
  `booking_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('private-class','group-class','consultation','room-reservation') DEFAULT 'private-class',
  `status` enum('pending','approved','rejected','completed','canceled','rescheduled','completed','held','postponed') DEFAULT 'pending',
  `requested_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `idx_bookings_schedule` (`requested_date`,`start_time`,`end_time`,`status`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=279371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_classrooms`;
CREATE TABLE `academy_branch_classrooms` (
  `classroom_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `type_id` bigint(20) unsigned DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `status` enum('pending','available','maintenance','reserved') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`classroom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_classroom_assets`;
CREATE TABLE `academy_branch_classroom_assets` (
  `classroom_asset_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `classroom_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`classroom_asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=829 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_classroom_types`;
CREATE TABLE `academy_branch_classroom_types` (
  `classroom_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`classroom_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_courses`;
CREATE TABLE `academy_branch_courses` (
  `course_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `lesson_id` bigint(20) unsigned DEFAULT NULL,
  `level_id` bigint(20) unsigned DEFAULT NULL,
  `teacher_capacity` int(11) DEFAULT 1,
  `student_capacity` int(11) DEFAULT 1,
  `status` enum('pending','open','ongoing','finished') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  KEY `idx_course_lesson` (`lesson_id`),
  KEY `idx_courses_branch_deleted` (`branch_id`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=918 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_terms`;
CREATE TABLE `academy_branch_course_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `session_count` int(11) DEFAULT NULL,
  `session_period` enum('week','2-week','3-week','4-week','month','year','no-period') DEFAULT 'week',
  `price` decimal(12,2) DEFAULT NULL,
  `currency_id` tinyint(4) DEFAULT NULL,
  `status` enum('pending','open','ongoing','finished') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_id`),
  KEY `idx_terms_course_deleted` (`course_id`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=46559 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_term_enrollments`;
CREATE TABLE `academy_branch_course_term_enrollments` (
  `term_enrollment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('teacher','student','other') DEFAULT NULL,
  `status` enum('pending','active','canceled','completed') DEFAULT 'pending',
  `joined_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_enrollment_id`),
  KEY `idx_enrollments_term_type` (`term_id`,`type`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=51256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_term_invoices`;
CREATE TABLE `academy_branch_course_term_invoices` (
  `term_invoice_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `discount_id` bigint(20) unsigned DEFAULT NULL,
  `payable_amount` decimal(14,2) DEFAULT NULL,
  `currency_id` tinyint(4) DEFAULT NULL,
  `status` enum('draft','issued','partial','paid','canceled') DEFAULT 'draft',
  `due_date` date DEFAULT NULL,
  `issued_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46562 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_term_invoice_installments`;
CREATE TABLE `academy_branch_course_term_invoice_installments` (
  `term_invoice_installment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `installment_number` int(11) DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid','underpaid') DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_invoice_installment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=116848 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_term_sessions`;
CREATE TABLE `academy_branch_course_term_sessions` (
  `term_session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `booking_id` bigint(20) unsigned DEFAULT NULL,
  `classroom_id` bigint(20) unsigned DEFAULT NULL,
  `branch_url_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_mode` enum('in_person','online') NOT NULL DEFAULT 'in_person',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_session_id`),
  KEY `idx_term_sessions_term_date` (`term_id`,`deleted_at`,`booking_id`),
  KEY `idx_term_sessions_booking` (`booking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=279371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_course_term_session_attendances`;
CREATE TABLE `academy_branch_course_term_session_attendances` (
  `session_attendance_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) unsigned DEFAULT NULL,
  `term_enrollment_id` bigint(20) unsigned DEFAULT NULL,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('present','absent','late','leave','excused_absence','online') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`session_attendance_id`),
  KEY `idx_attendance_session_member` (`session_id`,`member_id`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=297459 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_members`;
CREATE TABLE `academy_branch_members` (
  `member_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','pending','rejected') DEFAULT 'pending',
  `joined_at` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_member_contracts`;
CREATE TABLE `academy_branch_member_contracts` (
  `member_contract_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('teacher','receptionist','manager','other') DEFAULT NULL,
  `user_lesson_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `price` decimal(12,2) unsigned DEFAULT NULL,
  `currency_id` tinyint(4) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`member_contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_member_permissions`;
CREATE TABLE `academy_branch_member_permissions` (
  `academy_branch_member_permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `permission_id` bigint(20) unsigned DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`academy_branch_member_permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_member_roles`;
CREATE TABLE `academy_branch_member_roles` (
  `member_role_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `is_main` tinyint(1) unsigned DEFAULT NULL,
  `granted_by` bigint(20) DEFAULT NULL,
  `granted_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`member_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_scheduling_rules`;
CREATE TABLE `academy_branch_scheduling_rules` (
  `scheduling_rule_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `rule_type` enum('cancellation','makeup','reservation','scheduling') NOT NULL,
  `rule_value` decimal(12,2) unsigned NOT NULL,
  `rule_value_unit` enum('hour','minute','day','session','absence','person','year','boolean','percent','currency') NOT NULL,
  `status` enum('active','inactive','pending','deleted') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`scheduling_rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_branch_types`;
CREATE TABLE `academy_branch_types` (
  `academy_branch_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('music','poetry','painting','hybrid','other') DEFAULT 'music',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`academy_branch_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `academy_documents`;
CREATE TABLE `academy_documents` (
  `academy_document_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academy_id` bigint(20) unsigned NOT NULL,
  `media_file_id` bigint(20) unsigned NOT NULL,
  `document_type` enum('license','identity','statute','tax','contract','certificate','other') NOT NULL DEFAULT 'other',
  `document_number` varchar(100) DEFAULT NULL,
  `issued_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','expired') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`academy_document_id`),
  UNIQUE KEY `uq_academy_document_media` (`media_file_id`),
  KEY `idx_academy_documents_academy` (`academy_id`,`status`),
  KEY `idx_academy_documents_expiry` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `access_system_permissions`;
CREATE TABLE `access_system_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `resource` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `type` enum('menu','sidebar','topbar','header') DEFAULT NULL,
  `group_name` enum('website','academy') DEFAULT NULL,
  `scope` enum('platform','website','academy','branch','self') NOT NULL DEFAULT 'website',
  `risk_level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `approval` enum('confirm','pending') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `idx_access_permissions_resource_action` (`resource`,`action`),
  KEY `idx_access_permissions_scope_risk` (`scope`,`risk_level`)
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `access_system_roles`;
CREATE TABLE `access_system_roles` (
  `role_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_role_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` enum('system','academy','other') DEFAULT NULL,
  `level` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `scope` enum('platform','website','academy','branch','self') NOT NULL DEFAULT 'website',
  `color` varchar(9) DEFAULT NULL,
  `sort_order` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `idx_access_roles_parent` (`parent_role_id`),
  KEY `idx_access_roles_scope_level` (`scope`,`level`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `access_system_role_permissions`;
CREATE TABLE `access_system_role_permissions` (
  `role_permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`role_permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `access_system_setting_permissions`;
CREATE TABLE `access_system_setting_permissions` (
  `setting_permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_id` bigint(20) unsigned DEFAULT NULL,
  `permission_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`setting_permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=540 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `group` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_accounts`;
CREATE TABLE `financial_system_accounts` (
  `account_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('student_wallet','academy_main','teacher_wallet','platform_revenue','cash','bank') DEFAULT NULL,
  `currency_id` tinyint(4) DEFAULT NULL,
  `balance` decimal(14,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_currency`;
CREATE TABLE `financial_system_currency` (
  `currency_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `icon_path` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_discounts`;
CREATE TABLE `financial_system_discounts` (
  `discount_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `type` enum('percentage','fixed') DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `max_usage` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','active','rejected','expire') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`discount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_ledger_entries`;
CREATE TABLE `financial_system_ledger_entries` (
  `ledger_entry_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('debit','credit') DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`ledger_entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_payments`;
CREATE TABLE `financial_system_payments` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `payer_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `currency_id` tinyint(4) DEFAULT NULL,
  `method` enum('cash','card','bank_transfer','online') DEFAULT NULL,
  `status` enum('pending','completed','failed','canceled') DEFAULT 'pending',
  `reference_code` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_refunds`;
CREATE TABLE `financial_system_refunds` (
  `refund_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`refund_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `financial_system_transactions`;
CREATE TABLE `financial_system_transactions` (
  `transaction_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('payment','refund','transfer') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `f_settings`;
CREATE TABLE `f_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `page` varchar(100) DEFAULT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT NULL,
  `variable_name` varchar(100) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `source` varchar(500) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

DROP TABLE IF EXISTS `f_translations`;
CREATE TABLE `f_translations` (
  `translation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) DEFAULT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `locale` varchar(5) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `field` varchar(100) DEFAULT NULL,
  `value` text DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`translation_id`),
  KEY `idx_f_translations_lookup` (`table_name`,`table_id`,`field`,`locale`,`version`)
) ENGINE=InnoDB AUTO_INCREMENT=2025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `instruments`;
CREATE TABLE `instruments` (
  `instrument_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`instrument_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE `lessons` (
  `lesson_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels` (
  `level_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('learning','academic') DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `media_files`;
CREATE TABLE `media_files` (
  `media_file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `disk` enum('local','public','private','s3','wasabi','google') DEFAULT 'local',
  `directory` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `type` enum('image','video','audio','document','archive','other') DEFAULT 'image',
  `collection` enum('logo','cover','gallery','document','certificate','license','teacher_avatar','teacher_gallery','student_avatar','academy_video','academy_audio','branch_logo','branch_cover','branch_gallery','intro_video') DEFAULT NULL,
  `path` text DEFAULT NULL,
  `thumbnail_path` text DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `fileable_type` varchar(255) DEFAULT NULL,
  `fileable_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(10) unsigned DEFAULT 0,
  `size` bigint(20) unsigned DEFAULT NULL,
  `duration` int(11) unsigned DEFAULT NULL,
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `checksum` varchar(255) DEFAULT NULL,
  `visibility` enum('public','private','academy_only') DEFAULT 'public',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`media_file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `otp_codes`;
CREATE TABLE `otp_codes` (
  ` otp_code_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(255) DEFAULT NULL,
  `type` enum('email','phone') DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `purpose` enum('register','login','reset') DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (` otp_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(20) unsigned DEFAULT NULL,
  `categories` varchar(500) DEFAULT NULL,
  `cover` varchar(200) DEFAULT NULL,
  `cover_media_id` bigint(20) unsigned DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `type` enum('post','product','music_theory','page') DEFAULT 'post',
  `status` enum('draft','published','private','inherit','pending','trash','auto-draft','future','request-pending','request-confirmed') DEFAULT 'pending',
  `visibility` enum('public','private','followers','premium') DEFAULT 'public',
  `visibility_user_id` bigint(20) unsigned DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `comment_count` bigint(20) DEFAULT NULL,
  `name` varchar(3000) DEFAULT NULL,
  `pinged` text DEFAULT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `related_posts_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=458 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `page` varchar(100) DEFAULT NULL,
  `sort_order` tinyint(3) unsigned DEFAULT NULL,
  `variable_name` varchar(100) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `source` varchar(500) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

DROP TABLE IF EXISTS `system_events`;
CREATE TABLE `system_events` (
  `system_event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`system_event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tracking_ingestion_batches`;
CREATE TABLE `tracking_ingestion_batches` (
  `tracking_ingestion_batch_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_uuid` char(36) NOT NULL,
  `tracking_user_session_id` bigint(20) unsigned NOT NULL,
  `page_view_id` bigint(20) unsigned NOT NULL,
  `events_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`tracking_ingestion_batch_id`),
  UNIQUE KEY `uq_tracking_ingestion_batch_uuid` (`batch_uuid`),
  KEY `idx_tracking_ingestion_session` (`tracking_user_session_id`,`created_at`),
  KEY `idx_tracking_ingestion_page` (`page_view_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_activity_intervals`;
CREATE TABLE `tracking_user_activity_intervals` (
  `tracking_user_activity_interval_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `interval_uuid` char(36) NOT NULL,
  `tracking_user_session_id` bigint(20) unsigned NOT NULL,
  `page_view_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `activity_type` enum('active','idle','reading','hidden','disconnected') NOT NULL,
  `started_at` datetime(3) NOT NULL,
  `ended_at` datetime(3) DEFAULT NULL,
  `duration_ms` bigint(20) unsigned DEFAULT NULL,
  `section_key` varchar(100) DEFAULT NULL,
  `created_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  PRIMARY KEY (`tracking_user_activity_interval_id`),
  UNIQUE KEY `uq_tracking_activity_interval_uuid` (`interval_uuid`),
  KEY `idx_tracking_activity_session_time` (`tracking_user_session_id`,`started_at`),
  KEY `idx_tracking_activity_page_time` (`page_view_id`,`started_at`),
  KEY `idx_tracking_activity_user_type` (`user_id`,`activity_type`,`started_at`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_consents`;
CREATE TABLE `tracking_user_consents` (
  `tracking_user_consent_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `guest_id` varchar(64) DEFAULT NULL,
  `analytics_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `personalization_allowed` tinyint(1) NOT NULL DEFAULT 0,
  `marketing_allowed` tinyint(1) NOT NULL DEFAULT 0,
  `policy_version` varchar(20) NOT NULL DEFAULT '1',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `granted_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tracking_user_consent_id`),
  KEY `idx_tracking_consent_user` (`user_id`,`created_at`),
  KEY `idx_tracking_consent_guest` (`guest_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_content_engagements`;
CREATE TABLE `tracking_user_content_engagements` (
  `tracking_user_content_engagement_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tracking_user_session_id` bigint(20) unsigned NOT NULL,
  `page_view_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `section_key` varchar(100) NOT NULL,
  `section_type` varchar(50) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `impression_count` int(10) unsigned NOT NULL DEFAULT 0,
  `visible_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `active_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `idle_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reading_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `max_visibility_percent` smallint(5) unsigned NOT NULL DEFAULT 0,
  `interaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `click_count` int(10) unsigned NOT NULL DEFAULT 0,
  `first_seen_at` datetime(3) DEFAULT NULL,
  `last_seen_at` datetime(3) DEFAULT NULL,
  `created_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` datetime(3) DEFAULT NULL ON UPDATE current_timestamp(3),
  PRIMARY KEY (`tracking_user_content_engagement_id`),
  UNIQUE KEY `uq_tracking_page_section` (`page_view_id`,`section_key`),
  KEY `idx_tracking_engagement_entity` (`entity_type`,`entity_id`),
  KEY `idx_tracking_engagement_user` (`user_id`,`created_at`),
  KEY `idx_tracking_engagement_session` (`tracking_user_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_events`;
CREATE TABLE `tracking_user_events` (
  `tracking_user_event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_uuid` char(36) NOT NULL,
  `tracking_user_session_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `page_view_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ارتباط با صفحه فعلی',
  `sequence_number` bigint(20) unsigned NOT NULL,
  `event_name` varchar(100) NOT NULL COMMENT 'مثال: add_to_cart, button_click, form_submit, search',
  `event_category` varchar(50) DEFAULT NULL,
  `event_action` varchar(100) DEFAULT NULL,
  `event_label` varchar(255) DEFAULT NULL,
  `occurred_at` datetime(3) NOT NULL,
  `received_at` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `client_timestamp_ms` bigint(20) unsigned DEFAULT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'داده‌های اضافی (مثلاً product_id, price, ...)' CHECK (json_valid(`event_data`)),
  `page_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `event_version` smallint(5) unsigned NOT NULL DEFAULT 1,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` varchar(191) DEFAULT NULL,
  `target_name` varchar(191) DEFAULT NULL,
  `target_text` varchar(255) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `section_key` varchar(100) DEFAULT NULL,
  `position_x` int(11) DEFAULT NULL,
  `position_y` int(11) DEFAULT NULL,
  `viewport_x` int(11) DEFAULT NULL,
  `viewport_y` int(11) DEFAULT NULL,
  `scroll_x` int(11) DEFAULT NULL,
  `scroll_y` int(11) DEFAULT NULL,
  `scroll_depth` smallint(5) unsigned DEFAULT NULL,
  `duration_ms` int(10) unsigned DEFAULT NULL,
  `numeric_value` decimal(18,4) DEFAULT NULL,
  `is_trusted` tinyint(1) DEFAULT NULL,
  `source` enum('client','server','system') NOT NULL DEFAULT 'client',
  PRIMARY KEY (`tracking_user_event_id`),
  UNIQUE KEY `uq_tracking_event_uuid` (`event_uuid`),
  KEY `idx_event_name` (`event_name`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_tracking_user_session_id` (`tracking_user_session_id`),
  KEY `idx_tracking_event_page_sequence` (`page_view_id`,`sequence_number`),
  KEY `idx_tracking_event_user_time` (`user_id`,`occurred_at`),
  KEY `idx_tracking_event_name_time` (`event_name`,`occurred_at`),
  KEY `idx_tracking_event_entity` (`entity_type`,`entity_id`),
  KEY `idx_tracking_event_section` (`section_key`,`occurred_at`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_page_views`;
CREATE TABLE `tracking_user_page_views` (
  `tracking_user_page_view_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_view_uuid` char(36) NOT NULL,
  `tracking_user_session_id` bigint(20) unsigned NOT NULL,
  `tab_id` varchar(64) DEFAULT NULL,
  `sequence_number` int(10) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `page_url` varchar(500) NOT NULL,
  `page_path` varchar(1000) DEFAULT NULL,
  `canonical_url` varchar(1000) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `route_name` varchar(100) DEFAULT NULL COMMENT 'نام روت لاراول یا مسیر',
  `page_type` varchar(50) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `locale` varchar(10) DEFAULT NULL,
  `entered_at` datetime NOT NULL DEFAULT current_timestamp(),
  `exited_at` datetime DEFAULT NULL,
  `duration` int(10) unsigned DEFAULT NULL COMMENT 'مدت زمان حضور در این صفحه (ثانیه)',
  `referrer` varchar(500) DEFAULT NULL,
  `referrer_domain` varchar(255) DEFAULT NULL,
  `query_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`query_params`)),
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `scroll_depth` int(11) DEFAULT NULL COMMENT 'درصد اسکرول (0-100)',
  `click_count` int(10) unsigned DEFAULT 0,
  `mouse_movement` int(10) unsigned DEFAULT NULL COMMENT 'تعداد حرکات ماوس (اختیاری)',
  `is_exit_page` tinyint(1) DEFAULT 0 COMMENT 'آخرین صفحه بازدید شده',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_activity_at` datetime(3) DEFAULT NULL,
  `last_heartbeat_at` datetime(3) DEFAULT NULL,
  `total_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `visible_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `hidden_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `active_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `idle_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `reading_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `interaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `keypress_count` int(10) unsigned NOT NULL DEFAULT 0,
  `form_interaction_count` int(10) unsigned NOT NULL DEFAULT 0,
  `max_scroll_depth` smallint(5) unsigned NOT NULL DEFAULT 0,
  `max_scroll_y` int(10) unsigned NOT NULL DEFAULT 0,
  `content_height` int(10) unsigned DEFAULT NULL,
  `first_interaction_at` datetime(3) DEFAULT NULL,
  `last_interaction_at` datetime(3) DEFAULT NULL,
  `exit_reason` varchar(50) DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tracking_user_page_view_id`),
  UNIQUE KEY `uq_tracking_page_view_uuid` (`page_view_uuid`),
  KEY `idx_tracking_user_session_id` (`tracking_user_session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_page_url` (`page_url`(191)),
  KEY `idx_entered_at` (`entered_at`),
  KEY `idx_route_name` (`route_name`),
  KEY `idx_tracking_page_session_sequence` (`tracking_user_session_id`,`sequence_number`),
  KEY `idx_tracking_page_user_entered` (`user_id`,`entered_at`),
  KEY `idx_tracking_page_entity` (`entity_type`,`entity_id`),
  KEY `idx_tracking_page_type_entered` (`page_type`,`entered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tracking_user_sessions`;
CREATE TABLE `tracking_user_sessions` (
  `tracking_user_session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'کاربر لاگین شده (اگر مهمان باشد NULL)',
  `session_id` varchar(128) NOT NULL COMMENT 'شناسه منحصر به فرد جلسه (Session ID)',
  `authenticated_session_id` bigint(20) unsigned DEFAULT NULL,
  `guest_id` varchar(64) DEFAULT NULL COMMENT 'شناسه مهمان (برای کاربران ناشناس)',
  `status` enum('active','idle','ended','expired','abandoned') NOT NULL DEFAULT 'active',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','bot','unknown') DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL COMMENT 'سیستم عامل',
  `browser` varchar(100) DEFAULT NULL COMMENT 'مرورگر',
  `browser_version` varchar(50) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL COMMENT 'کد کشور (IR, US, ...) از IP',
  `entry_page` varchar(1000) DEFAULT NULL,
  `exit_page` varchar(1000) DEFAULT NULL,
  `total_duration` int(10) unsigned DEFAULT NULL COMMENT 'کل زمان حضور در سایت (به ثانیه)',
  `page_views_count` int(10) unsigned DEFAULT 0 COMMENT 'تعداد صفحات بازدید شده',
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_activity_at` datetime(3) DEFAULT NULL,
  `last_heartbeat_at` datetime(3) DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `end_reason` enum('logout','tab_closed','browser_closed','timeout','session_expired','network_lost','unknown') DEFAULT NULL,
  `auth_revoked_at` datetime(3) DEFAULT NULL,
  `auth_revoked_by` bigint(20) unsigned DEFAULT NULL,
  `is_bounced` tinyint(1) DEFAULT 0 COMMENT 'آیا کاربر فقط یک صفحه دیده و رفته؟',
  `is_bot` tinyint(1) DEFAULT 0 COMMENT 'تشخیص ربات',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `active_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `idle_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `visible_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `hidden_duration_ms` bigint(20) unsigned NOT NULL DEFAULT 0,
  `events_count` int(10) unsigned NOT NULL DEFAULT 0,
  `engagement_score` decimal(8,4) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `screen_width` smallint(5) unsigned DEFAULT NULL,
  `screen_height` smallint(5) unsigned DEFAULT NULL,
  `viewport_width` smallint(5) unsigned DEFAULT NULL,
  `viewport_height` smallint(5) unsigned DEFAULT NULL,
  `device_pixel_ratio` decimal(5,2) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `app_version` varchar(50) DEFAULT NULL,
  `tracking_version` smallint(5) unsigned NOT NULL DEFAULT 1,
  `consent_status` enum('granted','denied','partial','unknown') NOT NULL DEFAULT 'unknown',
  `city_name` varchar(100) DEFAULT NULL,
  `region_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`tracking_user_session_id`),
  UNIQUE KEY `uq_tracking_visit_uuid` (`visit_uuid`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_started_at` (`started_at`),
  KEY `idx_device_type` (`device_type`),
  KEY `idx_country_code` (`country_code`),
  KEY `idx_tracking_user_status_activity` (`status`,`last_activity_at`),
  KEY `idx_tracking_user_user_started` (`user_id`,`started_at`),
  KEY `idx_tracking_user_guest_started` (`guest_id`,`started_at`),
  KEY `idx_tracking_user_auth_session` (`authenticated_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
  `translation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) DEFAULT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `locale` varchar(5) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `field` varchar(100) DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`translation_id`),
  KEY `idx_translations_lookup` (`table_name`,`table_id`,`field`,`locale`,`version`)
) ENGINE=InnoDB AUTO_INCREMENT=6446508 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_verified_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `national_code` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `type` enum('academy','branch','human') DEFAULT 'human',
  `status` enum('pending','approved','rejected','inactive','banned') DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `avatar_file_id` bigint(20) unsigned DEFAULT NULL,
  `visibility` enum('public','private','unlisted') NOT NULL DEFAULT 'unlisted',
  `birthday` date DEFAULT NULL,
  `register_time` datetime DEFAULT current_timestamp(),
  `register_method` enum('email','phone','google','academy','admin') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  UNIQUE KEY `uq_users_national_code` (`national_code`),
  UNIQUE KEY `uk_users_phone` (`phone`),
  KEY `idx_users_type` (`type`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT 0,
  `province_id` bigint(20) unsigned DEFAULT NULL,
  `county_id` bigint(20) unsigned DEFAULT NULL,
  `is_main` tinyint(3) unsigned DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `postal_code` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_availabilities`;
CREATE TABLE `user_availabilities` (
  `user_availability_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `day_of_week` enum('saturday','sunday','monday','tuesday','wednesday','thursday','friday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `type` enum('pending','available','unavailable','reserved') DEFAULT 'pending',
  `is_repeating` tinyint(1) DEFAULT 1,
  `repeat_period` enum('week','2-week','3-week','4-week','month','year','none') DEFAULT 'week',
  `is_closed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_availability_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_day` (`day_of_week`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_availability_exceptions`;
CREATE TABLE `user_availability_exceptions` (
  `user_availability_exception_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `type` enum('holiday','closed','unavailable','busy','vacation','blocked') DEFAULT 'unavailable',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_availability_exception_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_contacts`;
CREATE TABLE `user_contacts` (
  `user_contact_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `mode` enum('phone','email','social') DEFAULT NULL,
  `platform` enum('instagram','whats-app','youtube','spotify','soundcloud','telegram','linkedin','x','website','zoom','google-meet','skype','custom','other') DEFAULT NULL,
  `priority` enum('primary','secondary','emergency','ledger','support','other') DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `status` enum('pending','active','deactive','blocked','inactive') DEFAULT 'pending',
  `last_called_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_instruments`;
CREATE TABLE `user_instruments` (
  `user_instrument_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `instrument_id` bigint(20) unsigned DEFAULT NULL,
  `level_id` bigint(20) DEFAULT NULL,
  `start_date` varchar(10) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_instrument_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12952 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_lessons`;
CREATE TABLE `user_lessons` (
  `user_lesson_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `lesson_id` bigint(20) unsigned DEFAULT NULL,
  `level_id` bigint(20) DEFAULT NULL,
  `start_date` varchar(10) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13565 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_messages`;
CREATE TABLE `user_messages` (
  `user_message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned DEFAULT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('text','system','file','notification') DEFAULT NULL,
  `content` text DEFAULT NULL,
  `reply_to_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` int(11) unsigned DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `related_entity_type` varchar(50) DEFAULT NULL,
  `related_entity_id` bigint(20) unsigned DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_notifications`;
CREATE TABLE `user_notifications` (
  `user_notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2445 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `user_permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `permission_id` bigint(20) unsigned DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `expires_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_points`;
CREATE TABLE `user_points` (
  `user_point_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('general','professional') DEFAULT NULL,
  `points` int(11) unsigned DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_point_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_referrals`;
CREATE TABLE `user_referrals` (
  `user_referral_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `invite_code` varchar(24) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `referred_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','converted','blocked') NOT NULL DEFAULT 'active',
  `converted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_referral_id`),
  UNIQUE KEY `uq_user_referrals_user` (`user_id`),
  UNIQUE KEY `uq_user_referrals_code` (`invite_code`),
  KEY `idx_user_referrals_referrer` (`referred_by_user_id`),
  KEY `idx_user_referrals_status` (`status`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_role_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `is_main` tinyint(1) unsigned DEFAULT NULL,
  `granted_by` bigint(20) DEFAULT NULL,
  `granted_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `user_session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_verifications`;
CREATE TABLE `user_verifications` (
  `user_verification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected','expired','revoked') DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_verification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `verification_levels`;
CREATE TABLE `verification_levels` (
  `verification_level_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `priority` int(11) DEFAULT 0,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`verification_level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `world_iran_counties`;
CREATE TABLE `world_iran_counties` (
  `county_id` int(10) unsigned NOT NULL,
  `county_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `world_iran_provinces`;
CREATE TABLE `world_iran_provinces` (
  `province_id` int(11) NOT NULL,
  `province_name` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `tel_prefix` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`province_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_academy_branch_course_term_waiting_list`;
CREATE TABLE `z_academy_branch_course_term_waiting_list` (
  `term_waiting_list_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`term_waiting_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_academy_branch_scheduling_queues`;
CREATE TABLE `z_academy_branch_scheduling_queues` (
  `scheduling_queue_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','processing','done','failed') DEFAULT NULL,
  `type` enum('booking','term_generation') DEFAULT NULL,
  `priority` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`scheduling_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_conversations`;
CREATE TABLE `z_conversations` (
  `conversation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `last_message_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('direct','group','system') DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_conversation_members`;
CREATE TABLE `z_conversation_members` (
  `conversation_member_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `last_read_message_id` bigint(20) unsigned DEFAULT NULL,
  `role` enum('member','admin') DEFAULT NULL,
  `is_muted` tinyint(1) DEFAULT 0,
  `left_at` datetime DEFAULT NULL,
  `joined_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`conversation_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_languages`;
CREATE TABLE `z_languages` (
  `code` varchar(5) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `direction` enum('ltr','rtl') DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_music_structure`;
CREATE TABLE `z_music_structure` (
  `music_structure_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `parent_structure_id` bigint(20) unsigned DEFAULT NULL,
  `mode` enum('structural','melodic','rhytmic') DEFAULT NULL,
  `type` enum('dastgeh','gushe') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`music_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_music_structure_degrees`;
CREATE TABLE `z_music_structure_degrees` (
  `music_structure_degree_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `music_structure_id` bigint(20) unsigned DEFAULT NULL,
  `degree_structure` text DEFAULT NULL,
  `mode` enum('upper','lower') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`music_structure_degree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_music_structure_properties`;
CREATE TABLE `z_music_structure_properties` (
  `music_structure_property_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `music_structure_id` bigint(20) DEFAULT NULL,
  `title` int(11) DEFAULT NULL,
  `brief` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `note_example` text DEFAULT NULL,
  `audio_example` text DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `isuuer` varchar(1000) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`music_structure_property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_music_structure_property_others`;
CREATE TABLE `z_music_structure_property_others` (
  `music_structure_property_other_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `music_structure_property_id` bigint(20) unsigned DEFAULT NULL,
  `title` text DEFAULT NULL,
  `brief` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL,
  `issuer` varchar(1000) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`music_structure_property_other_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_music_structure_sentences`;
CREATE TABLE `z_music_structure_sentences` (
  `music_structure_sentence_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `music_structure_id` bigint(20) unsigned DEFAULT NULL,
  `start_note` varchar(100) DEFAULT NULL,
  `end_note` varchar(100) DEFAULT NULL,
  `degree_structure` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `brief` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes_repeat_numbers` text DEFAULT NULL,
  `important_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`music_structure_sentence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_settings`;
CREATE TABLE `z_settings` (
  `setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `group` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_sor_contacts`;
CREATE TABLE `z_sor_contacts` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `author` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `author_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `author_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `author_ip` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `has_response` tinyint(4) DEFAULT NULL,
  `approved` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `parent` bigint(20) unsigned DEFAULT NULL,
  `receiver_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

DROP TABLE IF EXISTS `z_sor_lessons`;
CREATE TABLE `z_sor_lessons` (
  `lesson_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_fa` varchar(200) DEFAULT NULL,
  `name_en` varchar(200) DEFAULT NULL,
  `description_fa` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `reigion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_taggables`;
CREATE TABLE `z_taggables` (
  `tag_id` bigint(20) unsigned DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `entity_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_tags`;
CREATE TABLE `z_tags` (
  `tag_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `group` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_approvals`;
CREATE TABLE `z_user_approvals` (
  `user_approval_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `by_user_id` bigint(20) unsigned DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `action` enum('approve','reject') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_audit_logs`;
CREATE TABLE `z_user_audit_logs` (
  `user_audit_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `entity_id` bigint(20) unsigned DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_audit_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_auth_providers`;
CREATE TABLE `z_user_auth_providers` (
  `user_auth_provider_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `provider_user_id` varchar(255) DEFAULT NULL,
  `provider_email` varchar(255) DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_auth_provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_awards`;
CREATE TABLE `z_user_awards` (
  `user_award_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_award_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_badges`;
CREATE TABLE `z_user_badges` (
  `user_badge_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `verification_level_id` bigint(20) unsigned DEFAULT NULL,
  `granted_by` bigint(20) unsigned DEFAULT NULL,
  `granted_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `status` enum('active','expired','revoked') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_certificates`;
CREATE TABLE `z_user_certificates` (
  `user_certificate_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `certificate_url` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_certificate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_comments`;
CREATE TABLE `z_user_comments` (
  `user_comment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_educations`;
CREATE TABLE `z_user_educations` (
  `user_education_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_education_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_events`;
CREATE TABLE `z_user_events` (
  `user_event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` enum('concert','festival','competition','workshop','other') DEFAULT 'other',
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_experiences`;
CREATE TABLE `z_user_experiences` (
  `user_experience_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_experience_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_favorites`;
CREATE TABLE `z_user_favorites` (
  `user_favorite_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `item_type` enum('user','academy','branch','course','post','lesson','instrument') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_favorite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_merges`;
CREATE TABLE `z_user_merges` (
  `user_merge_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` bigint(20) unsigned DEFAULT NULL,
  `to_user_id` bigint(20) unsigned DEFAULT NULL,
  `merged_by` bigint(20) unsigned DEFAULT NULL,
  `merged_at` datetime DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_merge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_permission_caches`;
CREATE TABLE `z_user_permission_caches` (
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `permission_name` varchar(100) DEFAULT NULL,
  `source` enum('role','direct') DEFAULT 'role',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_polls`;
CREATE TABLE `z_user_polls` (
  `user_poll_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('single','multiple') DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `status` enum('active','deactive','closed') DEFAULT 'deactive',
  `votes_count` int(11) unsigned DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_poll_options`;
CREATE TABLE `z_user_poll_options` (
  `user_poll_option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned DEFAULT NULL,
  `votes_count` int(11) unsigned DEFAULT 0,
  `sort_order` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_poll_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_poll_votes`;
CREATE TABLE `z_user_poll_votes` (
  `user_poll_vote_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` bigint(20) unsigned DEFAULT NULL,
  `option_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_poll_vote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_profiles`;
CREATE TABLE `z_user_profiles` (
  `user_profile_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `student_level_id` bigint(20) unsigned DEFAULT NULL,
  `start_career_date` date DEFAULT NULL,
  `picture_media_id` bigint(20) unsigned DEFAULT NULL,
  `show_in_public` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25692 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_publications`;
CREATE TABLE `z_user_publications` (
  `user_publication_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `published_date` date DEFAULT NULL,
  `content` text DEFAULT NULL,
  `is_peer_reviewed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_ratings`;
CREATE TABLE `z_user_ratings` (
  `user_rating_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `item_type` enum('user','academy','branch','course','post','lesson','instrument') DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_rating_summaries`;
CREATE TABLE `z_user_rating_summaries` (
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `avg_rating` decimal(3,2) DEFAULT NULL,
  `total_votes` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_relationships`;
CREATE TABLE `z_user_relationships` (
  `user_relationship_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint(20) unsigned DEFAULT NULL,
  `following_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('follow','friend','block','mute') DEFAULT 'follow',
  `status` enum('active','pending','rejected') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_relationship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_reports`;
CREATE TABLE `z_user_reports` (
  `user_report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` bigint(20) unsigned DEFAULT NULL,
  `reported_user_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `item_type` enum('user','academy','post','comment','course') DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_reputations`;
CREATE TABLE `z_user_reputations` (
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `general_score` int(11) DEFAULT 0,
  `professional_score` int(11) DEFAULT 0,
  `social_score` int(11) DEFAULT 0,
  `academy_score` int(11) DEFAULT 0,
  `teaching_score` int(11) DEFAULT 0,
  `student_score` int(11) DEFAULT 0,
  `total_score` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_reputation_logs`;
CREATE TABLE `z_user_reputation_logs` (
  `user_reputation_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `source_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_reputation_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_reviews`;
CREATE TABLE `z_user_reviews` (
  `user_review_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_settings`;
CREATE TABLE `z_user_settings` (
  ` user_setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','bool','int','json') DEFAULT 'string',
  `visibility` enum('private','public') DEFAULT 'private',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (` user_setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_user_specialties`;
CREATE TABLE `z_user_specialties` (
  `user_specialty_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `instrument_id` bigint(20) unsigned DEFAULT NULL,
  `skill_name` varchar(255) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced','master') DEFAULT 'beginner',
  `years_experience` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_world_cities`;
CREATE TABLE `z_world_cities` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state_id` mediumint(8) unsigned NOT NULL,
  `state_code` varchar(255) NOT NULL,
  `country_id` mediumint(8) unsigned NOT NULL,
  `country_code` char(2) NOT NULL,
  `type` varchar(191) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `native` varchar(255) DEFAULT NULL,
  `population` bigint(20) unsigned DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL COMMENT 'IANA timezone identifier (e.g., America/New_York)',
  `translations` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '2014-01-01 12:01:01',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Rapid API GeoDB Cities',
  PRIMARY KEY (`id`),
  KEY `cities_test_ibfk_1` (`state_id`),
  KEY `cities_test_ibfk_2` (`country_id`),
  CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `z_world_states` (`id`),
  CONSTRAINT `cities_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `z_world_countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `z_world_countries`;
CREATE TABLE `z_world_countries` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numeric_code` char(3) DEFAULT NULL,
  `iso2` char(2) DEFAULT NULL,
  `phonecode` varchar(255) DEFAULT NULL,
  `capital` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `currency_name` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(255) DEFAULT NULL,
  `tld` varchar(255) DEFAULT NULL,
  `native` varchar(255) DEFAULT NULL,
  `population` bigint(20) unsigned DEFAULT NULL,
  `gdp` bigint(20) unsigned DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `region_id` mediumint(8) unsigned DEFAULT NULL,
  `subregion` varchar(255) DEFAULT NULL,
  `subregion_id` mediumint(8) unsigned DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `area_sq_km` double DEFAULT NULL,
  `postal_code_format` varchar(255) DEFAULT NULL,
  `postal_code_regex` varchar(255) DEFAULT NULL,
  `timezones` text DEFAULT NULL,
  `translations` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `emoji` varchar(191) DEFAULT NULL,
  `emojiU` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Rapid API GeoDB Cities',
  PRIMARY KEY (`id`),
  KEY `country_continent` (`region_id`),
  KEY `country_subregion` (`subregion_id`),
  CONSTRAINT `country_continent_final` FOREIGN KEY (`region_id`) REFERENCES `z_world_regions` (`id`),
  CONSTRAINT `country_subregion_final` FOREIGN KEY (`subregion_id`) REFERENCES `z_world_subregions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `z_world_iran_cities`;
CREATE TABLE `z_world_iran_cities` (
  `city_id` bigint(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `county_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_world_iran_cities_filtered`;
CREATE TABLE `z_world_iran_cities_filtered` (
  `cities_filtered_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `county_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_world_iran_districts`;
CREATE TABLE `z_world_iran_districts` (
  `district_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_world_iran_rurals`;
CREATE TABLE `z_world_iran_rurals` (
  `rural_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `county_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `z_world_postcodes`;
CREATE TABLE `z_world_postcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL COMMENT 'The postal code value (alphanumeric, country-specific format)',
  `country_id` mediumint(8) unsigned NOT NULL,
  `country_code` char(2) NOT NULL,
  `state_id` mediumint(8) unsigned DEFAULT NULL,
  `state_code` varchar(255) DEFAULT NULL,
  `city_id` mediumint(8) unsigned DEFAULT NULL,
  `locality_name` varchar(255) DEFAULT NULL COMMENT 'Human-readable place name associated with the postcode',
  `type` varchar(32) DEFAULT NULL COMMENT 'Granularity: full | outward | sector | district | area',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `source` varchar(64) DEFAULT NULL COMMENT 'Originating data source for license/attribution tracking (e.g. openplz, wikidata, census)',
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Wikidata Q-ID for cross-referencing',
  `created_at` timestamp NOT NULL DEFAULT '2014-01-01 12:01:01',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_postcodes_code` (`code`),
  KEY `idx_postcodes_country_code` (`country_id`,`code`),
  KEY `idx_postcodes_state` (`state_id`),
  KEY `idx_postcodes_city` (`city_id`),
  CONSTRAINT `postcodes_city_fk` FOREIGN KEY (`city_id`) REFERENCES `z_world_cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `postcodes_country_fk` FOREIGN KEY (`country_id`) REFERENCES `z_world_countries` (`id`),
  CONSTRAINT `postcodes_state_fk` FOREIGN KEY (`state_id`) REFERENCES `z_world_states` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='Postal codes (issue #1039) - Tier 4: one row per postcode';

DROP TABLE IF EXISTS `z_world_regions`;
CREATE TABLE `z_world_regions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `translations` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Rapid API GeoDB Cities',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `z_world_states`;
CREATE TABLE `z_world_states` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `country_id` mediumint(8) unsigned NOT NULL,
  `country_code` char(2) NOT NULL,
  `fips_code` varchar(255) DEFAULT NULL,
  `iso2` varchar(255) DEFAULT NULL,
  `iso3166_2` varchar(10) DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `native` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL COMMENT 'IANA timezone identifier (e.g., America/New_York)',
  `translations` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Rapid API GeoDB Cities',
  `population` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `country_region` (`country_id`),
  CONSTRAINT `country_region_final` FOREIGN KEY (`country_id`) REFERENCES `z_world_countries` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5818 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `z_world_subregions`;
CREATE TABLE `z_world_subregions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `translations` text DEFAULT NULL,
  `region_id` mediumint(8) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `flag` tinyint(1) NOT NULL DEFAULT 1,
  `wikiDataId` varchar(255) DEFAULT NULL COMMENT 'Rapid API GeoDB Cities',
  PRIMARY KEY (`id`),
  KEY `subregion_continent` (`region_id`),
  CONSTRAINT `subregion_continent_final` FOREIGN KEY (`region_id`) REFERENCES `z_world_regions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `academies` (`academy_id`,`user_id`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('7','24','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);

INSERT INTO `academy_branches` (`branch_id`,`academy_id`,`user_id`,`is_main`,`academy_branch_type_id`,`mode`,`timezone`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('7','7','25','1','2','hybrid','Asia/Tehran','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);

INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('22','7','4','11','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('23','7','1','12','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('24','7','2','13','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('25','7','3','14','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('26','7','4','15','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('27','7','1','16','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classrooms` (`classroom_id`,`branch_id`,`type_id`,`capacity`,`is_active`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('28','7','2','17','1','available','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);

INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('64','22','11','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('65','22','3','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('66','22','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('67','23','12','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('68','23','4','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('69','23','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('70','24','13','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('71','24','5','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('72','24','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('73','25','14','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('74','25','6','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('75','25','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('76','26','15','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('77','26','7','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('78','26','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('79','27','4','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('80','27','2','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('81','27','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('82','28','5','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('83','28','3','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_classroom_assets` (`classroom_asset_id`,`classroom_id`,`quantity`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('84','28','1','2026-08-13 23:11:57','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);

INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('152','7','10','1','3','17','finished','2026-08-14 00:26:56','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('153','7','11','4','4','20','pending','2026-08-14 00:26:56','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('154','7','12','7','1','1','open','2026-08-14 00:26:56','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('155','7','13','1','1','1','ongoing','2026-08-14 00:26:56','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('156','7','14','4','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('157','7','15','7','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('158','7','16','1','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('159','7','17','4','1','1','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('160','7','18','7','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('161','7','19','1','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('162','7','20','4','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('163','7','21','7','1','9','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('164','7','22','1','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('165','7','39','4','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('166','7','50','7','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('167','7','61','1','1','1','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('168','7','72','4','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('169','7','7','7','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('170','7','8','1','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('171','7','9','4','1','1','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('172','7','10','7','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('173','7','11','1','1','19','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('174','7','12','4','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('175','7','13','7','1','1','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('176','7','14','1','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('177','7','15','4','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('178','7','16','7','1','1','open','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('179','7','17','1','1','1','ongoing','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('180','7','18','4','1','1','finished','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);
INSERT INTO `academy_branch_courses` (`course_id`,`branch_id`,`lesson_id`,`level_id`,`teacher_capacity`,`student_capacity`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('181','7','19','7','1','1','pending','2026-08-14 00:26:57','1','2026-08-14 09:22:37','1',NULL,NULL,NULL,NULL);

INSERT INTO `academy_branch_members` (`member_id`,`branch_id`,`user_id`,`role_id`,`status`,`joined_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('14','7','8',NULL,'active','2026-08-13','2026-08-13 22:50:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);

INSERT INTO `academy_branch_member_contracts` (`member_contract_id`,`member_id`,`type`,`user_lesson_id`,`start_date`,`end_date`,`price`,`currency_id`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('14','14','manager',NULL,'2026-08-13',NULL,NULL,NULL,'2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);

INSERT INTO `academy_branch_scheduling_rules` (`scheduling_rule_id`,`branch_id`,`rule_type`,`rule_value`,`rule_value_unit`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('22','7','makeup','48.00','hour','active','2026-08-14 12:01:50','25','2026-08-14 12:13:44',NULL,NULL,NULL,NULL,NULL);

INSERT INTO `financial_system_accounts` (`account_id`,`user_id`,`type`,`currency_id`,`balance`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('8','8','student_wallet',NULL,'0.00','active','2026-08-14 09:56:52',NULL,'2026-08-14 09:56:52',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `financial_system_accounts` (`account_id`,`user_id`,`type`,`currency_id`,`balance`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('24','24','academy_main',NULL,'0.00','active','2026-08-14 00:20:12','8','2026-08-14 09:46:21','8',NULL,NULL,NULL,NULL);
INSERT INTO `financial_system_accounts` (`account_id`,`user_id`,`type`,`currency_id`,`balance`,`status`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('25','25','student_wallet',NULL,'0.00','active','2026-08-14 09:56:52',NULL,'2026-08-14 09:56:52',NULL,NULL,NULL,NULL,NULL);

INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('41','61fb24ba-bf3c-46a4-82e9-86b2a5b0fd6b','42','42','2','2026-08-16 03:44:41.152');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('42','fa2b7045-9bef-402a-ab2f-ef0012674a5d','42','42','0','2026-08-16 03:44:55.156');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('43','3ec3070d-dc6b-4d41-9d96-487d085c9f1b','42','42','0','2026-08-16 03:45:10.126');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('44','4dfacc39-8d00-429c-8c6a-041ce99fbb11','42','42','0','2026-08-16 03:45:25.122');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('46','9fc52f12-882a-45b1-9317-04ab953ba690','42','42','0','2026-08-16 03:45:40.124');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('48','678a2073-4867-4cb7-b1ad-3c63b7300782','42','42','0','2026-08-16 03:45:55.120');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('50','5def1cb9-9173-4143-8ee8-f0f2d2e3a643','42','42','5','2026-08-16 03:46:09.365');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('51','d368c95e-7f07-479a-808f-31de00db7a1f','42','42','6','2026-08-16 03:46:11.421');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('52','1cb2f032-2ad0-4077-a30b-569cb1f4402a','42','42','1','2026-08-16 03:46:11.435');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('53','8762515c-4705-4578-bb48-26943846bc42','42','54','2','2026-08-16 03:46:12.882');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('55','fecff122-3ed2-43a9-9c7d-dd29183052cd','42','54','1','2026-08-16 03:46:22.178');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('56','a023f113-a8af-422c-8cf6-5b48e533f037','42','54','2','2026-08-16 03:46:22.187');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('57','b3431ced-1675-4d16-ba95-ab7f24d2edb1','42','58','2','2026-08-16 03:46:24.444');
INSERT INTO `tracking_ingestion_batches` (`tracking_ingestion_batch_id`,`batch_uuid`,`tracking_user_session_id`,`page_view_id`,`events_count`,`created_at`) VALUES ('59','001cfca0-24b7-40d9-a419-7fe196945306','42','58','4','2026-08-16 03:46:37.835');

INSERT INTO `tracking_user_activity_intervals` (`tracking_user_activity_interval_id`,`interval_uuid`,`tracking_user_session_id`,`page_view_id`,`user_id`,`activity_type`,`started_at`,`ended_at`,`duration_ms`,`section_key`,`created_at`) VALUES ('20','58e15744-37fd-4546-bc1f-0532fbabe6a9','42','42','24','active','2026-08-16 00:16:03.323','2026-08-16 00:16:11.394','8071','','2026-08-16 03:46:11.423');
INSERT INTO `tracking_user_activity_intervals` (`tracking_user_activity_interval_id`,`interval_uuid`,`tracking_user_session_id`,`page_view_id`,`user_id`,`activity_type`,`started_at`,`ended_at`,`duration_ms`,`section_key`,`created_at`) VALUES ('21','8f8ff65b-adf5-431c-97db-85c44f4f948f','42','54','24','active','2026-08-16 00:16:11.596','2026-08-16 00:16:16.620','5024','','2026-08-16 03:46:22.188');
INSERT INTO `tracking_user_activity_intervals` (`tracking_user_activity_interval_id`,`interval_uuid`,`tracking_user_session_id`,`page_view_id`,`user_id`,`activity_type`,`started_at`,`ended_at`,`duration_ms`,`section_key`,`created_at`) VALUES ('22','446a08cb-46d3-48e9-abb4-d04704ed44d6','42','54','24','reading','2026-08-16 00:16:16.620','2026-08-16 00:16:22.152','5532','','2026-08-16 03:46:22.188');
INSERT INTO `tracking_user_activity_intervals` (`tracking_user_activity_interval_id`,`interval_uuid`,`tracking_user_session_id`,`page_view_id`,`user_id`,`activity_type`,`started_at`,`ended_at`,`duration_ms`,`section_key`,`created_at`) VALUES ('23','200a1d01-08d2-4ac0-8aee-0cc421d51a9a','42','58','24','active','2026-08-16 00:16:22.764','2026-08-16 00:16:35.774','13010','account','2026-08-16 03:46:37.836');

INSERT INTO `tracking_user_content_engagements` (`tracking_user_content_engagement_id`,`tracking_user_session_id`,`page_view_id`,`user_id`,`section_key`,`section_type`,`entity_type`,`entity_id`,`impression_count`,`visible_duration_ms`,`active_duration_ms`,`idle_duration_ms`,`reading_duration_ms`,`max_visibility_percent`,`interaction_count`,`click_count`,`first_seen_at`,`last_seen_at`,`created_at`,`updated_at`) VALUES ('9','42','58','24','account','div','',NULL,'1','8031','5000','0','3031','25','0','0','2026-08-16 00:16:30.724','2026-08-16 00:16:37.805','2026-08-16 03:46:37.836',NULL);

INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('43','e6a22829-814a-4a6d-b50f-53d3fc4c28f8','42','24','42','8','click','behavior','click','','2026-08-16 00:16:10.692','2026-08-16 03:46:11.421','1786839370692','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','label','','','مرا به خاطر بسپار','',NULL,'','888','480','888','480','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('44','407bf03b-5574-435c-8a75-c45b4fe83925','42','24','42','9','field_focus','behavior','field_focus','','2026-08-16 00:16:10.692','2026-08-16 03:46:11.422','1786839370692','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','input','','remember','1','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('45','8ade53b1-6710-457c-87a2-838b5ff40b54','42','24','42','10','click','behavior','click','','2026-08-16 00:16:10.692','2026-08-16 03:46:11.422','1786839370692','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','input','','remember','1','',NULL,'','888','480','888','480','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('46','fd5d5ecf-aadd-4b4c-8bac-87db85d66486','42','24','42','11','click','behavior','click','','2026-08-16 00:16:11.180','2026-08-16 03:46:11.422','1786839371180','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','button','','','ورود','',NULL,'','844','524','844','524','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('47','6513976c-c790-4a8e-a6f2-665d7299a95c','42','24','42','12','form_submit','behavior','form_submit','','2026-08-16 00:16:11.180','2026-08-16 03:46:11.422','1786839371180','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','form','loginForm','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('48','076ff396-0d36-4328-976c-338d06f4ea75','42','24','42','13','page_view_end','behavior','page_view_end','','2026-08-16 00:16:11.394','2026-08-16 03:46:11.423','1786839371394','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('49','736c2e43-9d14-434f-a69f-c899517707af','42','24','42','14','page_hidden','behavior','page_hidden','','2026-08-16 00:16:11.394','2026-08-16 03:46:11.435','1786839371394','[]','http://sornaz.local/system/login','2026-08-16 03:46:11','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('50','daf5e7cb-8689-4fed-a846-9504e0d8bcd0','42','24','54','15','session_start','behavior','session_start','','2026-08-16 00:16:11.614','2026-08-16 03:46:12.882','1786839371614','[]','http://sornaz.local/page/home','2026-08-16 03:46:12','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','34',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('51','2ad04c79-a070-45dc-bdba-902e91aedac3','42','24','54','16','page_view_start','behavior','page_view_start','','2026-08-16 00:16:11.614','2026-08-16 03:46:12.883','1786839371614','[]','http://sornaz.local/page/home','2026-08-16 03:46:12','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','34',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('52','351191a1-ee14-4884-b2b5-bc0864af39bd','42','24','54','19','page_hidden','behavior','page_hidden','','2026-08-16 00:16:22.153','2026-08-16 03:46:22.178','1786839382153','[]','http://sornaz.local/page/home','2026-08-16 03:46:22','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','35',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('53','988e94a0-eb01-4ec8-b431-529f07d20507','42','24','54','17','click','behavior','click','','2026-08-16 00:16:21.844','2026-08-16 03:46:22.188','1786839381844','[]','http://sornaz.local/page/home','2026-08-16 03:46:22','1','a','','','پنل آموزشگاه','',NULL,'','467','31','467','31','0','0','35',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('54','b414e6fe-2a1d-4f5f-b85b-4ea006754726','42','24','54','18','page_view_end','behavior','page_view_end','','2026-08-16 00:16:22.153','2026-08-16 03:46:22.188','1786839382153','[]','http://sornaz.local/page/home','2026-08-16 03:46:22','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','35',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('55','758d6d96-17d8-4842-a90f-4d44a95fab9b','42','24','58','20','session_start','behavior','session_start','','2026-08-16 00:16:22.768','2026-08-16 03:46:24.445','1786839382768','[]','http://sornaz.local/analytics/admin-panel','2026-08-16 03:46:24','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('56','9e492a8a-21f9-4f37-9632-e2a25b7ac8c9','42','24','58','21','page_view_start','behavior','page_view_start','','2026-08-16 00:16:22.768','2026-08-16 03:46:24.447','1786839382768','[]','http://sornaz.local/analytics/admin-panel','2026-08-16 03:46:24','1','','','','','',NULL,'',NULL,NULL,NULL,NULL,'0','0','100',NULL,NULL,NULL,'client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('57','e1bea986-0447-4b27-8e13-0f89c294a867','42','24','58','22','click','behavior','click','','2026-08-16 00:16:24.485','2026-08-16 03:46:37.835','1786839384485','[]','http://sornaz.local/analytics/admin-panel','2026-08-16 03:46:37','1','','','','','',NULL,'','1378','168','1378','168','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('58','66272e77-843f-49e8-aafa-587f5ae3615a','42','24','58','23','click','behavior','click','','2026-08-16 00:16:28.068','2026-08-16 03:46:37.835','1786839388068','[]','http://sornaz.local/analytics/admin-panel','2026-08-16 03:46:37','1','button','','','تأیید','',NULL,'','742','536','742','536','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('59','323ae346-56b3-43da-a546-fcc16a6d563a','42','24','58','24','click','behavior','click','','2026-08-16 00:16:30.500','2026-08-16 03:46:37.835','1786839390500','[]','http://sornaz.local/analytics/admin-panel','2026-08-16 03:46:37','1','a','','','حساب کاربری','',NULL,'','1382','164','1382','164','0','0','100',NULL,NULL,'1','client');
INSERT INTO `tracking_user_events` (`tracking_user_event_id`,`event_uuid`,`tracking_user_session_id`,`user_id`,`page_view_id`,`sequence_number`,`event_name`,`event_category`,`event_action`,`event_label`,`occurred_at`,`received_at`,`client_timestamp_ms`,`event_data`,`page_url`,`created_at`,`event_version`,`target_type`,`target_id`,`target_name`,`target_text`,`entity_type`,`entity_id`,`section_key`,`position_x`,`position_y`,`viewport_x`,`viewport_y`,`scroll_x`,`scroll_y`,`scroll_depth`,`duration_ms`,`numeric_value`,`is_trusted`,`source`) VALUES ('60','cfb43be8-d1da-49cd-9bd3-dc6dc165033f','42','24','58','25','section_enter','behavior','section_enter','','2026-08-16 00:16:30.724','2026-08-16 03:46:37.836','1786839390724','[]','http://sornaz.local/analytics/admin-panel#','2026-08-16 03:46:37','1','','','','','',NULL,'account',NULL,NULL,NULL,NULL,'0','0','100',NULL,'25.0000',NULL,'client');

INSERT INTO `tracking_user_page_views` (`tracking_user_page_view_id`,`page_view_uuid`,`tracking_user_session_id`,`tab_id`,`sequence_number`,`user_id`,`page_url`,`page_path`,`canonical_url`,`page_title`,`route_name`,`page_type`,`entity_type`,`entity_id`,`locale`,`entered_at`,`exited_at`,`duration`,`referrer`,`referrer_domain`,`query_params`,`utm_source`,`utm_medium`,`utm_campaign`,`scroll_depth`,`click_count`,`mouse_movement`,`is_exit_page`,`created_at`,`last_activity_at`,`last_heartbeat_at`,`total_duration_ms`,`visible_duration_ms`,`hidden_duration_ms`,`active_duration_ms`,`idle_duration_ms`,`reading_duration_ms`,`interaction_count`,`keypress_count`,`form_interaction_count`,`max_scroll_depth`,`max_scroll_y`,`content_height`,`first_interaction_at`,`last_interaction_at`,`exit_reason`,`is_completed`) VALUES ('42','90457938-f65d-4525-916c-d5400269ad1d','42','c3830988f6ca46849a9f255b4dbc980d','1','24','http://sornaz.local/system/login','/system/login','http://sornaz.local/system/login','','','other',NULL,NULL,'fa','2026-08-16 00:14:39',NULL,'92','','','[]','','','','100','5',NULL,'0','2026-08-16 03:44:41',NULL,'2026-08-16 02:16:11.000','92083','15309','65776','9078','6231','0','20','11','4','100','0','910','2026-08-16 00:16:03.052','2026-08-16 00:16:11.180','','0');
INSERT INTO `tracking_user_page_views` (`tracking_user_page_view_id`,`page_view_uuid`,`tracking_user_session_id`,`tab_id`,`sequence_number`,`user_id`,`page_url`,`page_path`,`canonical_url`,`page_title`,`route_name`,`page_type`,`entity_type`,`entity_id`,`locale`,`entered_at`,`exited_at`,`duration`,`referrer`,`referrer_domain`,`query_params`,`utm_source`,`utm_medium`,`utm_campaign`,`scroll_depth`,`click_count`,`mouse_movement`,`is_exit_page`,`created_at`,`last_activity_at`,`last_heartbeat_at`,`total_duration_ms`,`visible_duration_ms`,`hidden_duration_ms`,`active_duration_ms`,`idle_duration_ms`,`reading_duration_ms`,`interaction_count`,`keypress_count`,`form_interaction_count`,`max_scroll_depth`,`max_scroll_y`,`content_height`,`first_interaction_at`,`last_interaction_at`,`exit_reason`,`is_completed`) VALUES ('54','a8c5ab75-5ca5-4230-9dbc-11f8ef3ae29b','42','c3830988f6ca46849a9f255b4dbc980d','2','24','http://sornaz.local/page/home','/page/home','http://sornaz.local/page/home','','','home',NULL,NULL,'fa','2026-08-16 00:16:11','2026-08-16 00:16:22','10','http://sornaz.local/system/login','sornaz.local','[]','','','','35','1',NULL,'1','2026-08-16 03:46:12',NULL,'2026-08-16 02:16:22.000','10557','10557','0','4552','0','6005','1','0','0','35','0','2601','2026-08-16 00:16:21.844','2026-08-16 00:16:21.844','pagehide','1');
INSERT INTO `tracking_user_page_views` (`tracking_user_page_view_id`,`page_view_uuid`,`tracking_user_session_id`,`tab_id`,`sequence_number`,`user_id`,`page_url`,`page_path`,`canonical_url`,`page_title`,`route_name`,`page_type`,`entity_type`,`entity_id`,`locale`,`entered_at`,`exited_at`,`duration`,`referrer`,`referrer_domain`,`query_params`,`utm_source`,`utm_medium`,`utm_campaign`,`scroll_depth`,`click_count`,`mouse_movement`,`is_exit_page`,`created_at`,`last_activity_at`,`last_heartbeat_at`,`total_duration_ms`,`visible_duration_ms`,`hidden_duration_ms`,`active_duration_ms`,`idle_duration_ms`,`reading_duration_ms`,`interaction_count`,`keypress_count`,`form_interaction_count`,`max_scroll_depth`,`max_scroll_y`,`content_height`,`first_interaction_at`,`last_interaction_at`,`exit_reason`,`is_completed`) VALUES ('58','fcf72d62-1f76-4c15-9b03-0a82128c7aad','42','c3830988f6ca46849a9f255b4dbc980d','3','24','http://sornaz.local/analytics/admin-panel','/analytics/admin-panel','http://sornaz.local/analytics/admin-panel','','','dashboard',NULL,NULL,'fa','2026-08-16 00:16:22',NULL,'15','http://sornaz.local/page/home','sornaz.local','[]','','','','100','3',NULL,'0','2026-08-16 03:46:24',NULL,'2026-08-16 02:16:37.000','15041','15041','0','12010','0','3031','3','0','0','100','0','910','2026-08-16 00:16:24.485','2026-08-16 00:16:30.500','','0');

INSERT INTO `tracking_user_sessions` (`tracking_user_session_id`,`visit_uuid`,`user_id`,`session_id`,`authenticated_session_id`,`guest_id`,`status`,`ip_address`,`user_agent`,`device_type`,`os`,`browser`,`browser_version`,`country_code`,`entry_page`,`exit_page`,`total_duration`,`page_views_count`,`started_at`,`last_activity_at`,`last_heartbeat_at`,`ended_at`,`end_reason`,`auth_revoked_at`,`auth_revoked_by`,`is_bounced`,`is_bot`,`created_at`,`updated_at`,`active_duration_ms`,`idle_duration_ms`,`visible_duration_ms`,`hidden_duration_ms`,`events_count`,`engagement_score`,`language`,`timezone`,`screen_width`,`screen_height`,`viewport_width`,`viewport_height`,`device_pixel_ratio`,`platform`,`app_version`,`tracking_version`,`consent_status`,`city_name`,`region_name`) VALUES ('42','0b45cf99-5108-4111-ab8f-d6e771449077','24','89mv28jnbb7l0ehv05an3s85k9',NULL,'fd2f1aef-15c5-4b50-8090-430d0ddcf677','active','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','desktop','Windows','Chrome','151.0.0.0',NULL,'http://sornaz.local/system/login','http://sornaz.local/analytics/admin-panel#','92','3','2026-08-16 00:14:39','2026-08-16 00:16:30.500','2026-08-16 02:16:37.000',NULL,NULL,NULL,NULL,'0','0','2026-08-16 03:44:41','2026-08-16 03:46:37','12010','6231','15309','65776','25',NULL,'fa','Asia/Tehran','1920','1080','1460','910','1.00','Win32','web-1','1','unknown',NULL,NULL);

INSERT INTO `users` (`user_id`,`email`,`phone`,`phone_verified_at`,`last_login_at`,`last_login_ip`,`username`,`password`,`national_code`,`gender`,`type`,`status`,`locale`,`timezone`,`avatar_file_id`,`visibility`,`birthday`,`register_time`,`register_method`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('8','sornaz.academy.manager07@gmail.com','09911000007','2025-04-15 05:50:11','2025-04-24 22:50:11','185.51.26.16','test_academy_manager_07','$2y$10$E1IwEDIPN1zjokieepqtaO.wLKfFk5SVQgnmEL/1FMTCa/TkjOCkm','7300000071','male','human','pending','fa','Asia/Tehran','67','public','1985-05-03','2025-04-14 22:50:11','phone','2025-04-14 22:50:11','8','2026-08-14 00:20:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `users` (`user_id`,`email`,`phone`,`phone_verified_at`,`last_login_at`,`last_login_ip`,`username`,`password`,`national_code`,`gender`,`type`,`status`,`locale`,`timezone`,`avatar_file_id`,`visibility`,`birthday`,`register_time`,`register_method`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('24','academy07@sornaz.test','09121000007',NULL,NULL,NULL,'test_academy_07','$2y$10$gBym4QXMSY/iDaxOE5Fs1OsJGKi38crVKLf3EKaTfgkgKuGzyxSiu',NULL,'other','academy','approved','fa','Asia/Tehran','127','unlisted','1376-07-07','2026-08-14 00:20:12','email','2026-08-14 00:20:12','8','2026-08-14 00:20:16','8',NULL,NULL,NULL,NULL);
INSERT INTO `users` (`user_id`,`email`,`phone`,`phone_verified_at`,`last_login_at`,`last_login_ip`,`username`,`password`,`national_code`,`gender`,`type`,`status`,`locale`,`timezone`,`avatar_file_id`,`visibility`,`birthday`,`register_time`,`register_method`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('25','branch031@sornaz.test','09353000031',NULL,NULL,NULL,'test_main_branch_07','$2y$10$u6QlHngH0QGs/sTyJeAugOiqfzOJAEEP1.87Ool677w4yG0yssdwi',NULL,'other','branch','approved','fa','Asia/Tehran','67','unlisted','1376-07-07','2026-08-14 00:20:12','email','2026-08-14 00:20:12','8','2026-08-14 00:20:20','8',NULL,NULL,NULL,NULL);

INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('13','8','0','113','1130004','1','34.7988575','48.5146239','6516738695','2025-04-16 04:50:11','8','2025-04-16 09:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('26','25','1','130','1300006','1','35.6100000','51.2100000','1400000610','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);
INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('42','24','0','113','1130004','1','34.7988575','48.5146239','6516738695','2026-08-15 04:20:12','24','2026-08-15 09:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('43','24','0','106','1060003','0','31.3282914','48.6706183','6135714387','2026-08-16 04:20:12','24','2026-08-16 14:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('62','25','0','113','1130004','1','34.7988575','48.5146239','6516738695','2026-08-15 10:20:12','25','2026-08-15 15:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_addresses` (`address_id`,`user_id`,`country_id`,`province_id`,`county_id`,`is_main`,`latitude`,`longitude`,`postal_code`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('63','25','0','106','1060003','0','31.3282914','48.6706183','6135714387','2026-08-16 10:20:12','25','2026-08-16 20:20:12','25',NULL,NULL,NULL,NULL);

INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('457','8',NULL,'saturday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('458','8',NULL,'sunday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('459','8',NULL,'sunday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('460','8',NULL,'monday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('461','8',NULL,'monday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('462','8',NULL,'monday','16:00:00','19:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('463','8',NULL,'tuesday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('464','8',NULL,'wednesday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('465','8',NULL,'wednesday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('466','8',NULL,'thursday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('467','8',NULL,'thursday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('468','8',NULL,'thursday','17:00:00','20:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('469','8',NULL,'friday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('470','8','2026-09-19',NULL,'16:00:00','19:00:00','Asia/Tehran','available','0','none','0','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('606','24',NULL,'saturday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('607','24',NULL,'saturday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('608','24',NULL,'sunday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('609','24',NULL,'sunday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('610','24',NULL,'sunday','17:00:00','20:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('611','24',NULL,'monday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('612','24',NULL,'tuesday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('613','24',NULL,'tuesday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('614','24',NULL,'wednesday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('615','24',NULL,'wednesday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('616','24',NULL,'wednesday','16:00:00','19:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('617','24',NULL,'thursday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('618','24',NULL,'friday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('619','24',NULL,'friday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('620','24','2026-10-22',NULL,'16:00:00','19:00:00','Asia/Tehran','available','0','none','0','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('192','25',NULL,'saturday','08:30:00','21:30:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('193','25',NULL,'sunday','08:30:00','21:30:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('194','25',NULL,'monday','08:30:00','21:30:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('195','25',NULL,'tuesday','08:30:00','21:30:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'4');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('196','25',NULL,'wednesday','08:30:00','21:30:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'5');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('197','25',NULL,'thursday','09:00:00','17:00:00','Asia/Tehran','available','1','week','0','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'6');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('198','25',NULL,'friday','00:00:00','23:59:00','Asia/Tehran','unavailable','1','week','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL,'7');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('756','25',NULL,'saturday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('757','25',NULL,'saturday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('758','25',NULL,'sunday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('759','25',NULL,'sunday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('760','25',NULL,'sunday','17:00:00','20:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('761','25',NULL,'monday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('762','25',NULL,'tuesday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('763','25',NULL,'tuesday','13:00:00','16:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('764','25',NULL,'wednesday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('765','25',NULL,'wednesday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('766','25',NULL,'wednesday','16:00:00','19:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'3');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('767','25',NULL,'thursday','09:00:00','12:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('768','25',NULL,'friday','08:00:00','11:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('769','25',NULL,'friday','12:00:00','15:00:00','Asia/Tehran','available','1','week','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'2');
INSERT INTO `user_availabilities` (`user_availability_id`,`user_id`,`date`,`day_of_week`,`start_time`,`end_time`,`timezone`,`type`,`is_repeating`,`repeat_period`,`is_closed`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`,`priority`) VALUES ('770','25','2026-10-22',NULL,'16:00:00','19:00:00','Asia/Tehran','available','0','none','0','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL,'1');

INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('19','8','2026-09-04',NULL,NULL,'busy','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('20','8','2026-10-11','11:00:00','14:00:00','holiday','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('48','24','2026-10-27',NULL,NULL,'vacation','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('49','24','2026-11-07','15:00:00','18:00:00','unavailable','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('50','24','2026-09-14','10:00:00','13:00:00','busy','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('78','25','2026-10-18',NULL,NULL,'busy','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('79','25','2026-11-25','15:00:00','18:00:00','holiday','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_availability_exceptions` (`user_availability_exception_id`,`user_id`,`date`,`start_time`,`end_time`,`type`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('80','25','2026-09-05','10:00:00','13:00:00','vacation','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);

INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('37','8','phone','other','primary','1','active','2025-04-16 06:50:11','2025-04-15 05:50:11','8','2025-04-16 07:50:11','8','2025-04-15 06:50:11','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('38','8','email','other','primary','1','active','2025-04-17 13:50:11','2025-04-15 11:50:11','8','2025-04-17 15:50:11','8','2025-04-15 13:50:11','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('39','8','email','other','ledger','0','active','2025-04-18 20:50:11','2025-04-15 17:50:11','8','2025-04-18 23:50:11','8','2025-04-15 20:50:11','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('40','8','social','instagram','primary','1','active','2025-04-20 03:50:11','2025-04-15 23:50:11','8','2025-04-20 07:50:11','8','2025-04-16 03:50:11','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('41','8','social','telegram','support','0','active','2025-04-21 10:50:11','2025-04-16 05:50:11','8','2025-04-21 15:50:11','8','2025-04-16 10:50:11','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('81','25','phone','other','primary','1','active',NULL,'2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('82','25','social','instagram','secondary','0','active',NULL,'2026-08-14 00:20:12','8','2026-08-14 00:20:12','8','2026-08-13 22:50:12','8',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('130','24','phone','other','primary','1','active','2026-08-15 08:20:12','2026-08-14 07:20:12','24','2026-08-15 09:20:12','24','2026-08-14 08:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('131','24','email','other','primary','1','active','2026-08-16 15:20:12','2026-08-14 13:20:12','24','2026-08-16 17:20:12','24','2026-08-14 15:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('132','24','email','other','ledger','0','active','2026-08-17 22:20:12','2026-08-14 19:20:12','24','2026-08-18 01:20:12','24','2026-08-14 22:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('133','24','social','instagram','primary','1','active','2026-08-19 05:20:12','2026-08-15 01:20:12','24','2026-08-19 09:20:12','24','2026-08-15 05:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('134','24','social','telegram','support','0','active','2026-08-20 12:20:12','2026-08-15 07:20:12','24','2026-08-20 17:20:12','24','2026-08-15 12:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('135','24','social','website','secondary','0','active','2026-08-21 19:20:12','2026-08-15 13:20:12','24','2026-08-22 01:20:12','24','2026-08-15 19:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('136','24','social','whats-app','support','0','active','2026-08-23 02:20:12','2026-08-15 19:20:12','24','2026-08-23 03:20:12','24','2026-08-16 02:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('137','24','social','linkedin','other','0','inactive','2026-08-24 09:20:12','2026-08-16 01:20:12','24','2026-08-24 11:20:12','24','2026-08-16 09:20:12','24',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('185','25','phone','other','primary','1','active','2026-08-15 08:20:12','2026-08-14 07:20:12','25','2026-08-15 09:20:12','25','2026-08-14 08:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('186','25','email','other','primary','1','active','2026-08-16 15:20:12','2026-08-14 13:20:12','25','2026-08-16 17:20:12','25','2026-08-14 15:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('187','25','email','other','ledger','0','active','2026-08-17 22:20:12','2026-08-14 19:20:12','25','2026-08-18 01:20:12','25','2026-08-14 22:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('188','25','social','instagram','primary','1','active','2026-08-19 05:20:12','2026-08-15 01:20:12','25','2026-08-19 09:20:12','25','2026-08-15 05:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('189','25','social','telegram','support','0','active','2026-08-20 12:20:12','2026-08-15 07:20:12','25','2026-08-20 17:20:12','25','2026-08-15 12:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('190','25','social','website','secondary','0','active','2026-08-21 19:20:12','2026-08-15 13:20:12','25','2026-08-22 01:20:12','25','2026-08-15 19:20:12','25',NULL,NULL);
INSERT INTO `user_contacts` (`user_contact_id`,`user_id`,`mode`,`platform`,`priority`,`is_main`,`status`,`last_called_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('191','25','social','whats-app','support','0','active','2026-08-23 02:20:12','2026-08-15 19:20:12','25','2026-08-23 03:20:12','25','2026-08-16 02:20:12','25',NULL,NULL);

INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11530','24','3','8','1392-05-19','1','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11531','24','14','9','1394-08-28','0','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11532','24','25','1','1396-11-05','0','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11533','24','36','2','1398-02-09','0','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11551','25','28','8','1402-11-21','1','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11552','25','39','9','1384-02-11','0','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11553','25','50','1','1386-05-18','0','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11554','25','6','2','1388-08-12','0','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11630','25','7','7','1396-03-08','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11631','25','8','8','1397-04-09','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11632','25','9','9','1398-05-10','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11633','25','10','1','1399-06-11','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11634','25','11','2','1400-07-12','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11635','25','12','3','1401-08-13','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11636','25','13','4','1402-09-14','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11637','25','14','5','1403-10-15','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11638','25','15','6','1404-11-16','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11639','25','16','7','1390-12-17','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11640','25','17','8','1391-01-18','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11641','25','18','9','1392-02-19','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11642','25','19','1','1393-03-20','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11643','25','20','2','1394-04-21','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11644','25','21','3','1395-05-22','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_instruments` (`user_instrument_id`,`user_id`,`instrument_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('11645','25','22','4','1396-06-23','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);

INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12029','8','43','7','1402-07-01','1','2025-04-15 00:50:11','8','2025-04-15 00:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12030','8','54','8','1384-10-08','0','2025-04-15 02:50:11','8','2025-04-15 02:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12052','24','25','8','1392-05-19','0','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12053','24','36','9','1394-08-28','0','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12054','24','47','1','1396-11-05','0','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12055','24','58','2','1398-02-09','0','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12074','25','39','8','1402-11-21','0','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12075','25','50','9','1384-02-11','0','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12076','25','61','1','1386-05-18','0','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12077','25','72','2','1388-08-12','0','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12156','25','7','7','1396-03-08','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12157','25','8','8','1397-04-09','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12158','25','9','9','1398-05-10','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12159','25','10','1','1399-06-11','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12160','25','11','2','1400-07-12','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12161','25','12','3','1401-08-13','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12162','25','13','4','1402-09-14','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12163','25','14','5','1403-10-15','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12164','25','15','6','1404-11-16','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12165','25','16','7','1390-12-17','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12166','25','17','8','1391-01-18','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12167','25','18','9','1392-02-19','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12168','25','19','1','1393-03-20','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12169','25','20','2','1394-04-21','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12170','25','21','3','1395-05-22','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `user_lessons` (`user_lesson_id`,`user_id`,`lesson_id`,`level_id`,`start_date`,`is_primary`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12171','25','22','4','1396-06-23','0','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);

INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('32','25','database_insert','ایجاد اطلاعات در academy_branch_scheduling_rules','یک عملیات ایجاد به‌صورت سیستمی در جدول academy_branch_scheduling_rules ثبت شد. فیلدهای «branch_id، title، rule_type، rule_value، status، summary» تغییر کردند.','academy_branch_scheduling_rules','22','0','2026-08-14 12:01:50','25','2026-08-14 12:01:50',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2003','8','database_insert','ایجاد اطلاعات در user_referrals','یک عملیات ایجاد به‌صورت سیستمی در جدول user_referrals ثبت شد. فیلدهای «user_id، invite_code، referred_by_user_id، status، created_by، updated_by» تغییر کردند.','user_referrals','8','0','2026-08-16 00:50:37','8','2026-08-16 00:50:37',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2019','24','database_insert','ایجاد اطلاعات در user_referrals','یک عملیات ایجاد به‌صورت سیستمی در جدول user_referrals ثبت شد. فیلدهای «user_id، invite_code، referred_by_user_id، status، created_by، updated_by» تغییر کردند.','user_referrals','24','0','2026-08-16 00:50:38','24','2026-08-16 00:50:38',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2020','25','database_insert','ایجاد اطلاعات در user_referrals','یک عملیات ایجاد به‌صورت سیستمی در جدول user_referrals ثبت شد. فیلدهای «user_id، invite_code، referred_by_user_id، status، created_by، updated_by» تغییر کردند.','user_referrals','25','0','2026-08-16 00:50:38','25','2026-08-16 00:50:38',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2439','24','database_update','ویرایش اطلاعات در academy_branch_types','یک عملیات ویرایش به‌صورت سیستمی در جدول academy_branch_types ثبت شد. فیلدهای «updated_by، deleted_at، deleted_by» تغییر کردند.','academy_branch_types','1','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2440','24','database_update','ویرایش اطلاعات در translations','یک عملیات ویرایش به‌صورت سیستمی در جدول translations ثبت شد. فیلدهای «created_by، updated_by، deleted_at، deleted_by» تغییر کردند.','translations','1','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2441','24','database_update','ویرایش اطلاعات در translations','یک عملیات ویرایش به‌صورت سیستمی در جدول translations ثبت شد. فیلدهای «created_by، updated_by، deleted_at، deleted_by» تغییر کردند.','translations','1','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2442','24','database_update','ویرایش اطلاعات در academy_branch_types','یک عملیات ویرایش به‌صورت سیستمی در جدول academy_branch_types ثبت شد. فیلدهای «updated_by، deleted_at، deleted_by» تغییر کردند.','academy_branch_types','2','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2443','24','database_update','ویرایش اطلاعات در translations','یک عملیات ویرایش به‌صورت سیستمی در جدول translations ثبت شد. فیلدهای «created_by، updated_by، deleted_at، deleted_by» تغییر کردند.','translations','2','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `user_notifications` (`user_notification_id`,`user_id`,`type`,`title`,`message`,`entity_type`,`entity_id`,`is_read`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('2444','24','database_update','ویرایش اطلاعات در translations','یک عملیات ویرایش به‌صورت سیستمی در جدول translations ثبت شد. فیلدهای «created_by، updated_by، deleted_at، deleted_by» تغییر کردند.','translations','2','0','2026-08-16 03:46:23','24','2026-08-16 03:46:23',NULL,NULL,NULL,NULL,NULL);

INSERT INTO `user_referrals` (`user_referral_id`,`user_id`,`invite_code`,`referred_by_user_id`,`status`,`converted_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) VALUES ('8','8','97174F76D023',NULL,'active',NULL,'2026-08-16 00:50:37','8','2026-08-16 00:50:37','8',NULL,NULL);
INSERT INTO `user_referrals` (`user_referral_id`,`user_id`,`invite_code`,`referred_by_user_id`,`status`,`converted_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) VALUES ('24','24','6AEAFDA922A6',NULL,'active',NULL,'2026-08-16 00:50:38','24','2026-08-16 00:50:38','24',NULL,NULL);
INSERT INTO `user_referrals` (`user_referral_id`,`user_id`,`invite_code`,`referred_by_user_id`,`status`,`converted_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`deleted_at`,`deleted_by`) VALUES ('25','25','A8C618EC3236',NULL,'active',NULL,'2026-08-16 00:50:38','25','2026-08-16 00:50:38','25',NULL,NULL);

INSERT INTO `user_roles` (`user_role_id`,`user_id`,`role_id`,`is_main`,`granted_by`,`granted_at`,`expires_at`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('12','8','9',NULL,NULL,'2026-06-25 17:13:50',NULL,'2024-07-05 10:30:00',NULL,'2026-06-14 15:58:45',NULL,NULL,NULL,NULL,NULL);

INSERT INTO `z_sor_contacts` (`contact_id`,`user_id`,`post_id`,`author`,`author_email`,`author_url`,`author_ip`,`date`,`content`,`has_response`,`approved`,`agent`,`type`,`parent`,`receiver_user_id`) VALUES ('64','8','1','علی حقیقی','ali.haghighi@gmail.com',NULL,NULL,'2026-03-09 22:15:40','سلام دوست گرامی\r\nبرای ثبت آموزشگاه ارغنون شهرستان زرقان در سیستم جامع مدیریت آموزشگاه برنامه سرناز نیاز به ثبت اطلاعات مربوطه در فرمی هست که به زودی در وبسایت قرار داده می شود\r\nفعلا این فرم در دست تکمیل هست و کامل نشده است.',NULL,NULL,NULL,'contact','0','3');

INSERT INTO `z_user_profiles` (`user_profile_id`,`user_id`,`student_level_id`,`start_career_date`,`picture_media_id`,`show_in_public`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('9','8',NULL,NULL,NULL,'1','2026-08-10 08:47:03',NULL,'2026-08-10 08:47:03',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `z_user_profiles` (`user_profile_id`,`user_id`,`student_level_id`,`start_career_date`,`picture_media_id`,`show_in_public`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('20521','24',NULL,NULL,NULL,'1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `z_user_profiles` (`user_profile_id`,`user_id`,`student_level_id`,`start_career_date`,`picture_media_id`,`show_in_public`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('20522','25',NULL,NULL,NULL,'1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);

INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218175','academies','7','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218174','academies','7','fa',NULL,'description','آموزشگاه مهرآهنگ با بهره‌گیری از استادان باتجربه، دوره‌های مقدماتی تا پیشرفته پیانو و موسیقی کلاسیک را در فضایی حرفه‌ای برگزار می‌کند. برنامه آموزشی این مجموعه بر تمرین هدفمند، اجرای هنرجویی و رشد خلاقیت موسیقایی استوار است.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218173','academies','7','en',NULL,'short_description','A sample music academy offering structured courses for learners of different ages and levels.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218172','academies','7','fa',NULL,'short_description','آموزشگاه مهرآهنگ در کرمان؛ مرکز تخصصی پیانو و موسیقی کلاسیک برای هنرجویان کودک، نوجوان و بزرگسال.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218171','academies','7','en',NULL,'slogan','Discover your musical voice','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218170','academies','7','fa',NULL,'slogan','آغاز مسیر حرفه‌ای موسیقی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218169','academies','7','en',NULL,'title','Sornaz Music Academy 7','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218168','academies','7','fa',NULL,'title','مهرآهنگ','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218183','academy_branches','7','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218182','academy_branches','7','fa',NULL,'description','شعبه مرکزی مهرآهنگ با فضای آموزشی مجهز و برنامه منظم کلاس‌ها.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218185','academy_branches','7','en',NULL,'manager','Academy Manager','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218184','academy_branches','7','fa',NULL,'manager','حسین محمدی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218179','academy_branches','7','en',NULL,'name','Sornaz Music Academy 7','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218178','academy_branches','7','fa',NULL,'name','مهرآهنگ - شعبه مرکزی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218181','academy_branches','7','en',NULL,'slogan','Discover your musical voice','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218180','academy_branches','7','fa',NULL,'slogan','آغاز مسیر حرفه‌ای موسیقی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61633','academy_branch_classrooms','22','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61632','academy_branch_classrooms','22','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61631','academy_branch_classrooms','22','fa',NULL,'title','کلاس 1 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61639','academy_branch_classrooms','23','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61638','academy_branch_classrooms','23','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61637','academy_branch_classrooms','23','fa',NULL,'title','کلاس 2 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61645','academy_branch_classrooms','24','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61644','academy_branch_classrooms','24','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61643','academy_branch_classrooms','24','fa',NULL,'title','کلاس 3 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61651','academy_branch_classrooms','25','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61650','academy_branch_classrooms','25','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61649','academy_branch_classrooms','25','fa',NULL,'title','کلاس 4 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61657','academy_branch_classrooms','26','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61656','academy_branch_classrooms','26','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61655','academy_branch_classrooms','26','fa',NULL,'title','کلاس 5 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61663','academy_branch_classrooms','27','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61662','academy_branch_classrooms','27','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61661','academy_branch_classrooms','27','fa',NULL,'title','کلاس 6 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61669','academy_branch_classrooms','28','fa',NULL,'description','این کلاس با چیدمان آموزشی، نور مناسب و تجهیزات موردنیاز برای برگزاری دوره‌های حضوری آماده شده است.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61668','academy_branch_classrooms','28','fa',NULL,'summary','کلاس مجهز با ظرفیت مناسب برای آموزش موسیقی.','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61667','academy_branch_classrooms','28','fa',NULL,'title','کلاس 7 - مهرآهنگ - شعبه مرکزی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61642','academy_branch_classroom_assets','64','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61646','academy_branch_classroom_assets','65','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61647','academy_branch_classroom_assets','66','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61648','academy_branch_classroom_assets','67','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61652','academy_branch_classroom_assets','68','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61653','academy_branch_classroom_assets','69','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61654','academy_branch_classroom_assets','70','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61658','academy_branch_classroom_assets','71','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61659','academy_branch_classroom_assets','72','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61660','academy_branch_classroom_assets','73','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61664','academy_branch_classroom_assets','74','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61665','academy_branch_classroom_assets','75','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61666','academy_branch_classroom_assets','76','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61670','academy_branch_classroom_assets','77','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61671','academy_branch_classroom_assets','78','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61672','academy_branch_classroom_assets','79','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61676','academy_branch_classroom_assets','80','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61677','academy_branch_classroom_assets','81','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61678','academy_branch_classroom_assets','82','fa',NULL,'title','صندلی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61682','academy_branch_classroom_assets','83','fa',NULL,'title','پایه نت','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('61683','academy_branch_classroom_assets','84','fa',NULL,'title','سیستم صوتی','1','2026-08-12 17:31:54','8','2026-08-13 23:11:57','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240955','academy_branch_courses','152','en',NULL,'code','test-course-b7-1','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240951','academy_branch_courses','152','fa',NULL,'code','test-course-b7-1','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240958','academy_branch_courses','152','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240954','academy_branch_courses','152','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240957','academy_branch_courses','152','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240953','academy_branch_courses','152','fa',NULL,'summary','دوره آموزشی رباب ایرانی در سطح انتخاب‌شده.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240956','academy_branch_courses','152','en',NULL,'title','Music course 1','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240952','academy_branch_courses','152','fa',NULL,'title','دوره رباب ایرانی 1','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240963','academy_branch_courses','153','en',NULL,'code','test-course-b7-2','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240959','academy_branch_courses','153','fa',NULL,'code','test-course-b7-2','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240966','academy_branch_courses','153','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240962','academy_branch_courses','153','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240965','academy_branch_courses','153','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240961','academy_branch_courses','153','fa',NULL,'summary','دوره آموزشی کمانچه در سطح انتخاب‌شده.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240964','academy_branch_courses','153','en',NULL,'title','Music course 2','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240960','academy_branch_courses','153','fa',NULL,'title','دوره کمانچه 2','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240971','academy_branch_courses','154','en',NULL,'code','test-course-b7-3','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240967','academy_branch_courses','154','fa',NULL,'code','test-course-b7-3','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240974','academy_branch_courses','154','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240970','academy_branch_courses','154','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240973','academy_branch_courses','154','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240969','academy_branch_courses','154','fa',NULL,'summary','دوره آموزشی قیچک در سطح انتخاب‌شده.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240972','academy_branch_courses','154','en',NULL,'title','Music course 3','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240968','academy_branch_courses','154','fa',NULL,'title','دوره قیچک 3','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240979','academy_branch_courses','155','en',NULL,'code','test-course-b7-4','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240975','academy_branch_courses','155','fa',NULL,'code','test-course-b7-4','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240982','academy_branch_courses','155','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240978','academy_branch_courses','155','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240981','academy_branch_courses','155','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240977','academy_branch_courses','155','fa',NULL,'summary','دوره آموزشی قیچک باس در سطح انتخاب‌شده.','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240980','academy_branch_courses','155','en',NULL,'title','Music course 4','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240976','academy_branch_courses','155','fa',NULL,'title','دوره قیچک باس 4','1','2026-08-14 00:26:56','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240987','academy_branch_courses','156','en',NULL,'code','test-course-b7-5','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240983','academy_branch_courses','156','fa',NULL,'code','test-course-b7-5','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240990','academy_branch_courses','156','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240986','academy_branch_courses','156','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240989','academy_branch_courses','156','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240985','academy_branch_courses','156','fa',NULL,'summary','دوره آموزشی نی در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240988','academy_branch_courses','156','en',NULL,'title','Music course 5','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240984','academy_branch_courses','156','fa',NULL,'title','دوره نی 5','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240995','academy_branch_courses','157','en',NULL,'code','test-course-b7-6','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240991','academy_branch_courses','157','fa',NULL,'code','test-course-b7-6','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240998','academy_branch_courses','157','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240994','academy_branch_courses','157','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240997','academy_branch_courses','157','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240993','academy_branch_courses','157','fa',NULL,'summary','دوره آموزشی نی‌انبان در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240996','academy_branch_courses','157','en',NULL,'title','Music course 6','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240992','academy_branch_courses','157','fa',NULL,'title','دوره نی‌انبان 6','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241003','academy_branch_courses','158','en',NULL,'code','test-course-b7-7','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6240999','academy_branch_courses','158','fa',NULL,'code','test-course-b7-7','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241006','academy_branch_courses','158','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241002','academy_branch_courses','158','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241005','academy_branch_courses','158','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241001','academy_branch_courses','158','fa',NULL,'summary','دوره آموزشی سرنا در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241004','academy_branch_courses','158','en',NULL,'title','Music course 7','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241000','academy_branch_courses','158','fa',NULL,'title','دوره سرنا 7','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241011','academy_branch_courses','159','en',NULL,'code','test-course-b7-8','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241007','academy_branch_courses','159','fa',NULL,'code','test-course-b7-8','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241014','academy_branch_courses','159','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241010','academy_branch_courses','159','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241013','academy_branch_courses','159','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241009','academy_branch_courses','159','fa',NULL,'summary','دوره آموزشی کرنا در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241012','academy_branch_courses','159','en',NULL,'title','Music course 8','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241008','academy_branch_courses','159','fa',NULL,'title','دوره کرنا 8','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241019','academy_branch_courses','160','en',NULL,'code','test-course-b7-9','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241015','academy_branch_courses','160','fa',NULL,'code','test-course-b7-9','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241022','academy_branch_courses','160','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241018','academy_branch_courses','160','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241021','academy_branch_courses','160','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241017','academy_branch_courses','160','fa',NULL,'summary','دوره آموزشی دوزله در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241020','academy_branch_courses','160','en',NULL,'title','Music course 9','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241016','academy_branch_courses','160','fa',NULL,'title','دوره دوزله 9','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241027','academy_branch_courses','161','en',NULL,'code','test-course-b7-10','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241023','academy_branch_courses','161','fa',NULL,'code','test-course-b7-10','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241030','academy_branch_courses','161','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241026','academy_branch_courses','161','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241029','academy_branch_courses','161','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241025','academy_branch_courses','161','fa',NULL,'summary','دوره آموزشی بالابان در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241028','academy_branch_courses','161','en',NULL,'title','Music course 10','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241024','academy_branch_courses','161','fa',NULL,'title','دوره بالابان 10','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241035','academy_branch_courses','162','en',NULL,'code','test-course-b7-11','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241031','academy_branch_courses','162','fa',NULL,'code','test-course-b7-11','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241038','academy_branch_courses','162','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241034','academy_branch_courses','162','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241037','academy_branch_courses','162','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241033','academy_branch_courses','162','fa',NULL,'summary','دوره آموزشی تنبک در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241036','academy_branch_courses','162','en',NULL,'title','Music course 11','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241032','academy_branch_courses','162','fa',NULL,'title','دوره تنبک 11','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241043','academy_branch_courses','163','en',NULL,'code','test-course-b7-12','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241039','academy_branch_courses','163','fa',NULL,'code','test-course-b7-12','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241046','academy_branch_courses','163','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241042','academy_branch_courses','163','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241045','academy_branch_courses','163','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241041','academy_branch_courses','163','fa',NULL,'summary','دوره آموزشی دف در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241044','academy_branch_courses','163','en',NULL,'title','Music course 12','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241040','academy_branch_courses','163','fa',NULL,'title','دوره دف 12','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241051','academy_branch_courses','164','en',NULL,'code','test-course-b7-13','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241047','academy_branch_courses','164','fa',NULL,'code','test-course-b7-13','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241054','academy_branch_courses','164','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241050','academy_branch_courses','164','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241053','academy_branch_courses','164','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241049','academy_branch_courses','164','fa',NULL,'summary','دوره آموزشی دایره در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241052','academy_branch_courses','164','en',NULL,'title','Music course 13','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241048','academy_branch_courses','164','fa',NULL,'title','دوره دایره 13','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241059','academy_branch_courses','165','en',NULL,'code','test-course-b7-14','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241055','academy_branch_courses','165','fa',NULL,'code','test-course-b7-14','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241062','academy_branch_courses','165','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241058','academy_branch_courses','165','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241061','academy_branch_courses','165','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241057','academy_branch_courses','165','fa',NULL,'summary','دوره آموزشی آکاردئون در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241060','academy_branch_courses','165','en',NULL,'title','Music course 14','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241056','academy_branch_courses','165','fa',NULL,'title','دوره آکاردئون 14','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241067','academy_branch_courses','166','en',NULL,'code','test-course-b7-15','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241063','academy_branch_courses','166','fa',NULL,'code','test-course-b7-15','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241070','academy_branch_courses','166','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241066','academy_branch_courses','166','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241069','academy_branch_courses','166','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241065','academy_branch_courses','166','fa',NULL,'summary','دوره آموزشی ریکوردر در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241068','academy_branch_courses','166','en',NULL,'title','Music course 15','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241064','academy_branch_courses','166','fa',NULL,'title','دوره ریکوردر 15','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241075','academy_branch_courses','167','en',NULL,'code','test-course-b7-16','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241071','academy_branch_courses','167','fa',NULL,'code','test-course-b7-16','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241078','academy_branch_courses','167','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241074','academy_branch_courses','167','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241077','academy_branch_courses','167','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241073','academy_branch_courses','167','fa',NULL,'summary','دوره آموزشی تنظیم موسیقی در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241076','academy_branch_courses','167','en',NULL,'title','Music course 16','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241072','academy_branch_courses','167','fa',NULL,'title','دوره تنظیم موسیقی 16','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241083','academy_branch_courses','168','en',NULL,'code','test-course-b7-17','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241079','academy_branch_courses','168','fa',NULL,'code','test-course-b7-17','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241086','academy_branch_courses','168','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241082','academy_branch_courses','168','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241085','academy_branch_courses','168','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241081','academy_branch_courses','168','fa',NULL,'summary','دوره آموزشی صداسازی در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241084','academy_branch_courses','168','en',NULL,'title','Music course 17','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241080','academy_branch_courses','168','fa',NULL,'title','دوره صداسازی 17','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241091','academy_branch_courses','169','en',NULL,'code','test-course-b7-18','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241087','academy_branch_courses','169','fa',NULL,'code','test-course-b7-18','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241094','academy_branch_courses','169','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241090','academy_branch_courses','169','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241093','academy_branch_courses','169','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241089','academy_branch_courses','169','fa',NULL,'summary','دوره آموزشی دوتار در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241092','academy_branch_courses','169','en',NULL,'title','Music course 18','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241088','academy_branch_courses','169','fa',NULL,'title','دوره دوتار 18','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241099','academy_branch_courses','170','en',NULL,'code','test-course-b7-19','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241095','academy_branch_courses','170','fa',NULL,'code','test-course-b7-19','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241102','academy_branch_courses','170','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241098','academy_branch_courses','170','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241101','academy_branch_courses','170','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241097','academy_branch_courses','170','fa',NULL,'summary','دوره آموزشی دیوان در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241100','academy_branch_courses','170','en',NULL,'title','Music course 19','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241096','academy_branch_courses','170','fa',NULL,'title','دوره دیوان 19','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241107','academy_branch_courses','171','en',NULL,'code','test-course-b7-20','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241103','academy_branch_courses','171','fa',NULL,'code','test-course-b7-20','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241110','academy_branch_courses','171','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241106','academy_branch_courses','171','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241109','academy_branch_courses','171','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241105','academy_branch_courses','171','fa',NULL,'summary','دوره آموزشی شورانگیز در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241108','academy_branch_courses','171','en',NULL,'title','Music course 20','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241104','academy_branch_courses','171','fa',NULL,'title','دوره شورانگیز 20','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241115','academy_branch_courses','172','en',NULL,'code','test-course-b7-21','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241111','academy_branch_courses','172','fa',NULL,'code','test-course-b7-21','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241118','academy_branch_courses','172','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241114','academy_branch_courses','172','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241117','academy_branch_courses','172','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241113','academy_branch_courses','172','fa',NULL,'summary','دوره آموزشی رباب ایرانی در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241116','academy_branch_courses','172','en',NULL,'title','Music course 21','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241112','academy_branch_courses','172','fa',NULL,'title','دوره رباب ایرانی 21','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241123','academy_branch_courses','173','en',NULL,'code','test-course-b7-22','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241119','academy_branch_courses','173','fa',NULL,'code','test-course-b7-22','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241126','academy_branch_courses','173','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241122','academy_branch_courses','173','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241125','academy_branch_courses','173','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241121','academy_branch_courses','173','fa',NULL,'summary','دوره آموزشی کمانچه در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241124','academy_branch_courses','173','en',NULL,'title','Music course 22','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241120','academy_branch_courses','173','fa',NULL,'title','دوره کمانچه 22','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241131','academy_branch_courses','174','en',NULL,'code','test-course-b7-23','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241127','academy_branch_courses','174','fa',NULL,'code','test-course-b7-23','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241134','academy_branch_courses','174','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241130','academy_branch_courses','174','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241133','academy_branch_courses','174','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241129','academy_branch_courses','174','fa',NULL,'summary','دوره آموزشی قیچک در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241132','academy_branch_courses','174','en',NULL,'title','Music course 23','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241128','academy_branch_courses','174','fa',NULL,'title','دوره قیچک 23','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241139','academy_branch_courses','175','en',NULL,'code','test-course-b7-24','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241135','academy_branch_courses','175','fa',NULL,'code','test-course-b7-24','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241142','academy_branch_courses','175','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241138','academy_branch_courses','175','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241141','academy_branch_courses','175','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241137','academy_branch_courses','175','fa',NULL,'summary','دوره آموزشی قیچک باس در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241140','academy_branch_courses','175','en',NULL,'title','Music course 24','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241136','academy_branch_courses','175','fa',NULL,'title','دوره قیچک باس 24','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241147','academy_branch_courses','176','en',NULL,'code','test-course-b7-25','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241143','academy_branch_courses','176','fa',NULL,'code','test-course-b7-25','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241150','academy_branch_courses','176','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241146','academy_branch_courses','176','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241149','academy_branch_courses','176','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241145','academy_branch_courses','176','fa',NULL,'summary','دوره آموزشی نی در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241148','academy_branch_courses','176','en',NULL,'title','Music course 25','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241144','academy_branch_courses','176','fa',NULL,'title','دوره نی 25','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241155','academy_branch_courses','177','en',NULL,'code','test-course-b7-26','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241151','academy_branch_courses','177','fa',NULL,'code','test-course-b7-26','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241158','academy_branch_courses','177','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241154','academy_branch_courses','177','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241157','academy_branch_courses','177','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241153','academy_branch_courses','177','fa',NULL,'summary','دوره آموزشی نی‌انبان در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241156','academy_branch_courses','177','en',NULL,'title','Music course 26','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241152','academy_branch_courses','177','fa',NULL,'title','دوره نی‌انبان 26','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241163','academy_branch_courses','178','en',NULL,'code','test-course-b7-27','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241159','academy_branch_courses','178','fa',NULL,'code','test-course-b7-27','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241166','academy_branch_courses','178','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241162','academy_branch_courses','178','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241165','academy_branch_courses','178','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241161','academy_branch_courses','178','fa',NULL,'summary','دوره آموزشی سرنا در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241164','academy_branch_courses','178','en',NULL,'title','Music course 27','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241160','academy_branch_courses','178','fa',NULL,'title','دوره سرنا 27','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241171','academy_branch_courses','179','en',NULL,'code','test-course-b7-28','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241167','academy_branch_courses','179','fa',NULL,'code','test-course-b7-28','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241174','academy_branch_courses','179','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241170','academy_branch_courses','179','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241173','academy_branch_courses','179','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241169','academy_branch_courses','179','fa',NULL,'summary','دوره آموزشی کرنا در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241172','academy_branch_courses','179','en',NULL,'title','Music course 28','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241168','academy_branch_courses','179','fa',NULL,'title','دوره کرنا 28','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241179','academy_branch_courses','180','en',NULL,'code','test-course-b7-29','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241175','academy_branch_courses','180','fa',NULL,'code','test-course-b7-29','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241182','academy_branch_courses','180','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241178','academy_branch_courses','180','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241181','academy_branch_courses','180','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241177','academy_branch_courses','180','fa',NULL,'summary','دوره آموزشی دوزله در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241180','academy_branch_courses','180','en',NULL,'title','Music course 29','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241176','academy_branch_courses','180','fa',NULL,'title','دوره دوزله 29','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241187','academy_branch_courses','181','en',NULL,'code','test-course-b7-30','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241183','academy_branch_courses','181','fa',NULL,'code','test-course-b7-30','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241190','academy_branch_courses','181','en',NULL,'description','This fixture course is based on the lessons offered by its branch.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241186','academy_branch_courses','181','fa',NULL,'description','این دوره آزمایشی مطابق امکانات شعبه و درس‌های ارائه‌شده آن ایجاد شده است.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241189','academy_branch_courses','181','en',NULL,'summary','A branch music course at the selected learning level.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241185','academy_branch_courses','181','fa',NULL,'summary','دوره آموزشی بالابان در سطح انتخاب‌شده.','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241188','academy_branch_courses','181','en',NULL,'title','Music course 30','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6241184','academy_branch_courses','181','fa',NULL,'title','دوره بالابان 30','1','2026-08-14 00:26:57','1','2026-08-14 07:52:24','1',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6438277','academy_branch_scheduling_rules','22','fa',NULL,'description','توضیحات مربوط به «مهلت رزرو کلاس گروهی» برای این شعبه با مقدار ۴۸ ساعت قبل','1','2026-08-14 12:13:44','25','2026-08-14 12:13:44',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6438246','academy_branch_scheduling_rules','22','fa',NULL,'summary','خلاصه قانون مهلت رزرو کلاس گروهی','1','2026-08-14 12:13:44','25','2026-08-14 12:13:44',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6438215','academy_branch_scheduling_rules','22','fa',NULL,'title','مهلت رزرو کلاس گروهی','1','2026-08-14 12:13:44','25','2026-08-14 12:13:44',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217346','users','8','en',NULL,'full_name','Hossein Karimi','1','2025-04-14 22:50:11','8','2026-08-13 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217094','users','8','fa',NULL,'full_name','حسین کریمی','1','2025-04-14 22:50:11','8','2026-08-13 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218165','users','24','en',NULL,'full_name','Sornaz Music Academy 24','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218164','users','24','fa',NULL,'full_name','مهرآهنگ','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218167','users','24','en',NULL,'slogan','Discover your musical voice','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218166','users','24','fa',NULL,'slogan','آغاز مسیر حرفه‌ای موسیقی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218177','users','25','en',NULL,'full_name','Sornaz Music Academy 25','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218176','users','25','fa',NULL,'full_name','مهرآهنگ - شعبه مرکزی','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217374','user_addresses','13','en',NULL,'address','Sample registered address for the academy manager in Iran.','1','2025-04-16 04:50:11','8','2025-04-16 09:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217095','user_addresses','13','fa',NULL,'address','همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا','1','2025-04-16 04:50:11','8','2025-04-16 09:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217375','user_addresses','13','en',NULL,'note','Additional sample notes for this record.','1','2025-04-16 04:50:11','8','2025-04-16 09:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217096','user_addresses','13','fa',NULL,'note','خانه آزمایشی نزدیک میدان است؛ ممکن است عصرها کسی در محل حضور نداشته باشد.','1','2025-04-16 04:50:11','8','2025-04-16 09:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218195','user_addresses','26','en',NULL,'address','Sample registered address for the academy main branch in Iran.','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218194','user_addresses','26','fa',NULL,'address','خیابان فرهنگ، کوچه هنر، پلاک 71','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218676','user_addresses','42','fa',NULL,'address','همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا','1','2026-08-15 04:20:12','24','2026-08-15 09:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218677','user_addresses','42','fa',NULL,'note','خانه آزمایشی نزدیک میدان است؛ ممکن است عصرها کسی در محل حضور نداشته باشد.','1','2026-08-15 04:20:12','24','2026-08-15 09:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218678','user_addresses','43','fa',NULL,'address','اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید','1','2026-08-16 04:20:12','24','2026-08-16 14:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218679','user_addresses','43','fa',NULL,'note','برای ملاقات حضوری، ساعات خنک‌تر روز انتخاب شود و هماهنگی قبلی انجام گیرد.','1','2026-08-16 04:20:12','24','2026-08-16 14:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219272','user_addresses','62','fa',NULL,'address','همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا','1','2026-08-15 10:20:12','25','2026-08-15 15:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219273','user_addresses','62','fa',NULL,'note','خانه آزمایشی نزدیک میدان است؛ ممکن است عصرها کسی در محل حضور نداشته باشد.','1','2026-08-15 10:20:12','25','2026-08-15 15:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219274','user_addresses','63','fa',NULL,'address','اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید','1','2026-08-16 10:20:12','25','2026-08-16 20:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219275','user_addresses','63','fa',NULL,'note','برای ملاقات حضوری، ساعات خنک‌تر روز انتخاب شود و هماهنگی قبلی انجام گیرد.','1','2026-08-16 10:20:12','25','2026-08-16 20:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210799','user_availabilities','192','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210798','user_availabilities','192','fa',NULL,'description','شعبه در روزهای کاری از 08:30:00 تا 21:30:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210797','user_availabilities','192','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210796','user_availabilities','192','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210803','user_availabilities','193','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210802','user_availabilities','193','fa',NULL,'description','شعبه در روزهای کاری از 08:30:00 تا 21:30:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210801','user_availabilities','193','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210800','user_availabilities','193','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210807','user_availabilities','194','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210806','user_availabilities','194','fa',NULL,'description','شعبه در روزهای کاری از 08:30:00 تا 21:30:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210805','user_availabilities','194','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210804','user_availabilities','194','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210811','user_availabilities','195','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210810','user_availabilities','195','fa',NULL,'description','شعبه در روزهای کاری از 08:30:00 تا 21:30:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210809','user_availabilities','195','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210808','user_availabilities','195','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210815','user_availabilities','196','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210814','user_availabilities','196','fa',NULL,'description','شعبه در روزهای کاری از 08:30:00 تا 21:30:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210813','user_availabilities','196','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210812','user_availabilities','196','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210819','user_availabilities','197','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210818','user_availabilities','197','fa',NULL,'description','شعبه در روزهای کاری از 09:00:00 تا 17:00:00 به‌صورت پیوسته فعال است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210817','user_availabilities','197','en',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210816','user_availabilities','197','fa',NULL,'summary','ساعات کاری هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210823','user_availabilities','198','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210822','user_availabilities','198','fa',NULL,'description','شعبه در روز جمعه تعطیل است.','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210821','user_availabilities','198','en',NULL,'summary','تعطیلی هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6210820','user_availabilities','198','fa',NULL,'summary','تعطیلی هفتگی شعبه','1','2026-08-13 23:12:02','8','2026-08-13 23:12:02','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217788','user_availabilities','457','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217112','user_availabilities','457','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217789','user_availabilities','457','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217111','user_availabilities','457','fa',NULL,'summary','حضور هفتگی در شنبه از 08:00 تا 11:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217790','user_availabilities','458','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217114','user_availabilities','458','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217791','user_availabilities','458','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217113','user_availabilities','458','fa',NULL,'summary','حضور هفتگی در یکشنبه از 09:00 تا 12:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217792','user_availabilities','459','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217116','user_availabilities','459','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217793','user_availabilities','459','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217115','user_availabilities','459','fa',NULL,'summary','حضور هفتگی در یکشنبه از 13:00 تا 16:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217794','user_availabilities','460','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217118','user_availabilities','460','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217795','user_availabilities','460','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217117','user_availabilities','460','fa',NULL,'summary','حضور هفتگی در دوشنبه از 08:00 تا 11:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217796','user_availabilities','461','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217120','user_availabilities','461','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217797','user_availabilities','461','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217119','user_availabilities','461','fa',NULL,'summary','حضور هفتگی در دوشنبه از 12:00 تا 15:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217798','user_availabilities','462','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217122','user_availabilities','462','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217799','user_availabilities','462','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217121','user_availabilities','462','fa',NULL,'summary','حضور هفتگی در دوشنبه از 16:00 تا 19:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217800','user_availabilities','463','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217124','user_availabilities','463','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217801','user_availabilities','463','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217123','user_availabilities','463','fa',NULL,'summary','حضور هفتگی در سه‌شنبه از 09:00 تا 12:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217802','user_availabilities','464','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217126','user_availabilities','464','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217803','user_availabilities','464','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217125','user_availabilities','464','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 08:00 تا 11:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217804','user_availabilities','465','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217128','user_availabilities','465','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217805','user_availabilities','465','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217127','user_availabilities','465','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 12:00 تا 15:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217806','user_availabilities','466','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217130','user_availabilities','466','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217807','user_availabilities','466','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217129','user_availabilities','466','fa',NULL,'summary','حضور هفتگی در پنجشنبه از 09:00 تا 12:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217808','user_availabilities','467','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217132','user_availabilities','467','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217809','user_availabilities','467','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217131','user_availabilities','467','fa',NULL,'summary','حضور هفتگی در پنجشنبه از 13:00 تا 16:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217810','user_availabilities','468','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217134','user_availabilities','468','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217811','user_availabilities','468','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217133','user_availabilities','468','fa',NULL,'summary','حضور هفتگی در پنجشنبه از 17:00 تا 20:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217812','user_availabilities','469','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217136','user_availabilities','469','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217813','user_availabilities','469','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217135','user_availabilities','469','fa',NULL,'summary','حضور هفتگی در جمعه از 08:00 تا 11:00','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217814','user_availabilities','470','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217138','user_availabilities','470','fa',NULL,'description','حضور غیرتکراری کاربر برای جلسه، ارزیابی یا برنامه ویژه در تاریخ مشخص‌شده.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217815','user_availabilities','470','en',NULL,'summary','Recurring or date-specific availability for this member.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217137','user_availabilities','470','fa',NULL,'summary','حضور ویژه در تاریخ 2026-09-19','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218713','user_availabilities','606','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218712','user_availabilities','606','fa',NULL,'summary','حضور هفتگی در شنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218715','user_availabilities','607','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218714','user_availabilities','607','fa',NULL,'summary','حضور هفتگی در شنبه از 12:00 تا 15:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218717','user_availabilities','608','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218716','user_availabilities','608','fa',NULL,'summary','حضور هفتگی در یکشنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218719','user_availabilities','609','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218718','user_availabilities','609','fa',NULL,'summary','حضور هفتگی در یکشنبه از 13:00 تا 16:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218721','user_availabilities','610','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218720','user_availabilities','610','fa',NULL,'summary','حضور هفتگی در یکشنبه از 17:00 تا 20:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218723','user_availabilities','611','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218722','user_availabilities','611','fa',NULL,'summary','حضور هفتگی در دوشنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218725','user_availabilities','612','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218724','user_availabilities','612','fa',NULL,'summary','حضور هفتگی در سه‌شنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218727','user_availabilities','613','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218726','user_availabilities','613','fa',NULL,'summary','حضور هفتگی در سه‌شنبه از 13:00 تا 16:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218729','user_availabilities','614','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218728','user_availabilities','614','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218731','user_availabilities','615','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218730','user_availabilities','615','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 12:00 تا 15:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218733','user_availabilities','616','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218732','user_availabilities','616','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 16:00 تا 19:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218735','user_availabilities','617','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218734','user_availabilities','617','fa',NULL,'summary','حضور هفتگی در پنجشنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218737','user_availabilities','618','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218736','user_availabilities','618','fa',NULL,'summary','حضور هفتگی در جمعه از 08:00 تا 11:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218739','user_availabilities','619','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218738','user_availabilities','619','fa',NULL,'summary','حضور هفتگی در جمعه از 12:00 تا 15:00','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218741','user_availabilities','620','fa',NULL,'description','حضور غیرتکراری کاربر برای جلسه، ارزیابی یا برنامه ویژه در تاریخ مشخص‌شده.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218740','user_availabilities','620','fa',NULL,'summary','حضور ویژه در تاریخ 2026-10-22','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219307','user_availabilities','756','fa',NULL,'description','بازه حضور دوره‌ای کاربر در خیابان فرهنگ، کوچه هنر، پلاک 71؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219306','user_availabilities','756','fa',NULL,'summary','حضور هفتگی در شنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219309','user_availabilities','757','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219308','user_availabilities','757','fa',NULL,'summary','حضور هفتگی در شنبه از 12:00 تا 15:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219311','user_availabilities','758','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219310','user_availabilities','758','fa',NULL,'summary','حضور هفتگی در یکشنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219313','user_availabilities','759','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219312','user_availabilities','759','fa',NULL,'summary','حضور هفتگی در یکشنبه از 13:00 تا 16:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219315','user_availabilities','760','fa',NULL,'description','بازه حضور دوره‌ای کاربر در خیابان فرهنگ، کوچه هنر، پلاک 71؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219314','user_availabilities','760','fa',NULL,'summary','حضور هفتگی در یکشنبه از 17:00 تا 20:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219317','user_availabilities','761','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219316','user_availabilities','761','fa',NULL,'summary','حضور هفتگی در دوشنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219319','user_availabilities','762','fa',NULL,'description','بازه حضور دوره‌ای کاربر در خیابان فرهنگ، کوچه هنر، پلاک 71؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219318','user_availabilities','762','fa',NULL,'summary','حضور هفتگی در سه‌شنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219321','user_availabilities','763','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219320','user_availabilities','763','fa',NULL,'summary','حضور هفتگی در سه‌شنبه از 13:00 تا 16:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219323','user_availabilities','764','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219322','user_availabilities','764','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 08:00 تا 11:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219325','user_availabilities','765','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219324','user_availabilities','765','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 12:00 تا 15:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219327','user_availabilities','766','fa',NULL,'description','بازه حضور دوره‌ای کاربر در خیابان فرهنگ، کوچه هنر، پلاک 71؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219326','user_availabilities','766','fa',NULL,'summary','حضور هفتگی در چهارشنبه از 16:00 تا 19:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219329','user_availabilities','767','fa',NULL,'description','بازه حضور دوره‌ای کاربر در اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219328','user_availabilities','767','fa',NULL,'summary','حضور هفتگی در پنجشنبه از 09:00 تا 12:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219331','user_availabilities','768','fa',NULL,'description','بازه حضور دوره‌ای کاربر در خیابان فرهنگ، کوچه هنر، پلاک 71؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219330','user_availabilities','768','fa',NULL,'summary','حضور هفتگی در جمعه از 08:00 تا 11:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219333','user_availabilities','769','fa',NULL,'description','بازه حضور دوره‌ای کاربر در همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219332','user_availabilities','769','fa',NULL,'summary','حضور هفتگی در جمعه از 12:00 تا 15:00','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219335','user_availabilities','770','fa',NULL,'description','حضور غیرتکراری کاربر برای جلسه، ارزیابی یا برنامه ویژه در تاریخ مشخص‌شده.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219334','user_availabilities','770','fa',NULL,'summary','حضور ویژه در تاریخ 2026-10-22','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217942','user_availability_exceptions','19','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217140','user_availability_exceptions','19','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217943','user_availability_exceptions','19','en',NULL,'summary','A scheduled leave, holiday, or availability exception.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217139','user_availability_exceptions','19','fa',NULL,'summary','عدم حضور تمام‌روز در تاریخ 2026-09-04','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217944','user_availability_exceptions','20','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217142','user_availability_exceptions','20','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217945','user_availability_exceptions','20','en',NULL,'summary','A scheduled leave, holiday, or availability exception.','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217141','user_availability_exceptions','20','fa',NULL,'summary','عدم حضور ساعتی در تاریخ 2026-10-11','1','2025-04-14 22:50:11','8','2025-04-14 22:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218743','user_availability_exceptions','48','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218742','user_availability_exceptions','48','fa',NULL,'summary','عدم حضور تمام‌روز در تاریخ 2026-10-27','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218745','user_availability_exceptions','49','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218744','user_availability_exceptions','49','fa',NULL,'summary','عدم حضور ساعتی در تاریخ 2026-11-07','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218747','user_availability_exceptions','50','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218746','user_availability_exceptions','50','fa',NULL,'summary','عدم حضور ساعتی در تاریخ 2026-09-14','1','2026-08-14 00:20:12','24','2026-08-14 00:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219337','user_availability_exceptions','78','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219336','user_availability_exceptions','78','fa',NULL,'summary','عدم حضور تمام‌روز در تاریخ 2026-10-18','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219339','user_availability_exceptions','79','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219338','user_availability_exceptions','79','fa',NULL,'summary','عدم حضور ساعتی در تاریخ 2026-11-25','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219341','user_availability_exceptions','80','fa',NULL,'description','این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219340','user_availability_exceptions','80','fa',NULL,'summary','عدم حضور ساعتی در تاریخ 2026-09-05','1','2026-08-14 00:20:12','25','2026-08-14 00:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217460','user_contacts','37','en',NULL,'note','Additional sample notes for this record.','1','2025-04-15 05:50:11','8','2025-04-16 07:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217098','user_contacts','37','fa',NULL,'note','راه ارتباطی اصلی کاربر؛ کد تأیید یک‌بارمصرف با موفقیت ثبت شده است.','1','2025-04-15 05:50:11','8','2025-04-16 07:50:11','8','2025-04-15 06:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217461','user_contacts','37','en',NULL,'value','09911000007','1','2025-04-15 05:50:11','8','2025-04-16 07:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217097','user_contacts','37','fa',NULL,'value','09911000007','1','2025-04-15 05:50:11','8','2025-04-16 07:50:11','8','2025-04-15 06:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217462','user_contacts','38','en',NULL,'note','Additional sample notes for this record.','1','2025-04-15 11:50:11','8','2025-04-17 15:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217100','user_contacts','38','fa',NULL,'note','راه ارتباطی پشتیبان برای زمان‌هایی که مورد اصلی در دسترس نیست.','1','2025-04-15 11:50:11','8','2025-04-17 15:50:11','8','2025-04-15 13:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217463','user_contacts','38','en',NULL,'value','sornaz.manager07@gmail.com','1','2025-04-15 11:50:11','8','2025-04-17 15:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217099','user_contacts','38','fa',NULL,'value','sornaz.manager07@gmail.com','1','2025-04-15 11:50:11','8','2025-04-17 15:50:11','8','2025-04-15 13:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217464','user_contacts','39','en',NULL,'note','Additional sample notes for this record.','1','2025-04-15 17:50:11','8','2025-04-18 23:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217102','user_contacts','39','fa',NULL,'note','ایمیل امور اداری و دریافت اسناد و صورت‌حساب‌ها.','1','2025-04-15 17:50:11','8','2025-04-18 23:50:11','8','2025-04-15 20:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217465','user_contacts','39','en',NULL,'value','test_academy_manager_07.office@gmail.com','1','2025-04-15 17:50:11','8','2025-04-18 23:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217101','user_contacts','39','fa',NULL,'value','test_academy_manager_07.office@gmail.com','1','2025-04-15 17:50:11','8','2025-04-18 23:50:11','8','2025-04-15 20:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217466','user_contacts','40','en',NULL,'note','Additional sample notes for this record.','1','2025-04-15 23:50:11','8','2025-04-20 07:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217104','user_contacts','40','fa',NULL,'note','صفحه عمومی اینستاگرام؛ پیام‌های کاری در ساعات اداری بررسی می‌شوند.','1','2025-04-15 23:50:11','8','2025-04-20 07:50:11','8','2025-04-16 03:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217467','user_contacts','40','en',NULL,'value','https://instagram.com/test_academy_manager_07','1','2025-04-15 23:50:11','8','2025-04-20 07:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217103','user_contacts','40','fa',NULL,'value','https://instagram.com/test_academy_manager_07','1','2025-04-15 23:50:11','8','2025-04-20 07:50:11','8','2025-04-16 03:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217468','user_contacts','41','en',NULL,'note','Additional sample notes for this record.','1','2025-04-16 05:50:11','8','2025-04-21 15:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217106','user_contacts','41','fa',NULL,'note','شناسه تلگرام برای پشتیبانی و ارسال فایل‌های آموزشی.','1','2025-04-16 05:50:11','8','2025-04-21 15:50:11','8','2025-04-16 10:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217469','user_contacts','41','en',NULL,'value','https://t.me/test_academy_manager_07','1','2025-04-16 05:50:11','8','2025-04-21 15:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217105','user_contacts','41','fa',NULL,'value','https://t.me/test_academy_manager_07','1','2025-04-16 05:50:11','8','2025-04-21 15:50:11','8','2025-04-16 10:50:11','8',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218187','user_contacts','81','en',NULL,'title','Sornaz Music Academy 81','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218186','user_contacts','81','fa',NULL,'title','تلفن اصلی شعبه','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218189','user_contacts','81','en',NULL,'value','02144000061','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218188','user_contacts','81','fa',NULL,'value','02144000061','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218191','user_contacts','82','en',NULL,'title','Sornaz Music Academy 82','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218190','user_contacts','82','fa',NULL,'title','اینستاگرام شعبه','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218193','user_contacts','82','en',NULL,'value','https://instagram.com/sornaz_branch_61','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218192','user_contacts','82','fa',NULL,'value','https://instagram.com/sornaz_branch_61','1','2026-08-14 00:20:12','8','2026-08-14 00:20:12','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218681','user_contacts','130','fa',NULL,'note','راه ارتباطی اصلی کاربر؛ کد تأیید یک‌بارمصرف با موفقیت ثبت شده است.','1','2026-08-14 07:20:12','24','2026-08-15 09:20:12','24','2026-08-14 08:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218680','user_contacts','130','fa',NULL,'value','09911000017','1','2026-08-14 07:20:12','24','2026-08-15 09:20:12','24','2026-08-14 08:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218683','user_contacts','131','fa',NULL,'note','راه ارتباطی پشتیبان برای زمان‌هایی که مورد اصلی در دسترس نیست.','1','2026-08-14 13:20:12','24','2026-08-16 17:20:12','24','2026-08-14 15:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218682','user_contacts','131','fa',NULL,'value','sornaz.manager17@gmail.com','1','2026-08-14 13:20:12','24','2026-08-16 17:20:12','24','2026-08-14 15:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218685','user_contacts','132','fa',NULL,'note','ایمیل امور اداری و دریافت اسناد و صورت‌حساب‌ها.','1','2026-08-14 19:20:12','24','2026-08-18 01:20:12','24','2026-08-14 22:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218684','user_contacts','132','fa',NULL,'value','test_academy_07.office@gmail.com','1','2026-08-14 19:20:12','24','2026-08-18 01:20:12','24','2026-08-14 22:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218687','user_contacts','133','fa',NULL,'note','صفحه عمومی اینستاگرام؛ پیام‌های کاری در ساعات اداری بررسی می‌شوند.','1','2026-08-15 01:20:12','24','2026-08-19 09:20:12','24','2026-08-15 05:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218686','user_contacts','133','fa',NULL,'value','https://instagram.com/test_academy_07','1','2026-08-15 01:20:12','24','2026-08-19 09:20:12','24','2026-08-15 05:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218689','user_contacts','134','fa',NULL,'note','شناسه تلگرام برای پشتیبانی و ارسال فایل‌های آموزشی.','1','2026-08-15 07:20:12','24','2026-08-20 17:20:12','24','2026-08-15 12:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218688','user_contacts','134','fa',NULL,'value','https://t.me/test_academy_07','1','2026-08-15 07:20:12','24','2026-08-20 17:20:12','24','2026-08-15 12:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218691','user_contacts','135','fa',NULL,'note','وب‌سایت آزمایشی معرفی مدیر و برنامه‌های آموزشگاه.','1','2026-08-15 13:20:12','24','2026-08-22 01:20:12','24','2026-08-15 19:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218690','user_contacts','135','fa',NULL,'value','https://test_academy_07.sornaz.test','1','2026-08-15 13:20:12','24','2026-08-22 01:20:12','24','2026-08-15 19:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218693','user_contacts','136','fa',NULL,'note','واتساپ کاری؛ تماس صوتی فقط با هماهنگی قبلی انجام شود.','1','2026-08-15 19:20:12','24','2026-08-23 03:20:12','24','2026-08-16 02:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218692','user_contacts','136','fa',NULL,'value','https://wa.me/989197000017','1','2026-08-15 19:20:12','24','2026-08-23 03:20:12','24','2026-08-16 02:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218695','user_contacts','137','fa',NULL,'note','پروفایل حرفه‌ای قدیمی است و ممکن است با تأخیر به‌روزرسانی شود.','1','2026-08-16 01:20:12','24','2026-08-24 11:20:12','24','2026-08-16 09:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218694','user_contacts','137','fa',NULL,'value','https://linkedin.com/in/test_academy_07','1','2026-08-16 01:20:12','24','2026-08-24 11:20:12','24','2026-08-16 09:20:12','24',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219277','user_contacts','185','fa',NULL,'note','راه ارتباطی اصلی کاربر؛ کد تأیید یک‌بارمصرف با موفقیت ثبت شده است.','1','2026-08-14 07:20:12','25','2026-08-15 09:20:12','25','2026-08-14 08:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219276','user_contacts','185','fa',NULL,'value','09911000107','1','2026-08-14 07:20:12','25','2026-08-15 09:20:12','25','2026-08-14 08:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219279','user_contacts','186','fa',NULL,'note','راه ارتباطی پشتیبان برای زمان‌هایی که مورد اصلی در دسترس نیست.','1','2026-08-14 13:20:12','25','2026-08-16 17:20:12','25','2026-08-14 15:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219278','user_contacts','186','fa',NULL,'value','sornaz.manager107@gmail.com','1','2026-08-14 13:20:12','25','2026-08-16 17:20:12','25','2026-08-14 15:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219281','user_contacts','187','fa',NULL,'note','ایمیل امور اداری و دریافت اسناد و صورت‌حساب‌ها.','1','2026-08-14 19:20:12','25','2026-08-18 01:20:12','25','2026-08-14 22:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219280','user_contacts','187','fa',NULL,'value','test_main_branch_07.office@gmail.com','1','2026-08-14 19:20:12','25','2026-08-18 01:20:12','25','2026-08-14 22:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219283','user_contacts','188','fa',NULL,'note','صفحه عمومی اینستاگرام؛ پیام‌های کاری در ساعات اداری بررسی می‌شوند.','1','2026-08-15 01:20:12','25','2026-08-19 09:20:12','25','2026-08-15 05:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219282','user_contacts','188','fa',NULL,'value','https://instagram.com/test_main_branch_07','1','2026-08-15 01:20:12','25','2026-08-19 09:20:12','25','2026-08-15 05:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219285','user_contacts','189','fa',NULL,'note','شناسه تلگرام برای پشتیبانی و ارسال فایل‌های آموزشی.','1','2026-08-15 07:20:12','25','2026-08-20 17:20:12','25','2026-08-15 12:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219284','user_contacts','189','fa',NULL,'value','https://t.me/test_main_branch_07','1','2026-08-15 07:20:12','25','2026-08-20 17:20:12','25','2026-08-15 12:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219287','user_contacts','190','fa',NULL,'note','وب‌سایت آزمایشی معرفی مدیر و برنامه‌های آموزشگاه.','1','2026-08-15 13:20:12','25','2026-08-22 01:20:12','25','2026-08-15 19:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219286','user_contacts','190','fa',NULL,'value','https://test_main_branch_07.sornaz.test','1','2026-08-15 13:20:12','25','2026-08-22 01:20:12','25','2026-08-15 19:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219289','user_contacts','191','fa',NULL,'note','واتساپ کاری؛ تماس صوتی فقط با هماهنگی قبلی انجام شود.','1','2026-08-15 19:20:12','25','2026-08-23 03:20:12','25','2026-08-16 02:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219288','user_contacts','191','fa',NULL,'value','https://wa.me/989197000107','1','2026-08-15 19:20:12','25','2026-08-23 03:20:12','25','2026-08-16 02:20:12','25',NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218697','user_instruments','11530','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218696','user_instruments','11530','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218699','user_instruments','11531','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218698','user_instruments','11531','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218701','user_instruments','11532','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218700','user_instruments','11532','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218703','user_instruments','11533','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218702','user_instruments','11533','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219291','user_instruments','11551','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219290','user_instruments','11551','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219293','user_instruments','11552','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219292','user_instruments','11552','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219295','user_instruments','11553','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219294','user_instruments','11553','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219297','user_instruments','11554','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219296','user_instruments','11554','fa',NULL,'summary','سابقه آزمایشی پیوسته در نوازندگی این ساز با تمرین منظم هفتگی.','1','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232336','user_instruments','11630','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232335','user_instruments','11630','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232334','user_instruments','11630','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232333','user_instruments','11630','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232340','user_instruments','11631','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232339','user_instruments','11631','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232338','user_instruments','11631','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232337','user_instruments','11631','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232344','user_instruments','11632','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232343','user_instruments','11632','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232342','user_instruments','11632','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232341','user_instruments','11632','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232348','user_instruments','11633','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232347','user_instruments','11633','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232346','user_instruments','11633','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232345','user_instruments','11633','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232352','user_instruments','11634','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232351','user_instruments','11634','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232350','user_instruments','11634','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232349','user_instruments','11634','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232356','user_instruments','11635','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232355','user_instruments','11635','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232354','user_instruments','11635','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232353','user_instruments','11635','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232360','user_instruments','11636','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232359','user_instruments','11636','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232358','user_instruments','11636','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232357','user_instruments','11636','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232364','user_instruments','11637','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232363','user_instruments','11637','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232362','user_instruments','11637','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232361','user_instruments','11637','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232368','user_instruments','11638','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232367','user_instruments','11638','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232366','user_instruments','11638','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232365','user_instruments','11638','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232372','user_instruments','11639','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232371','user_instruments','11639','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232370','user_instruments','11639','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232369','user_instruments','11639','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232376','user_instruments','11640','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232375','user_instruments','11640','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232374','user_instruments','11640','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232373','user_instruments','11640','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232380','user_instruments','11641','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232379','user_instruments','11641','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232378','user_instruments','11641','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232377','user_instruments','11641','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232384','user_instruments','11642','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232383','user_instruments','11642','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232382','user_instruments','11642','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232381','user_instruments','11642','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232388','user_instruments','11643','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232387','user_instruments','11643','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232386','user_instruments','11643','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232385','user_instruments','11643','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232392','user_instruments','11644','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232391','user_instruments','11644','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232390','user_instruments','11644','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232389','user_instruments','11644','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232396','user_instruments','11645','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232395','user_instruments','11645','fa',NULL,'description','این ساز با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232394','user_instruments','11645','en',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232393','user_instruments','11645','fa',NULL,'summary','ساز ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217592','user_lessons','12029','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-15 00:50:11','8','2025-04-15 00:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217108','user_lessons','12029','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2025-04-15 00:50:11','8','2025-04-15 00:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217593','user_lessons','12029','en',NULL,'summary','A concise summary of the member’s experience in this lesson.','1','2025-04-15 00:50:11','8','2025-04-15 00:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217107','user_lessons','12029','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2025-04-15 00:50:11','8','2025-04-15 00:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217594','user_lessons','12030','en',NULL,'description','Detailed English description for this music education record, including its background, purpose, and relevant experience.','1','2025-04-15 02:50:11','8','2025-04-15 02:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217110','user_lessons','12030','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2025-04-15 02:50:11','8','2025-04-15 02:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217595','user_lessons','12030','en',NULL,'summary','A concise summary of the member’s experience in this lesson.','1','2025-04-15 02:50:11','8','2025-04-15 02:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6217109','user_lessons','12030','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2025-04-15 02:50:11','8','2025-04-15 02:50:11','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218705','user_lessons','12052','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218704','user_lessons','12052','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 02:20:12','24','2026-08-14 02:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218707','user_lessons','12053','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218706','user_lessons','12053','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 04:20:12','24','2026-08-14 04:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218709','user_lessons','12054','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218708','user_lessons','12054','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 06:20:12','24','2026-08-14 06:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218711','user_lessons','12055','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6218710','user_lessons','12055','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 08:20:12','24','2026-08-14 08:20:12','24',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219299','user_lessons','12074','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219298','user_lessons','12074','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 02:20:12','25','2026-08-14 02:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219301','user_lessons','12075','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219300','user_lessons','12075','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 04:20:12','25','2026-08-14 04:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219303','user_lessons','12076','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219302','user_lessons','12076','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 06:20:12','25','2026-08-14 06:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219305','user_lessons','12077','fa',NULL,'description','این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.','1','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6219304','user_lessons','12077','fa',NULL,'summary','سابقه آزمایشی پیوسته در این درس موسیقی با تمرین منظم هفتگی.','1','2026-08-14 08:20:12','25','2026-08-14 08:20:12','25',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232400','user_lessons','12156','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232399','user_lessons','12156','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232398','user_lessons','12156','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232397','user_lessons','12156','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232404','user_lessons','12157','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232403','user_lessons','12157','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232402','user_lessons','12157','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232401','user_lessons','12157','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232408','user_lessons','12158','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232407','user_lessons','12158','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232406','user_lessons','12158','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232405','user_lessons','12158','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232412','user_lessons','12159','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232411','user_lessons','12159','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232410','user_lessons','12159','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232409','user_lessons','12159','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232416','user_lessons','12160','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232415','user_lessons','12160','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232414','user_lessons','12160','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232413','user_lessons','12160','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232420','user_lessons','12161','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232419','user_lessons','12161','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232418','user_lessons','12161','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232417','user_lessons','12161','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232424','user_lessons','12162','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232423','user_lessons','12162','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232422','user_lessons','12162','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232421','user_lessons','12162','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232428','user_lessons','12163','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232427','user_lessons','12163','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232426','user_lessons','12163','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232425','user_lessons','12163','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232432','user_lessons','12164','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232431','user_lessons','12164','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232430','user_lessons','12164','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232429','user_lessons','12164','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232436','user_lessons','12165','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232435','user_lessons','12165','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232434','user_lessons','12165','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232433','user_lessons','12165','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232440','user_lessons','12166','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232439','user_lessons','12166','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232438','user_lessons','12166','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232437','user_lessons','12166','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232444','user_lessons','12167','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232443','user_lessons','12167','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232442','user_lessons','12167','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232441','user_lessons','12167','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232448','user_lessons','12168','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232447','user_lessons','12168','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232446','user_lessons','12168','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232445','user_lessons','12168','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232452','user_lessons','12169','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232451','user_lessons','12169','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232450','user_lessons','12169','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232449','user_lessons','12169','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232456','user_lessons','12170','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232455','user_lessons','12170','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232454','user_lessons','12170','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232453','user_lessons','12170','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232460','user_lessons','12171','en',NULL,'description','This sample music academy provides professional instruction, purposeful practice, student performances, and a creative environment from beginner to advanced levels.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232459','user_lessons','12171','fa',NULL,'description','این درس با سطح‌بندی آموزشی مشخص و برنامه منظم در شعبه ارائه می‌شود.','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232458','user_lessons','12171','en',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);
INSERT INTO `translations` (`translation_id`,`table_name`,`table_id`,`locale`,`code`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`,`deleted_at`,`deleted_by`) VALUES ('6232457','user_lessons','12171','fa',NULL,'summary','درس ارائه‌شده در این شعبه','1','2026-08-14 00:26:36','8','2026-08-14 00:26:36','8',NULL,NULL,NULL,NULL);

SET FOREIGN_KEY_CHECKS=1;
