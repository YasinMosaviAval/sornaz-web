<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyTermService;
use Throwable;

class AcademyTermAvailabilityController {


    public function __construct(private AcademyTermService $service) {}


    public function index() {
        try {
            $organizationKind=(string)($_GET['organizationKind']??'branch');
            $availability=$organizationKind==='academy'
                ?$this->service->academyAvailability((int)auth()->id(),(int)($_GET['organizationUserId']??0),(string)($_GET['date']??''),(int)($_GET['timezoneId']??0))
                :$this->service->effectiveAvailability((int)auth()->id(),(int)($_GET['branch']??0),(string)($_GET['date']??''));
            $times = $availability['times'];
            sort($times);
            $closed = $availability['closed'];
            if ($closed) $times = [];
            $workingTimes = $times;
            $classroom = (int) ($_GET['classroom'] ?? 0);
            $duration = max(5, (int) ($_GET['duration'] ?? 90));
            $excludeTerm = max(0, (int) ($_GET['excludeTerm'] ?? 0));
            if ($classroom && $times) {
                $stmt = db()->prepare("SELECT b.start_time,b.end_time,b.timezone_id FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id WHERE s.classroom_id=? AND b.requested_date=? AND s.deleted_at IS NULL AND b.deleted_at IS NULL AND b.status NOT IN ('canceled','rejected') AND (?=0 OR s.term_id<>?)");
                $stmt->execute([$classroom, (string) ($_GET['date'] ?? ''), $excludeTerm, $excludeTerm]);
                $busy = $stmt->fetchAll();
                $targetTimezoneId=(int)($availability['timezoneId']??0);$targetTimezone=$targetTimezoneId?\Core\database\DB::table('f_timezone')->where('timezone_id',$targetTimezoneId)->first():null;
                if($targetTimezone)foreach($busy as &$row){$sourceId=(int)($row['timezone_id']??0);if(!$sourceId||$sourceId===$targetTimezoneId)continue;$source=\Core\database\DB::table('f_timezone')->where('timezone_id',$sourceId)->first();if(!$source)continue;$start=(new \DateTimeImmutable((string)($_GET['date']??'').' '.substr((string)$row['start_time'],0,5),new \DateTimeZone($source['timezone'])))->setTimezone(new \DateTimeZone($targetTimezone['timezone']));$end=(new \DateTimeImmutable((string)($_GET['date']??'').' '.substr((string)$row['end_time'],0,5),new \DateTimeZone($source['timezone'])))->setTimezone(new \DateTimeZone($targetTimezone['timezone']));$row['start_time']=$start->format('H:i');$row['end_time']=$end->format('H:i');}unset($row);
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
            $selectedStart=(string)($_GET['selectedStart']??'');
            $sourceTimezoneId=(int)($_GET['sourceTimezoneId']??0);
            $targetTimezoneId=(int)($availability['timezoneId']??0);
            if(preg_match('/^\d{2}:\d{2}$/',$selectedStart)&&$sourceTimezoneId&&$targetTimezoneId&&$sourceTimezoneId!==$targetTimezoneId){
                $sourceTimezone=\Core\database\DB::table('f_timezone')->where('timezone_id',$sourceTimezoneId)->first();
                $targetTimezone=\Core\database\DB::table('f_timezone')->where('timezone_id',$targetTimezoneId)->first();
                if($sourceTimezone&&$targetTimezone)$selectedStart=(new \DateTimeImmutable((string)($_GET['date']??'').' '.$selectedStart,new \DateTimeZone($sourceTimezone['timezone'])))->setTimezone(new \DateTimeZone($targetTimezone['timezone']))->format('H:i');
            }
            return ResponseFactory::json(['success' => true, 'data' => ['times' => $times, 'closed' => $closed || !$workingTimes, 'full' => !$closed && (bool) $workingTimes && !$times, 'timezoneId'=>$targetTimezoneId, 'selectedStart'=>$selectedStart, 'organizationKind'=>$organizationKind]]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
