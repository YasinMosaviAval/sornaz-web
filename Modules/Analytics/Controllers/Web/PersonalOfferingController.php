<?php

namespace Modules\Analytics\Controllers\Web;

use Core\database\DB;
use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyBranchOfferingService;
use Modules\Academy\Services\AcademyTermService;
use RuntimeException;
use Throwable;

final class PersonalOfferingController
{
    public function __construct(private AcademyBranchOfferingService $offerings)
    {
    }

    public function saveLesson(int $id = 0)
    {
        return $this->respond(function () use ($id) {
            $actor = (int) auth()->id();
            $data = $this->payload();
            if ($id) {
                $existing = DB::table('user_lessons')->where('user_lesson_id', $id)->where('user_id', $actor)->whereNull('deleted_at')->first();
                if (!$existing) throw new RuntimeException('درس قابل ویرایش یافت نشد.');
                if (DB::table('academy_branch_member_contracts')->where('user_lesson_id', $id)->whereNull('deleted_at')->first()) {
                    throw new RuntimeException('ویرایش درس متصل به قرارداد باید از پنل آموزشگاه انجام شود.');
                }
            }
            $data['organization_user_id'] = $actor;
            return $this->offerings->saveLesson($actor, $data, $id);
        });
    }

    public function saveSchedule(int $id = 0)
    {
        return $this->respond(function () use ($id) {
            $actor = (int) auth()->id();
            $data = $this->payload();
            if ($id) {
                $existing = DB::table('user_availabilities')->where('user_availability_id', $id)->where('user_id', $actor)->whereNull('unavailable_type')->whereNull('deleted_at')->first();
                if (!$existing) {
                    throw new RuntimeException('برنامهٔ زمانی قابل ویرایش یافت نشد.');
                }
                if (in_array((string) ($existing['status'] ?? ''), ['reserved', 'pending'], true)) {
                    throw new RuntimeException('این بازه در حال استفاده یا تأیید است و قابل تغییر مستقیم نیست.');
                }
            }
            $repeats = ['هفتگی' => 'week', 'دو هفته' => '2-week', 'سه هفته' => '3-week', 'چهار هفته' => '4-week', 'ماهانه' => 'month', 'سالانه' => 'year', 'بی‌تکرار' => 'none'];
            $repeat = $repeats[(string) ($data['repeatPeriod'] ?? '')] ?? null;
            if (!$repeat) throw new RuntimeException('دورهٔ تکرار معتبر نیست.');
            $existingGroup = DB::table('user_availabilities')->where('user_id', $actor)->where('repeat_period', $repeat)->whereNull('unavailable_type')->whereNull('deleted_at');
            if ($repeat === 'week') {
                $days = ['شنبه' => 'saturday', 'یکشنبه' => 'sunday', 'دوشنبه' => 'monday', 'سه‌شنبه' => 'tuesday', 'چهارشنبه' => 'wednesday', 'پنجشنبه' => 'thursday', 'جمعه' => 'friday'];
                $existingGroup->where('day_of_week', $days[(string) ($data['day'] ?? '')] ?? 'invalid')->whereNull('date');
            } else {
                $existingGroup->where('date', (string) ($data['repeatDate'] ?? ''));
            }
            foreach ($existingGroup->get() as $row) {
                if (in_array((string) ($row['status'] ?? ''), ['reserved', 'pending'], true)) {
                    throw new RuntimeException('این زمان‌بندی در حال استفاده یا تأیید است و قابل جایگزینی مستقیم نیست.');
                }
            }
            $data['organizationUserId'] = $actor;
            $data['branchId'] = $actor;
            return $this->offerings->saveSchedule($actor, $data, $id);
        });
    }

    public function saveInvoice(int $id)
    {
        return $this->respond(function () use ($id) {
            if ($id < 1) throw new RuntimeException('شناسهٔ فاکتور معتبر نیست.', 422);
            $data = $this->payload();
            $allowed = ['amount', 'statusCode', 'dueDate', 'title', 'summary', 'description'];
            (new AcademyTermService())->updatePersonalInvoice((int) auth()->id(), $id, array_intersect_key($data, array_flip($allowed)));
            return null;
        });
    }

    public function delete(string $type, int $id)
    {
        return $this->respond(function () use ($type, $id) {
            if (!in_array($type, ['lesson', 'schedule'], true)) {
                throw new RuntimeException('نوع درخواست معتبر نیست.');
            }
            $this->offerings->delete($type, $id, (int) auth()->id());
            return null;
        });
    }

    private function payload(): array
    {
        $encoded = (string) request()->input('payload_b64', '');
        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
        $data = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($data)) {
            throw new RuntimeException('اطلاعات ارسال‌شده معتبر نیست.');
        }
        return $data;
    }

    private function respond(callable $action)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $action()]);
        } catch (Throwable $error) {
            $expected = $error instanceof RuntimeException && !($error instanceof \PDOException);
            return ResponseFactory::json(['success' => false, 'message' => $expected ? $error->getMessage() : 'انجام عملیات در حال حاضر ممکن نیست.'], $expected ? 422 : 500);
        }
    }
}
