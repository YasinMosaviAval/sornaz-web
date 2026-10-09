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
