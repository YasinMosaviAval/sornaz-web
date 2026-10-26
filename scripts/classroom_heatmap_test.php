<?php
// Isolated real SQL fixture: no application database is opened or modified.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require_once $map[$class]; });
function db() { return $GLOBALS['fixture']; }
$fixture = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$fixture->exec("CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER,term_id INTEGER,booking_id INTEGER,classroom_id INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_bookings(booking_id INTEGER,status TEXT,requested_date TEXT,start_time TEXT,end_time TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_course_terms(term_id INTEGER,deleted_at TEXT);
INSERT INTO academy_branch_course_terms VALUES(1,NULL),(2,'deleted');
INSERT INTO academy_branch_bookings VALUES(1,'approved','2026-10-24','09:30','11:00',NULL),(2,'pending','2026-10-25','12:00','13:00',NULL),(3,'canceled','2026-10-24','14:00','15:00',NULL),(4,'rejected','2026-10-24','15:00','16:00',NULL),(5,'approved','2026-10-31','09:00','10:00',NULL),(6,'approved','2026-10-24','17:00','18:00','deleted');
INSERT INTO academy_branch_course_term_sessions VALUES(1,1,1,10,NULL),(2,1,1,99,NULL),(3,1,2,10,NULL),(4,1,3,10,NULL),(5,1,4,10,NULL),(6,1,5,10,NULL),(7,1,6,10,NULL),(8,2,1,10,NULL),(9,1,1,10,'deleted');");
$service = (new ReflectionClass(Modules\Academy\Services\AcademyClassroomService::class))->newInstanceWithoutConstructor();
$method = new ReflectionMethod($service, 'heatmapRows');
$rows = $method->invoke($service, [10], '2026-10-24', '2026-10-30');
if (count($rows) !== 2 || array_unique(array_column($rows, 'classroomId')) !== [10] || array_column($rows, 'count') !== [1,1]) throw new RuntimeException('Room scope, week, canceled/rejected or deleted-row exclusion failed');
if ($method->invoke($service, [], '2026-10-24', '2026-10-30') !== []) throw new RuntimeException('Empty scope must remain empty');
foreach (['2026-02-30', 'invalid', '2026-10-24 OR 1=1'] as $week) {
    try { $service->heatmap(1, false, ['week' => $week]); throw new LogicException('Invalid date accepted'); }
    catch (RuntimeException $e) { if ($e->getCode() !== 422) throw $e; }
}
echo "Classroom occupancy SQL: allowed room IDs, week bounds, pending holds, canceled/rejected/deleted exclusion, empty scope and invalid dates passed (SQLite).\n";
