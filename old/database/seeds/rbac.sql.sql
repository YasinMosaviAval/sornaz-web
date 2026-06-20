-- ═══════════════════════════════════════════════════════════════
-- database/seeds/rbac.sql
-- داده‌های اولیه سیستم نقش و دسترسی
-- ═══════════════════════════════════════════════════════════════


-- ───────────────────────────────────────────────────────────────
-- ۱. ROLES — نقش‌ها
-- ───────────────────────────────────────────────────────────────
-- دو دسته نقش داریم:
--   الف) نقش‌های سطح پلتفرم  — کنترل دسترسی به کل سایت
--   ب) نقش‌های سطح آموزشگاه  — عضویت در یک شعبه خاص

INSERT INTO access_system_roles (id, name, description) VALUES

-- ══ نقش‌های سطح پلتفرم ══════════════════════════════════════
(1, 'superadmin', 'مدیر کل پلتفرم — دسترسی کامل به همه چیز'),
(2, 'admin', 'مدیر سایت — مدیریت محتوا، کاربران و آموزشگاه‌ها'),
(3, 'support', 'پشتیبانی — پاسخ به تیکت‌ها و مشکلات کاربران'),
(4, 'content_manager','مدیر محتوا — مدیریت مقالات، دروس و محتوای سایت'),
(5, 'financial_manager', 'مدیر مالی — گزارش‌های مالی و تراکنش‌ها'),
-- ══ نقش‌های سطح آموزشگاه ════════════════════════════════════
(6, 'academy_owner', 'مالک آموزشگاه — کنترل کامل آموزشگاه و تمام شعبه‌ها'),
(7, 'academy_manager', 'مدیر آموزشگاه — مدیریت اجرایی یک شعبه'),
(8, 'academy_receptionist', 'منشی آموزشگاه — ثبت‌نام، فاکتور و ارتباط با دانش‌آموزان'),
(9, 'academy_teacher', 'استاد — تدریس، حضورغیاب و مدیریت جلسات'),
(10, 'academy_student', 'دانش‌آموز — ثبت‌نام در دوره‌ها و مشاهده محتوا'),
-- ══ نقش‌های تکمیلی کاربر ════════════════════════════════════
(11, 'author', 'نویسنده — انتشار مقاله و محتوای آموزشی در سایت'),
(12, 'vip_member', 'عضو ویژه — دسترسی به محتوای پریمیوم'),
(13, 'verified_teacher', 'استاد تأییدشده — تیک آبی، نمایش در صفحه اساتید'),
(14, 'user', 'کاربر عادی — دسترسی پایه به سایت پس از ثبت‌نام');
-- ───────────────────────────────────────────────────────────────
-- ۲. PERMISSIONS — مجوزها
-- ───────────────────────────────────────────────────────────────
-- فرمت نام: {resource}.{action}
-- گروه‌ها: platform | academy | branch | course | user | content | financial

INSERT INTO access_system_permissions (id, name, group_name) VALUES

