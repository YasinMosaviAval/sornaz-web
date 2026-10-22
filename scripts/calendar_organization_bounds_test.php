<?php
// Isolated SQLite data: no application bootstrap or production requests.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function check($valid, $label) { if (!$valid) throw new LogicException($label); }
$pdo->exec("CREATE TABLE users(user_id INTEGER,type TEXT,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER,user_id INTEGER,created_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER,academy_id INTEGER,user_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_members(member_id INTEGER,academy_id INTEGER,branch_id INTEGER,user_id INTEGER,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_enrollments(term_id INTEGER,member_id INTEGER,type TEXT,status TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_courses(course_id INTEGER,academy_id INTEGER,branch_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_terms(term_id INTEGER,course_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_sessions(term_id INTEGER,booking_id INTEGER,classroom_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_bookings(booking_id INTEGER,requested_date TEXT,deleted_at TEXT);
INSERT INTO users VALUES(10,'academy',NULL),(20,'academy',NULL),(30,'branch',NULL);
INSERT INTO academies VALUES(100,10,10,NULL),(200,20,20,NULL);
INSERT INTO academy_branches VALUES(7,100,30,NULL),(8,200,40,NULL);
INSERT INTO academy_branch_courses VALUES(501,100,NULL,NULL),(502,100,7,NULL),(503,200,NULL,NULL),(504,200,8,NULL);
INSERT INTO academy_branch_course_terms VALUES(1,501,NULL),(2,502,NULL),(3,503,NULL),(4,504,NULL);
INSERT INTO academy_branch_bookings VALUES(1,'2026-11-01',NULL),(2,'2026-11-15',NULL),(3,'2026-10-01',NULL),(4,'2026-10-07',NULL),(5,'2020-01-01',NULL),(6,'2020-02-01',NULL),(7,'2019-01-01',NULL);
INSERT INTO academy_branch_course_term_sessions VALUES(1,1,10,NULL),(1,2,10,NULL),(2,3,11,NULL),(2,4,11,NULL),(3,5,20,NULL),(4,6,21,NULL),(1,7,0,NULL);");
$service = new Modules\Academy\Services\AcademyClassScheduleService();
check($service->weeklyBounds(10, 'academy') === ['firstDate' => '2026-11-01', 'lastDate' => '2026-11-15'], 'Academy bounds included branch, foreign or unassigned sessions');
check($service->weeklyBounds(10, 7) === ['firstDate' => '2026-10-01', 'lastDate' => '2026-10-07'], 'Branch bounds escaped selection');
check($service->weeklyBounds(10) === ['firstDate' => '2026-10-01', 'lastDate' => '2026-11-15'], 'All organizations escaped tenant scope');
check($service->weeklyBounds(30, 'academy') === ['firstDate' => null, 'lastDate' => null], 'Branch account gained academy sessions');
try { $service->weeklyBounds(10, 8); throw new LogicException('Foreign branch accepted'); } catch (RuntimeException $error) {}
echo "Calendar bounds: academy, branch, all, unassigned sessions and tenant isolation passed.\n";
