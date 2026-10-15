<?php
// Isolated ownership fixture; never connects to production data.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function (string $class) use ($map): void { if (isset($map[$class])) require_once $map[$class]; });
require_once __DIR__ . '/../Modules/Analytics/Services/PersonalPanelDataService.php';
$GLOBALS['pdo'] = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
$pdo = db();
$pdo->exec("CREATE TABLE user_lessons(user_lesson_id INTEGER PRIMARY KEY,user_id INTEGER,deleted_at TEXT);
CREATE TABLE lessons(lesson_id INTEGER PRIMARY KEY,deleted_at TEXT);
CREATE TABLE levels(level_id INTEGER PRIMARY KEY,type TEXT,is_active INTEGER,sort_order INTEGER,deleted_at TEXT);
CREATE TABLE user_availabilities(user_availability_id INTEGER PRIMARY KEY,user_id INTEGER,unavailable_type TEXT,deleted_at TEXT);
CREATE TABLE f_timezone(timezone_id INTEGER PRIMARY KEY,status TEXT,sort_order INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER PRIMARY KEY,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_invoices(term_invoice_id INTEGER PRIMARY KEY,member_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_invoice_installments(term_invoice_installment_id INTEGER PRIMARY KEY,invoice_id INTEGER,deleted_at TEXT);
CREATE TABLE financial_system_payments(payment_id INTEGER PRIMARY KEY,invoice_id INTEGER,record_type TEXT,deleted_at TEXT);
INSERT INTO user_lessons VALUES(11,2,NULL),(12,3,NULL);
INSERT INTO user_availabilities VALUES(21,2,NULL,NULL),(22,3,NULL,NULL);
INSERT INTO academy_branch_members VALUES(31,2,NULL),(32,3,NULL);
INSERT INTO academy_branch_course_term_invoices VALUES(41,31,NULL),(42,32,NULL);
INSERT INTO academy_branch_course_term_invoice_installments VALUES(51,41,NULL),(52,42,NULL);
INSERT INTO financial_system_payments VALUES(61,41,'academy_term',NULL),(62,42,'academy_term',NULL);");
$service = new \Modules\Analytics\Services\PersonalPanelDataService();
$lessons = $service->lessons(2, false);
$schedules = $service->schedules(2, false);
$finance = $service->finance(2, false);
if (array_column($lessons['items'], 'user_lesson_id') !== [11] || array_column($schedules['items'], 'user_availability_id') !== [21]) throw new LogicException('Personal lessons or schedules leaked another user.');
if (array_column($finance['invoices'], 'term_invoice_id') !== [41] || array_column($finance['installments'], 'invoice_id') !== [41] || array_column($finance['payments'], 'invoice_id') !== [41]) throw new LogicException('Personal finance leaked another member.');
if ($service->finance(4, false) !== ['invoices' => [], 'installments' => [], 'payments' => []]) throw new LogicException('Unrelated user received financial data.');
echo "Personal panel data: self lessons, schedules, invoices, installments and payments isolated.\n";
$pdo->exec("CREATE TABLE academy_branch_course_term_waiting_list(term_waiting_list_id INTEGER PRIMARY KEY,member_id INTEGER,term_id INTEGER,approved_at TEXT,deleted_at TEXT);
INSERT INTO academy_branch_course_term_waiting_list VALUES(71,31,8,NULL,NULL),(72,32,8,NULL,NULL),(73,31,8,NULL,'2026-10-15');");
if (array_column($service->waitingRequests(2, false)['items'], 'term_waiting_list_id') !== [71]) throw new LogicException('Waiting list leaked another member or deleted request.');
if ($service->waitingRequests(4, false) !== ['items' => []]) throw new LogicException('Unrelated user received waiting requests.');
echo "Personal waiting list: current-user membership and deleted-request boundaries passed.\n";
function locale(): string { return 'fa'; }
function app(): object { return new class { public function getLocale(): string { return 'fa'; } }; }
$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,username TEXT,national_code TEXT,birthday TEXT,phone TEXT,deleted_at TEXT);
CREATE TABLE user_addresses(address_id INTEGER PRIMARY KEY,user_id INTEGER,is_main INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_enrollments(enrollment_id INTEGER PRIMARY KEY,term_id INTEGER,member_id INTEGER,type TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,deleted_at TEXT);
INSERT INTO users VALUES(2,'student','1234567890','2000-01-01','09120000000',NULL);
INSERT INTO translations VALUES(1,'academy_branch_course_terms',8,'title','fa','Piano term',1,NULL);");
$ownRequest = $service->waitingRequests(2)['items'][0];
if ($ownRequest['id'] !== 71 || $ownRequest['name'] !== 'student' || $ownRequest['title'] !== 'Piano term' || !$ownRequest['canEdit']) throw new LogicException('Editable waiting request data does not match current user.');
$pdo->exec("UPDATE academy_branch_course_term_waiting_list SET approved_at='2026-10-15' WHERE term_waiting_list_id=71");
if ($service->waitingRequests(2)['items'][0]['canEdit']) throw new LogicException('Approved waiting request remains editable.');
echo "Personal waiting request decoration and approval state passed.\n";
