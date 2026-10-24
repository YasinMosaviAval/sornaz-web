<?php
// Isolated SQL mapping fixture; does not bootstrap or connect to the application database.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
function db() { return $GLOBALS['fixture']; }
$fixture = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$fixture->exec("CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER,term_id INTEGER,booking_id INTEGER,classroom_id INTEGER,delivery_mode TEXT,session_type TEXT,makeup_for_session_id INTEGER,cancellation_status TEXT,cancellation_requested_by INTEGER,cancellation_decided_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_bookings(booking_id INTEGER,status TEXT,requested_date TEXT,start_time TEXT,end_time TEXT,timezone_id INTEGER,deleted_at TEXT);
CREATE TABLE translations(table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,deleted_at TEXT);
INSERT INTO academy_branch_bookings VALUES(1,'approved','2030-01-01','12:00','13:00',1,NULL),(2,'approved','2030-01-02','12:00','13:00',1,NULL);
INSERT INTO academy_branch_course_term_sessions VALUES(1,10,1,1,'in_person','regular',NULL,'none',NULL,NULL,NULL),(2,10,2,2,'online','regular',NULL,'none',NULL,NULL,NULL),(3,99,1,1,'online','regular',NULL,'none',NULL,NULL,NULL),(4,10,1,1,'online','regular',NULL,'none',NULL,NULL,'2030-01-01');");
$service = new Modules\Academy\Services\AcademyTermService();
$sessions = (new ReflectionMethod($service, 'sessions'))->invoke($service, [10]);
if (array_keys($sessions) !== [10] || array_column($sessions[10], 'deliveryMode') !== ['in_person', 'online'] || array_column($sessions[10], 'number') !== [1,2]) throw new RuntimeException('Session mode, term scope, deleted row or numbering mapping failed');
echo "Term sessions: recorded delivery modes, requested-term scope, deleted-row exclusion and numbering passed (SQLite).\n";