-- ══ گروه: platform ══════════════════════════════════════════
(1,  'platform.manage_admins',        'platform'),
(2,  'platform.manage_settings',      'platform'),
(3,  'platform.view_audit_logs',      'platform'),
(4,  'platform.manage_languages',     'platform'),
(5,  'platform.manage_instruments',   'platform'),
(6,  'platform.manage_levels',        'platform'),
(7,  'platform.manage_verifications', 'platform'),
(8,  'platform.manage_badges',        'platform'),
(9,  'platform.manage_currencies',    'platform'),
(10, 'platform.view_reports',         'platform'),
-- ══ گروه: users ═════════════════════════════════════════════
(11, 'users.view_list',               'users'),
(12, 'users.view_profile',            'users'),
(13, 'users.create',                  'users'),
(14, 'users.edit',                    'users'),
(15, 'users.delete',                  'users'),
(16, 'users.ban',                     'users'),
(17, 'users.approve',                 'users'),
(18, 'users.assign_role',             'users'),
(19, 'users.view_sessions',           'users'),
(20, 'users.impersonate',             'users'),
-- ══ گروه: academy ════════════════════════════════════════════
(21, 'academy.create',                'academy'),
(22, 'academy.edit',                  'academy'),
(23, 'academy.delete',                'academy'),
(24, 'academy.approve',               'academy'),
(25, 'academy.reject',                'academy'),
(26, 'academy.view_list',             'academy'),
(27, 'academy.view_detail',           'academy'),
(28, 'academy.manage_translations',   'academy'),
-- ══ گروه: branch ═════════════════════════════════════════════
(29, 'branch.create',                 'branch'),
(30, 'branch.edit',                   'branch'),
(31, 'branch.delete',                 'branch'),
(32, 'branch.approve',                'branch'),
(33, 'branch.manage_working_hours',   'branch'),
(34, 'branch.manage_holidays',        'branch'),
(35, 'branch.manage_classrooms',      'branch'),
(36, 'branch.manage_phones',          'branch'),
(37, 'branch.manage_urls',            'branch'),
(38, 'branch.manage_scheduling_rules','branch'),
(39, 'branch.view_members',           'branch'),
(40, 'branch.add_member',             'branch'),
(41, 'branch.remove_member',          'branch'),
(42, 'branch.manage_contracts',       'branch'),
-- ══ گروه: course ═════════════════════════════════════════════
(43, 'course.create',                 'course'),
(44, 'course.edit',                   'course'),
(45, 'course.delete',                 'course'),
(46, 'course.approve',                'course'),
(47, 'course.manage_terms',           'course'),
(48, 'course.view_enrollments',       'course'),
(49, 'course.manage_enrollments',     'course'),
(50, 'course.manage_waiting_list',    'course'),
(51, 'course.assign_teacher',         'course'),
-- ══ گروه: session ════════════════════════════════════════════
(52, 'session.view',                  'session'),
(53, 'session.create',                'session'),
(54, 'session.edit',                  'session'),
(55, 'session.cancel',                'session'),
(56, 'session.reschedule',            'session'),
(57, 'session.manage_attendance',     'session'),
(58, 'session.approve_changes',       'session'),
(59, 'session.manage_bookings',       'session'),
-- ══ گروه: financial ══════════════════════════════════════════
(60, 'financial.view_invoices',       'financial'),
(61, 'financial.create_invoice',      'financial'),
(62, 'financial.edit_invoice',        'financial'),
(63, 'financial.cancel_invoice',      'financial'),
(64, 'financial.view_payments',       'financial'),
(65, 'financial.register_payment',    'financial'),
(66, 'financial.manage_installments', 'financial'),
(67, 'financial.manage_discounts',    'financial'),
(68, 'financial.process_refund',      'financial'),
(69, 'financial.view_accounts',       'financial'),
(70, 'financial.view_ledger',         'financial'),
(71, 'financial.view_reports',        'financial'),
-- ══ گروه: content ════════════════════════════════════════════
(72, 'content.create_post',           'content'),
(73, 'content.edit_own_post',         'content'),
(74, 'content.edit_any_post',         'content'),
(75, 'content.delete_own_post',       'content'),
(76, 'content.delete_any_post',       'content'),
(77, 'content.publish_post',          'content'),
(78, 'content.manage_comments',       'content'),
(79, 'content.moderate_reviews',      'content'),
-- ══ گروه: messaging ══════════════════════════════════════════
(80, 'messaging.send_direct',         'messaging'),
(81, 'messaging.send_group',          'messaging'),
(82, 'messaging.send_broadcast',      'messaging'),
(83, 'messaging.manage_conversations','messaging'),
-- ══ گروه: profile (خود کاربر) ════════════════════════════════
(84, 'profile.edit_own',              'profile'),
(85, 'profile.upload_media',          'profile'),
(86, 'profile.manage_availability',   'profile'),
(87, 'profile.manage_social_links',   'profile'),
(88, 'profile.view_public_profiles',  'profile'),
-- ══ گروه: reports ════════════════════════════════════════════
(89, 'reports.submit',                'reports'),
(90, 'reports.review',                'reports'),
(91, 'reports.resolve',               'reports');
-- ───────────────────────────────────────────────────────────────
-- ۳. ROLE_PERMISSIONS — هر نقش چه مجوزهایی دارد
-- ───────────────────────────────────────────────────────────────
INSERT INTO access_system_role_permissions (role_id, permission_id) VALUES

