<?php
// Isolated SQLite fixtures; never connects to the application database.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function env($key, $default = null) { return $default; }
function session() { return new class { public function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction($f) { db()->beginTransaction(); try { $r = $f(); db()->commit(); return $r; } catch (Throwable $e) { db()->rollBack(); throw $e; } }
$checks = 0;
function check($ok, $label) { if (!$ok) throw new LogicException($label); ++$GLOBALS['checks']; }
function denied($f, $code) { try { $f(); } catch (RuntimeException $e) { check($e->getCode() === $code, $e->getMessage()); return; } throw new LogicException('Expected rejection'); }

use Modules\Academy\Services\ScheduleGuard;
use Modules\Academy\Services\ScheduleTime;
use Modules\Academy\Services\InvoiceLedger;

foreach ([['2026-02-30','10:00','11:00'], ['2026-09-30','25:00','26:00'], ['2026-09-30','10:70','11:00'], ['2026-09-30','12:00','11:00']] as $bad) {
    denied(fn () => ScheduleTime::validate(...$bad), 422);
}
$pdo->exec("CREATE TABLE f_timezone(timezone_id INTEGER,timezone TEXT,deleted_at TEXT);
INSERT INTO f_timezone VALUES(1,'Asia/Tehran',NULL),(2,'UTC',NULL);
CREATE TABLE academy_branch_members(member_id INTEGER,user_id INTEGER,deleted_at TEXT);
INSERT INTO academy_branch_members VALUES(1,10,NULL),(2,20,NULL),(3,10,NULL);
CREATE TABLE academy_branch_course_term_enrollments(term_id INTEGER,member_id INTEGER,status TEXT,deleted_at TEXT);
INSERT INTO academy_branch_course_term_enrollments VALUES(1,1,'active',NULL);
CREATE TABLE academy_branch_bookings(booking_id INTEGER,requested_date TEXT,start_time TEXT,end_time TEXT,timezone_id INTEGER,status TEXT,deleted_at TEXT);
INSERT INTO academy_branch_bookings VALUES(1,'2026-09-30','10:00','11:00',1,'approved',NULL);
CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER,term_id INTEGER,booking_id INTEGER,classroom_id INTEGER,deleted_at TEXT);
INSERT INTO academy_branch_course_term_sessions VALUES(1,1,1,1,NULL);");
denied(fn () => ScheduleGuard::available('2026-09-30','10:30','11:30',1,1,[]), 409);
denied(fn () => ScheduleGuard::available('2026-09-30','10:30','11:30',1,2,[1]), 409);
denied(fn () => ScheduleGuard::available('2026-09-30','10:30','11:30',1,2,[3]), 409);
denied(fn () => ScheduleGuard::available('2026-09-30','06:45','07:15',2,1,[]), 409);
ScheduleGuard::available('2026-09-30','11:00','12:00',1,1,[1]); check(true, 'Adjacent session allowed');
ScheduleGuard::available('2026-09-30','10:00','11:00',1,1,[1],1); check(true, 'Own session excluded');
ScheduleGuard::available('2026-09-30','10:00','11:00',1,2,[2]); check(true, 'Unrelated room and member allowed');
$pdo->exec("UPDATE academy_branch_bookings SET status='canceled'");
ScheduleGuard::available('2026-09-30','10:00','11:00',1,1,[1]); check(true, 'Canceled slot released');
denied(fn () => ScheduleGuard::available('2026-09-30','10:00','11:00',99,1,[]), 422);
foreach (['0','1','10.50'] as $amount) {
    denied(fn () => InvoiceLedger::create(1, 1, ['cost'=>$amount,'installmentCount'=>2,'sessions'=>[[],[]]], '2026-09-30', 1), 422);
}
echo "Launch hardening: $checks checks passed.\n";
