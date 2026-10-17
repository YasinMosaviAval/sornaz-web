<?php
require_once __DIR__ . '/../Modules/Analytics/Services/AdminDashboardService.php';
use Modules\Analytics\Services\AdminDashboardService;
$stats = ['attendanceRate' => '75%', 'monthlyIncome' => '1,200', 'newStudentsWeek' => 4, 'activeStudents' => 12, 'todayClasses' => 3, 'pendingPayments' => 2];
$rows = AdminDashboardService::reportSnapshot($stats, '2026-10-17', 7, '<branch>');
function checkReport(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
checkReport(count($rows) === 4, 'Snapshot must contain only reports backed by real metrics');
checkReport($rows[0]['metrics'] === [['label' => 'درصد حضور', 'value' => '75%']], 'Attendance metric changed');
checkReport($rows[1]['metrics'][0]['value'] === '1,200', 'Financial data must remain unmodified');
checkReport($rows[2]['periodFrom'] === '2026-10-11' && $rows[2]['periodTo'] === '2026-10-17', 'Seven-day bounds differ from dashboard query');
checkReport($rows[0]['periodFrom'] === '2026-10-01', 'Monthly report bounds are incorrect');
checkReport(array_unique(array_column($rows, 'branchId')) === [7], 'Selected branch scope lost');
checkReport($rows === AdminDashboardService::reportSnapshot($stats, '2026-10-17', 7, '<branch>'), 'Reports must not contain random business data');
$missing = AdminDashboardService::reportSnapshot([], '2026-10-17', 0, 'All');
checkReport($missing[0]['metrics'][0]['value'] === null, 'Missing data must not be fabricated');
class ReportScopeFixture extends AdminDashboardService {
    public array $received = [];
    public function data(int $actor, array $filters = []): array {
        $this->received = [$actor, $filters];
        if (($filters['branchId'] ?? 0) === 8) throw new RuntimeException('Forbidden branch');
        return ['branches' => [['id' => 7, 'name' => 'Allowed']], 'stats' => []];
    }
}
function locale(): string { return 'fa'; }
$scoped = new ReportScopeFixture();
checkReport($scoped->reports(19, ['branchId' => 7])['reports'][0]['branchName'] === 'Allowed', 'Report must use scoped branch metadata');
checkReport($scoped->received === [19, ['branchId' => 7]], 'Report must delegate actor and filters to the existing scope service');
$denied = false;
try { $scoped->reports(19, ['branchId' => 8]); } catch (RuntimeException $e) { $denied = true; }
checkReport($denied, 'A rejected dashboard scope must also reject report generation');
$source = file_get_contents(__DIR__ . '/../assets/Analytics/js/reports.js');
checkReport(!str_contains($source, 'Math.random') && str_contains($source, '/analytics/admin-reports?'), 'Website must use the scoped reports endpoint');
checkReport(!str_contains($source, 'filteredReports.length ? filteredReports : allReports'), 'Empty filtered exports must not expose other rows');
echo "Real report snapshots: metrics, periods, scope metadata, missing values and frontend source passed.\n";
