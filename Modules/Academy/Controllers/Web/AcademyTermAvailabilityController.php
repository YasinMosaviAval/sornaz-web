<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyTermService;
use Throwable;

class AcademyTermAvailabilityController {


    public function __construct(private AcademyTermService $service) {}


    public function index() {
        try {
            $times = $this->service->availableStartTimes(
                (int) auth()->id(),
                (int) ($_GET['branch'] ?? 0),
                (string) ($_GET['date'] ?? '')
            );
            sort($times);
            $closed = $this->service->isBranchClosed((int) auth()->id(), (int) ($_GET['branch'] ?? 0), (string) ($_GET['date'] ?? ''));
            if ($closed) $times = [];
            $workingTimes = $times;
            $classroom = (int) ($_GET['classroom'] ?? 0);
            $duration = max(5, (int) ($_GET['duration'] ?? 90));
            $excludeTerm = max(0, (int) ($_GET['excludeTerm'] ?? 0));
            if ($classroom && $times) {
                $stmt = db()->prepare("SELECT b.start_time,b.end_time FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id WHERE s.classroom_id=? AND b.requested_date=? AND s.deleted_at IS NULL AND b.deleted_at IS NULL AND b.status NOT IN ('canceled','rejected') AND (?=0 OR s.term_id<>?)");
                $stmt->execute([$classroom, (string) ($_GET['date'] ?? ''), $excludeTerm, $excludeTerm]);
                $busy = $stmt->fetchAll();
                $toMinutes = static fn(string $time): int => (int) substr($time, 0, 2) * 60 + (int) substr($time, 3, 2);
                $workingMap = array_fill_keys($workingTimes, true);
                $times = array_values(array_filter($times, function (string $time) use ($duration, $busy, $toMinutes, $workingMap): bool {
                    $start = $toMinutes($time);
                    $end = $start + $duration;
                    for ($minute = $start; $minute < $end; $minute += 5) {
                        $slot = sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
                        if (!isset($workingMap[$slot])) return false;
                    }
                    foreach ($busy as $row) {
                        if ($start < $toMinutes($row['end_time']) && $end > $toMinutes($row['start_time'])) return false;
                    }
                    return true;
                }));
            }
            return ResponseFactory::json(['success' => true, 'data' => ['times' => $times, 'closed' => $closed || !$workingTimes, 'full' => !$closed && (bool) $workingTimes && !$times]]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
