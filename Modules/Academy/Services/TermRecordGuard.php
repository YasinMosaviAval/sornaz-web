<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

/** Existing records retain their IDs; structural edits use dedicated operations. */
final class TermRecordGuard
{
    public static function assertUnchanged(array $term, array $data): void
    {
        $id = (int) $term['term_id'];
        foreach (['courseId' => 'course_id', 'currencyId' => 'currency_id'] as $input => $column) {
            self::same((int) ($data[$input] ?? 0), (int) $term[$column]);
        }
        self::same((string) ($data['repeatType'] ?? 'no-period'), (string) $term['session_period']);
        if (isset($data['status'])) {
            self::same((string) $data['status'], (string) $term['status']);
        }
        self::people($id, $data);
        self::sessions($id, $data);
        self::invoice($id, $data);
    }

    private static function people(int $id, array $data): void
    {
        $rows = DB::table('academy_branch_course_term_enrollments')->where('term_id', $id)->whereNull('deleted_at')->get();
        foreach (['teachers' => 'teacher', 'students' => 'student'] as $key => $type) {
            $stored = array_map('intval', array_column(array_filter($rows, fn ($r) => $r['type'] === $type), 'member_id'));
            $incoming = array_map(fn ($r) => (int) (is_array($r) ? ($r['id'] ?? 0) : $r), $data[$key] ?? []);
            sort($stored);
            sort($incoming);
            self::same($incoming, $stored);
        }
    }

    private static function sessions(int $id, array $data): void
    {
        $query = db()->prepare('SELECT s.classroom_id,b.requested_date,b.start_time,b.end_time,b.timezone_id FROM academy_branch_course_term_sessions s JOIN academy_branch_bookings b ON b.booking_id=s.booking_id WHERE s.term_id=? AND s.deleted_at IS NULL AND b.deleted_at IS NULL ORDER BY b.requested_date,b.start_time,s.term_session_id');
        $query->execute([$id]);
        $stored = $query->fetchAll();
        $incoming = $data['sessions'] ?? [];
        self::same(count($incoming), count($stored));
        usort($incoming, fn ($a, $b) => [$a['date'] ?? '', $a['startTime'] ?? ''] <=> [$b['date'] ?? '', $b['startTime'] ?? '']);
        foreach ($stored as $index => $row) {
            $item = $incoming[$index];
            self::same((string) ($item['date'] ?? ''), (string) $row['requested_date']);
            self::same(substr((string) ($item['startTime'] ?? ''), 0, 5), substr((string) $row['start_time'], 0, 5));
            self::same(substr((string) ($item['endTime'] ?? ''), 0, 5), substr((string) $row['end_time'], 0, 5));
            self::same((int) ($data['classroomId'] ?? 0), (int) $row['classroom_id']);
            if (!empty($item['timezoneId'])) {
                self::same((int) $item['timezoneId'], (int) $row['timezone_id']);
            }
        }
    }

    private static function invoice(int $id, array $data): void
    {
        $rows = DB::table('academy_branch_course_term_invoices')->where('term_id', $id)->whereNull('member_id')->whereNull('deleted_at')->get();
        self::same(count($rows), 1);
        $invoice = $rows[0];
        self::same(InvoiceLedger::cents($data['cost'] ?? 0), InvoiceLedger::cents($invoice['payable_amount']));
        self::same((int) ($data['discountId'] ?? 0), (int) ($invoice['discount_id'] ?? 0));
        self::same((int) ($data['currencyId'] ?? 0), (int) $invoice['currency_id']);
        $count = DB::table('academy_branch_course_term_invoice_installments')->where('invoice_id', (int) $invoice['term_invoice_id'])->whereNull('deleted_at')->count();
        self::same((int) ($data['installmentCount'] ?? 1), (int) $count);
    }

    public static function assertDeletable(int $id): void
    {
        foreach (['academy_branch_course_term_enrollments', 'academy_branch_course_term_sessions', 'academy_branch_course_term_invoices'] as $table) {
            if (DB::table($table)->where('term_id', $id)->first()) {
                throw new RuntimeException('ترم دارای سوابق آموزشی یا مالی است؛ حذف آن مجاز نیست.', 409);
            }
        }
    }

    public static function assertNoHistory(string $table, string $column, int $id): void
    {
        if (DB::table($table)->where($column, $id)->first()) {
            throw new RuntimeException('این رکورد دارای سوابق وابسته است؛ به‌جای حذف، وضعیت آن را غیرفعال کنید.', 409);
        }
    }

    public static function assertSessionEditable(int $id): void
    {
        if (DB::table('academy_branch_course_term_session_attendances')->where('session_id', $id)->first()) {
            throw new RuntimeException('جلسه دارای سابقه حضور و غیاب است و نمی‌توان زمان یا هویت آن را تغییر داد یا آن را حذف کرد.', 409);
        }
        $session = DB::table('academy_branch_course_term_sessions')->where('term_session_id', $id)->first();
        if ($session && (($session['session_type'] ?? 'regular') === 'makeup' || ($session['cancellation_status'] ?? 'none') !== 'none')) {
            throw new RuntimeException('سوابق لغو و جبرانی باید از مسیر اختصاصی مدیریت شوند.', 409);
        }
    }

    private static function same(mixed $incoming, mixed $stored): void
    {
        if ($incoming !== $stored) {
            throw new RuntimeException('برای حفظ سوابق، ساختار ترم ثبت‌شده از این فرم قابل بازسازی نیست. تغییر جلسات و فاکتور را از بخش مربوط انجام دهید؛ در این فرم فقط عنوان و توضیحات را ویرایش کنید.', 409);
        }
    }
}
