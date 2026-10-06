<?php

// Isolated fixtures; no application boot, production database, or gateway calls.
$map = require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) { require_once $map[$class]; } });
$pdo = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$root = dirname(__DIR__).'/storage/financial-records-test-'.bin2hex(random_bytes(6));
mkdir($root, 0700);
register_shutdown_function(static function () use ($root) {
    foreach (glob($root.'/payment-locks/*.lock') as $file) { unlink($file); }
    if (is_dir($root.'/payment-locks')) { rmdir($root.'/payment-locks'); }
    rmdir($root);
});
function db() { return $GLOBALS['pdo']; }
function storage_path($path) { return $GLOBALS['root'].'/'.$path; }
function session() { return new class { public function get($key, $default = null) { return $key === 'suppress_database_notifications' ? true : $default; } }; }
function transaction($work) {
    db()->beginTransaction();
    try { $result = $work(); db()->commit(); return $result; }
    catch (Throwable $e) { db()->rollBack(); throw $e; }
}
function check($ok, $message) { if (!$ok) { throw new LogicException($message); } ++$GLOBALS['checks']; }
function rejected($work) {
    try { $work(); } catch (RuntimeException $e) {
        check(!$e instanceof PDOException && in_array($e->getCode(), [409, 422], true), 'Expected business rejection: '.$e->getMessage());
        return;
    }
    throw new LogicException('Unsafe operation accepted');
}
$checks = 0;
use Modules\Academy\Services\InvoiceLedger;
use Modules\Academy\Services\TermRecordGuard;
use Modules\Academy\Services\AcademyTermService;
$pdo->exec("CREATE TABLE academy_branch_course_term_invoices(term_invoice_id INTEGER PRIMARY KEY,term_id INTEGER,member_id INTEGER,discount_id INTEGER,payable_amount TEXT,currency_id INTEGER,status TEXT,due_date TEXT,created_by INTEGER,updated_by INTEGER,updated_at TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_invoice_installments(term_invoice_installment_id INTEGER PRIMARY KEY,invoice_id INTEGER,installment_number INTEGER,amount TEXT,due_date TEXT,status TEXT,created_by INTEGER,updated_by INTEGER,updated_at TEXT,paid_at TEXT,deleted_at TEXT);
CREATE TABLE financial_system_payments(payment_id INTEGER PRIMARY KEY,invoice_id INTEGER,status TEXT,record_type TEXT DEFAULT 'academy_term');");
$input = ['cost' => '100.00', 'installmentCount' => 3, 'sessions' => [[], [], []]];
transaction(fn () => InvoiceLedger::create(1, 1, $input, '2026-09-01', 1));
$state = InvoiceLedger::snapshot(1);
check($state['total'] === 10000 && $state['paid'] === 0, 'Invoice total mismatch');
check(array_column($state['rows'], 'amount') === ['34.00', '33.00', '33.00'], 'Whole currency split is inaccurate');
$ids = array_column($state['rows'], 'term_invoice_installment_id');
transaction(fn () => InvoiceLedger::revise(1, 1, ['amount' => '101.00', 'statusCode' => 'issued']));
$state = InvoiceLedger::snapshot(1);
check($state['total'] === 10100 && array_column($state['rows'], 'term_invoice_installment_id') === $ids, 'Revision replaced installment identities');
rejected(fn () => transaction(fn () => InvoiceLedger::revise(1, 1, ['amount' => '101', 'statusCode' => 'paid'])));
$before = $pdo->query('SELECT * FROM academy_branch_course_term_invoice_installments')->fetchAll();
$pdo->exec("CREATE TRIGGER reject_invoice_change BEFORE UPDATE ON academy_branch_course_term_invoices BEGIN SELECT RAISE(ABORT,'fixture failure'); END;");
try { transaction(fn () => InvoiceLedger::revise(1, 1, ['amount' => 120, 'statusCode' => 'issued'])); throw new LogicException('Expected fixture failure'); } catch (PDOException $e) {}
check($pdo->query('SELECT * FROM academy_branch_course_term_invoice_installments')->fetchAll() === $before, 'Failed invoice update committed installment changes');
$pdo->exec('DROP TRIGGER reject_invoice_change');
$pdo->exec("UPDATE academy_branch_course_term_invoice_installments SET status='paid' WHERE term_invoice_installment_id=1");
transaction(fn () => InvoiceLedger::refresh(1, 1));
check(InvoiceLedger::snapshot(1)['invoice']['status'] === 'partial', 'Partial payment status wrong');
rejected(fn () => transaction(fn () => InvoiceLedger::revise(1, 1, ['amount' => 120, 'statusCode' => 'partial'])));
rejected(fn () => transaction(fn () => InvoiceLedger::revise(1, 1, ['amount' => 101, 'statusCode' => 'canceled'])));
$pdo->exec("UPDATE academy_branch_course_term_invoice_installments SET status='paid'");
transaction(fn () => InvoiceLedger::refresh(1, 1));
check(InvoiceLedger::snapshot(1)['invoice']['status'] === 'paid', 'Fully paid invoice wrong');
$pdo->exec("UPDATE academy_branch_course_term_invoices SET payable_amount='999.00' WHERE term_invoice_id=1");
rejected(fn () => InvoiceLedger::snapshot(1));
$pdo->exec("UPDATE academy_branch_course_term_invoices SET payable_amount='101.00' WHERE term_invoice_id=1");
foreach (['-1', '1.001', 'NaN', [], '1e8'] as $bad) { rejected(fn () => InvoiceLedger::cents($bad)); }
rejected(fn () => InvoiceLedger::paymentAmount('10.50'));
check(InvoiceLedger::paymentAmount('10.00') === 10, 'Whole amount changed');
transaction(fn () => InvoiceLedger::create(2, 1, $input, '2026-09-01', 1));
$pdo->exec("INSERT INTO financial_system_payments(invoice_id,status) VALUES(2,'pending')");
rejected(fn () => transaction(fn () => InvoiceLedger::revise(2, 1, ['amount' => 200, 'statusCode' => 'draft'])));

$pdo->exec("CREATE TABLE users(user_id INTEGER PRIMARY KEY,type TEXT,deleted_at TEXT); INSERT INTO users VALUES(1,'admin',NULL);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,academy_id INTEGER,deleted_at TEXT); INSERT INTO academy_branches VALUES(1,1,NULL);
CREATE TABLE academy_branch_courses(course_id INTEGER PRIMARY KEY,branch_id INTEGER,academy_id INTEGER,deleted_at TEXT); INSERT INTO academy_branch_courses VALUES(1,1,NULL,NULL);
CREATE TABLE academy_branch_course_terms(term_id INTEGER PRIMARY KEY,course_id INTEGER,currency_id INTEGER,session_period TEXT,status TEXT,deleted_at TEXT); INSERT INTO academy_branch_course_terms VALUES(1,1,1,'week','open',NULL);
CREATE TABLE academy_branch_course_term_enrollments(enrollment_id INTEGER PRIMARY KEY,term_id INTEGER,member_id INTEGER,type TEXT,status TEXT,deleted_at TEXT); INSERT INTO academy_branch_course_term_enrollments VALUES(1,1,7,'student','active',NULL);
CREATE TABLE academy_branch_bookings(booking_id INTEGER PRIMARY KEY,requested_date TEXT,start_time TEXT,end_time TEXT,timezone_id INTEGER,deleted_at TEXT); INSERT INTO academy_branch_bookings VALUES(1,'2026-09-01','10:00:00','11:00:00',1,NULL);
CREATE TABLE academy_branch_course_term_sessions(term_session_id INTEGER PRIMARY KEY,term_id INTEGER,booking_id INTEGER,classroom_id INTEGER,session_type TEXT,cancellation_status TEXT,deleted_at TEXT); INSERT INTO academy_branch_course_term_sessions VALUES(1,1,1,1,'regular','none',NULL);
CREATE TABLE academy_branch_course_term_session_attendances(session_attendance_id INTEGER PRIMARY KEY,session_id INTEGER,member_id INTEGER,status TEXT); INSERT INTO academy_branch_course_term_session_attendances VALUES(1,1,7,'present');
CREATE TABLE translations(translation_id INTEGER PRIMARY KEY,table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_by INTEGER,updated_by INTEGER,deleted_by INTEGER,deleted_at TEXT);");
$edit = ['name' => 'New title', 'courseId' => 1, 'currencyId' => 1, 'repeatType' => 'week', 'status' => 'open', 'teachers' => [], 'students' => [['id' => 7]], 'classroomId' => 1, 'sessions' => [['date' => '2026-09-01', 'startTime' => '10:00', 'endTime' => '11:00', 'timezoneId' => 1]], 'cost' => 101, 'discountId' => 0, 'installmentCount' => 3];
$service = new AcademyTermService;
$tables = ['academy_branch_course_terms', 'academy_branch_course_term_sessions', 'academy_branch_course_term_enrollments', 'academy_branch_course_term_invoices', 'academy_branch_course_term_invoice_installments', 'academy_branch_course_term_session_attendances', 'academy_branch_bookings'];
$snapshot = fn () => array_map(fn ($table) => $GLOBALS['pdo']->query('SELECT * FROM '.$table)->fetchAll(), $tables);
$before = $snapshot();
$service->save(1, $edit, 1);
check($snapshot() === $before, 'Title edit changed financial or educational records');
check($pdo->query("SELECT value FROM translations WHERE field='title'")->fetchColumn() === 'New title', 'Metadata update failed');
foreach ([['cost' => 110], ['students' => []], ['classroomId' => 2], ['courseId' => 2], ['status' => 'pending'], ['sessions' => []]] as $change) {
    rejected(fn () => $service->save(1, array_replace($edit, $change), 1));
}
check($snapshot() === $before, 'Rejected edits damaged history');
rejected(fn () => $service->delete(1, 1));
rejected(fn () => TermRecordGuard::assertSessionEditable(1));
rejected(fn () => $service->payInstallment(1, 1, 1));
$service->save(1, ['metadataOnly' => true, 'name' => 'Metadata only'], 1);
check($snapshot() === $before, 'Explicit metadata edit damaged history');
$service->updateInvoice(1, 1, ['title' => 'Receipt description', 'amount' => 101, 'statusCode' => 'paid']);
check($pdo->query("SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND field='title'")->fetchColumn() === 'Metadata only', 'Invoice description renamed the term');
check($pdo->query("SELECT value FROM translations WHERE table_name='academy_branch_course_term_invoices' AND field='title'")->fetchColumn() === 'Receipt description', 'Invoice description was not stored independently');
rejected(fn () => TermRecordGuard::assertNoHistory('academy_branch_course_terms', 'course_id', 1));
rejected(fn () => TermRecordGuard::assertNoHistory('academy_branch_course_term_enrollments', 'member_id', 7));
rejected(fn () => TermRecordGuard::assertNoHistory('academy_branch_courses', 'branch_id', 1));

// Spy on schema access while a real SQLite transaction is open. DDL must not run.
$spy = new class('sqlite::memory:') extends \Core\database\PrefixedPDO {
    public bool $missing = false;
    public function query(string $query, ?int $fetchMode = null, mixed ...$args): PDOStatement|false {
        if (str_contains($query, 'information_schema.')) {
            $count = $this->missing ? 0 : (str_contains($query, '.TABLES') ? 2 : (str_contains($query, '.COLUMNS') ? 3 : 1));
            return parent::query('SELECT '.$count);
        }
        return parent::query($query);
    }
};
$schema = new ReflectionMethod(Modules\Analytics\Services\UserPointService::class, 'ensureSchema');
$ready = new ReflectionProperty(Modules\Analytics\Services\UserPointService::class, 'schemaReady');
$ready->setValue(null, false);
$spy->exec('CREATE TABLE rollback_fixture(id INTEGER)');
$spy->beginTransaction();
$spy->exec('INSERT INTO rollback_fixture VALUES(1)');
check($schema->invoke(null, $spy) === true && $spy->inTransaction(), 'Point schema check ended transaction');
$spy->rollBack();
check((int) $spy->query('SELECT COUNT(*) FROM rollback_fixture')->fetchColumn() === 0, 'Point schema check broke rollback');
$ready->setValue(null, false);
$spy->missing = true;
$spy->beginTransaction();
check($schema->invoke(null, $spy) === false && $spy->inTransaction(), 'Missing schema was created during transaction');
$spy->rollBack();
echo "Financial and educational records: $checks checks passed.\n";