-- ══ superadmin (id=1) — همه مجوزها ═════════════════════════
-- به جای insert تک‌تک، از یه SELECT استفاده می‌کنیم:
-- (بعد از insert بقیه role‌ها اضافه می‌کنیم)
-- ══ admin (id=2) ════════════════════════════════════════════
-- platform
(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),
-- users
(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),
-- academy
(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(2,28),
-- branch
(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,38),(2,39),(2,40),(2,41),(2,42),
-- course
(2,43),(2,44),(2,45),(2,46),(2,47),(2,48),(2,49),(2,50),(2,51),
-- session
(2,52),(2,53),(2,54),(2,55),(2,56),(2,57),(2,58),(2,59),
-- financial
(2,60),(2,61),(2,62),(2,63),(2,64),(2,65),(2,66),(2,67),(2,68),(2,69),(2,70),(2,71),
-- content
(2,72),(2,73),(2,74),(2,75),(2,76),(2,77),(2,78),(2,79),
-- messaging
(2,80),(2,81),(2,82),(2,83),
-- profile
(2,84),(2,85),(2,86),(2,87),(2,88),
-- reports
(2,89),(2,90),(2,91),
-- ══ support (id=3) ══════════════════════════════════════════
(3,11),(3,12),           -- users: view
(3,26),(3,27),           -- academy: view
(3,52),                  -- session: view
(3,60),(3,64),(3,71),    -- financial: view invoices, payments, reports
(3,78),(3,79),           -- content: moderate
(3,80),(3,83),           -- messaging: send + manage
(3,88),                  -- profile: view public
(3,90),(3,91),           -- reports: review + resolve
-- ══ content_manager (id=4) ══════════════════════════════════
(4,72),(4,73),(4,74),(4,75),(4,76),(4,77),(4,78),(4,79),  -- content: همه
(4,88),                  -- profile: view public
-- ══ financial_manager (id=5) ════════════════════════════════
(5,60),(5,61),(5,62),(5,63),(5,64),(5,65),(5,66),(5,67),(5,68),(5,69),(5,70),(5,71),
-- ══ academy_owner (id=6) ════════════════════════════════════
-- مدیریت کامل آموزشگاه و شعبه‌ها
(6,21),(6,22),(6,26),(6,27),(6,28),     -- academy
(6,29),(6,30),(6,31),(6,33),(6,34),(6,35),(6,36),(6,37),(6,38),(6,39),(6,40),(6,41),(6,42),  -- branch
(6,43),(6,44),(6,45),(6,47),(6,48),(6,49),(6,50),(6,51),  -- course
(6,52),(6,53),(6,54),(6,55),(6,56),(6,57),(6,58),(6,59),  -- session
(6,60),(6,61),(6,62),(6,63),(6,64),(6,65),(6,66),(6,67),(6,68),(6,69),(6,70),(6,71),  -- financial: همه
(6,80),(6,81),           -- messaging
(6,84),(6,85),(6,86),(6,87),(6,88),  -- profile
(6,89),                  -- reports: submit
-- ══ academy_manager (id=7) ══════════════════════════════════
(7,26),(7,27),(7,28),    -- academy: view
(7,29),(7,30),(7,33),(7,34),(7,35),(7,36),(7,37),(7,38),(7,39),(7,40),(7,41),(7,42),  -- branch (بدون حذف)
(7,43),(7,44),(7,47),(7,48),(7,49),(7,50),(7,51),  -- course (بدون حذف)
(7,52),(7,53),(7,54),(7,55),(7,56),(7,57),(7,58),(7,59),  -- session: همه
(7,60),(7,61),(7,62),(7,64),(7,65),(7,66),(7,67),(7,71),  -- financial (بدون refund + ledger)
(7,80),(7,81),           -- messaging
(7,84),(7,85),(7,86),(7,87),(7,88),  -- profile
(7,89),                  -- reports: submit
-- ══ academy_receptionist (id=8) ═════════════════════════════
(8,27),                  -- academy: view detail
(8,39),(8,40),(8,42),    -- branch: view/add members, contracts
(8,48),(8,49),(8,50),    -- course: enrollments + waiting list
(8,52),(8,59),           -- session: view + bookings
(8,60),(8,61),(8,64),(8,65),(8,66),  -- financial: view+create invoice, view+register payment, installments
(8,80),                  -- messaging: direct
(8,84),(8,85),(8,88),    -- profile
-- ══ academy_teacher (id=9) ══════════════════════════════════
(9,27),                  -- academy: view
(9,48),(9,52),           -- course: view enrollments, session view
(9,53),(9,54),(9,55),(9,56),(9,57),  -- session: create, edit, cancel, reschedule, attendance
(9,60),                  -- financial: view invoices (خودش)
(9,80),(9,81),           -- messaging
(9,84),(9,85),(9,86),(9,87),(9,88),  -- profile: همه
(9,89),                  -- reports: submit
-- ══ academy_student (id=10) ═════════════════════════════════
(10,27),                 -- academy: view
(10,52),                 -- session: view (جلسات خودش)
(10,60),                 -- financial: view own invoices
(10,80),                 -- messaging: direct
(10,84),(10,85),(10,86),(10,87),(10,88),  -- profile
(10,89),                 -- reports: submit
-- ══ author (id=11) ══════════════════════════════════════════
(11,72),(11,73),(11,75),(11,77),  -- content: create, edit own, delete own, publish
(11,84),(11,85),(11,87),(11,88),  -- profile
(11,80),                 -- messaging: direct
-- ══ vip_member (id=12) ══════════════════════════════════════
(12,84),(12,85),(12,86),(12,87),(12,88),  -- profile
(12,80),                 -- messaging: direct
(12,89),                 -- reports: submit
-- ══ verified_teacher (id=13) ════════════════════════════════
(13,84),(13,85),(13,86),(13,87),(13,88),  -- profile
(13,72),(13,73),(13,75),(13,77),  -- content: مثل author
(13,80),(13,81),         -- messaging
(13,89),                 -- reports: submit
-- ══ user (id=14) — کاربر عادی ══════════════════════════════
(14,84),(14,85),(14,86),(14,87),(14,88),  -- profile
(14,80),                 -- messaging: direct
(14,89);                 -- reports: submit
-- ───────────────────────────────────────────────────────────────
-- superadmin: همه مجوزها رو با یه query اضافه می‌کنیم
-- ───────────────────────────────────────────────────────────────
INSERT INTO access_system_role_permissions (role_id, permission_id)
SELECT 1, id FROM access_system_permissions;