<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\Academy\Services\AcademyPaymentStore;

final class PersonalPanelDataService
{
    public function lessons(int $actor, bool $decorate = true): array
    {
        $items = DB::table('user_lessons')->where('user_id', $actor)->whereNull('deleted_at')->orderBy('user_lesson_id', 'DESC')->get();
        $lessons = DB::table('lessons')->whereNull('deleted_at')->orderBy('lesson_id')->get();
        $levels = DB::table('levels')->where('type', 'learning')->where('is_active', 1)->whereNull('deleted_at')->orderBy('sort_order')->get();
        return [
            'items' => $decorate ? array_map(fn (array $item): array => ['id' => (int) $item['user_lesson_id'], 'title' => $this->text('lessons', (int) $item['lesson_id'], 'title', 'درس ' . $item['lesson_id']), 'summary' => $this->text('user_lessons', (int) $item['user_lesson_id'], 'summary'), 'description' => $this->text('user_lessons', (int) $item['user_lesson_id'], 'description')] + $item, $items) : $items,
            'lessons' => $decorate ? array_map(fn (array $item): array => ['id' => (int) $item['lesson_id'], 'title' => $this->text('lessons', (int) $item['lesson_id'], 'title', 'درس ' . $item['lesson_id'])] + $item, $lessons) : $lessons,
            'levels' => $decorate ? array_map(fn (array $item): array => ['id' => (int) $item['level_id'], 'title' => $this->text('levels', (int) $item['level_id'], 'title', 'سطح ' . $item['level_id'])] + $item, $levels) : $levels,
        ];
    }

    public function schedules(int $actor, bool $decorate = true): array
    {
        $items = DB::table('user_availabilities')->where('user_id', $actor)->whereNull('unavailable_type')->whereNull('deleted_at')->orderBy('user_availability_id', 'DESC')->get();
        $timezones = DB::table('f_timezone')->where('status', 'active')->whereNull('deleted_at')->orderBy('sort_order')->get();
        return [
            'items' => $decorate ? $this->scheduleGroups($items, $timezones) : $items,
            'timezones' => $timezones,
        ];
    }

    public function finance(int $actor, bool $decorate = true): array
    {
        $members = DB::table('academy_branch_members')->where('user_id', $actor)->whereNull('deleted_at')->get();
        $memberIds = array_map(static fn (array $member): int => (int) $member['member_id'], $members);
        $invoices = $memberIds ? DB::table('academy_branch_course_term_invoices')->whereIn('member_id', $memberIds)->whereNull('deleted_at')->orderBy('term_invoice_id', 'DESC')->get() : [];
        $invoiceIds = array_map(static fn (array $invoice): int => (int) $invoice['term_invoice_id'], $invoices);
        $installments = $invoiceIds ? DB::table('academy_branch_course_term_invoice_installments')->whereIn('invoice_id', $invoiceIds)->whereNull('deleted_at')->orderBy('term_invoice_installment_id', 'DESC')->get() : [];
        $payments = $invoiceIds ? AcademyPaymentStore::query()->whereIn('invoice_id', $invoiceIds)->whereNull('deleted_at')->orderBy('payment_id', 'DESC')->get() : [];
        $locked = [];
        foreach ($payments as $payment) $locked[(int) $payment['invoice_id']] = true;
        foreach ($installments as $installment) {
            if (in_array((string) ($installment['status'] ?? ''), ['paid', 'partially_paid'], true)) $locked[(int) $installment['invoice_id']] = true;
        }
        return [
            'invoices' => $decorate ? array_map(fn (array $invoice): array => ['id' => (int) $invoice['term_invoice_id'], 'amount' => (string) $invoice['payable_amount'], 'statusCode' => (string) $invoice['status'], 'dueDate' => $invoice['due_date'], 'readOnly' => !empty($locked[(int) $invoice['term_invoice_id']]) || !in_array((string) $invoice['status'], ['draft', 'issued', 'canceled'], true), 'title' => $this->text('academy_branch_course_term_invoices', (int) $invoice['term_invoice_id'], 'title', 'فاکتور ' . $invoice['term_invoice_id']), 'summary' => $this->text('academy_branch_course_term_invoices', (int) $invoice['term_invoice_id'], 'summary'), 'description' => $this->text('academy_branch_course_term_invoices', (int) $invoice['term_invoice_id'], 'description')] + $invoice, $invoices) : $invoices,
            'installments' => $installments,
            'payments' => $payments,
        ];
    }

    private function text(string $table, int $id, string $field, string $fallback = ''): string
    {
        $translations = \Core\translation\TranslationService::manager();
        return (string) ($translations->get($table, $id, $field, locale()) ?: $translations->get($table, $id, $field, 'fa') ?: $fallback);
    }

    private function scheduleGroups(array $items, array $timezones): array
    {
        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه', 'thursday' => 'پنجشنبه', 'friday' => 'جمعه'];
        $repeats = ['week' => 'هفتگی', '2-week' => 'دو هفته', '3-week' => 'سه هفته', '4-week' => 'چهار هفته', 'month' => 'ماهانه', 'year' => 'سالانه', 'none' => 'بی‌تکرار'];
        $zones = array_column($timezones, 'timezone', 'timezone_id');
        $groups = [];
        foreach ($items as $item) {
            $key = implode('|', [(string) ($item['repeat_period'] ?? ''), (string) ($item['date'] ?? ''), (string) ($item['day_of_week'] ?? '')]);
            $id = (int) $item['user_availability_id'];
            if (!isset($groups[$key])) {
                $groups[$key] = ['id' => $id, 'title' => (string) (($item['date'] ?? '') ?: ($days[$item['day_of_week'] ?? ''] ?? 'برنامه زمانی')), 'day' => $days[$item['day_of_week'] ?? ''] ?? 'شنبه', 'repeatPeriod' => $repeats[$item['repeat_period'] ?? ''] ?? 'هفتگی', 'repeatDate' => (string) ($item['date'] ?? ''), 'timezone' => $zones[(int) ($item['timezone_id'] ?? 0)] ?? 'Asia/Tehran', 'ranges' => [], 'summary' => $this->text('user_availabilities', $id, 'summary'), 'description' => $this->text('user_availabilities', $id, 'description'), 'readOnly' => false];
            }
            $groups[$key]['ranges'][] = ['start' => substr((string) ($item['start_time'] ?? ''), 0, 5), 'end' => substr((string) ($item['end_time'] ?? ''), 0, 5), 'status' => ($item['status'] ?? '') === 'unavailable' ? 'غیرفعال' : 'فعال'];
            if (in_array((string) ($item['status'] ?? ''), ['reserved', 'pending'], true)) $groups[$key]['readOnly'] = true;
        }
        return array_values($groups);
    }
}
