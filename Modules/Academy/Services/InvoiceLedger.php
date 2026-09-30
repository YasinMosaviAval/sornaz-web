<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

final class InvoiceLedger
{
    public static function cents(mixed $amount): int
    {
        if (!is_scalar($amount) || !preg_match('/^\d{1,12}(?:\.\d{1,2})?$/D', (string) $amount)) {
            throw new RuntimeException('مبلغ باید عدد نامنفی با حداکثر دو رقم اعشار باشد.', 422);
        }
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');
        return (int) $whole * 100 + (int) str_pad($fraction, 2, '0');
    }

    public static function amount(int $cents): string
    {
        return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function share(int $total, int $count, int $index): string
    {
        $unit = $total % 100 === 0 ? 100 : 1;
        $units = intdiv($total, $unit);
        return self::amount((intdiv($units, $count) + ($index < $units % $count ? 1 : 0)) * $unit);
    }

    public static function paymentAmount(mixed $amount): int
    {
        $cents = self::cents($amount);
        if ($cents < 100 || $cents % 100 !== 0) {
            throw new RuntimeException('مبلغ پرداخت ریالی یا تومانی باید عدد صحیح مثبت باشد.', 422);
        }
        return intdiv($cents, 100);
    }

    public static function create(int $termId, int $actor, array $data, string $start, int $currency): void
    {
        $total = self::cents($data['cost'] ?? 0);
        $count = (int) ($data['installmentCount'] ?? 1);
        if ($count < 1 || $count > max(2, count($data['sessions'] ?? [])) || $count > 1000) {
            throw new RuntimeException('تعداد اقساط معتبر نیست.', 422);
        }
        $id = DB::table('academy_branch_course_term_invoices')->insertGetId(['term_id' => $termId, 'discount_id' => (int) ($data['discountId'] ?? 0) ?: null, 'payable_amount' => self::amount($total), 'currency_id' => $currency, 'status' => 'draft', 'due_date' => $start, 'created_by' => $actor, 'updated_by' => $actor]);
        for ($index = 0; $index < $count; ++$index) {
            DB::table('academy_branch_course_term_invoice_installments')->insert(['invoice_id' => $id, 'installment_number' => $index + 1, 'amount' => self::share($total, $count, $index), 'due_date' => date('Y-m-d', strtotime($start . ' +' . $index . ' month')), 'status' => 'pending', 'created_by' => $actor, 'updated_by' => $actor]);
        }
    }

    public static function snapshot(int $id): array
    {
        $invoice = DB::table('academy_branch_course_term_invoices')->where('term_invoice_id', $id)->whereNull('deleted_at')->first();
        if (!$invoice) {
            throw new RuntimeException('فاکتور یافت نشد.', 404);
        }
        $rows = DB::table('academy_branch_course_term_invoice_installments')->where('invoice_id', $id)->whereNull('deleted_at')->orderBy('installment_number')->orderBy('term_invoice_installment_id')->get();
        $total = $paid = 0;
        foreach ($rows as $row) {
            $amount = self::cents($row['amount']);
            $total += $amount;
            if ($row['status'] === 'paid') {
                $paid += $amount;
            } elseif (!in_array($row['status'], ['pending', 'approved', 'overdue'], true)) {
                throw new RuntimeException('وضعیت اقساط نیازمند بررسی مالی است.', 409);
            }
        }
        if (!$rows || $total !== self::cents($invoice['payable_amount'])) {
            throw new RuntimeException('جمع اقساط با مبلغ فاکتور یکسان نیست؛ ابتدا مغایرت مالی را بررسی کنید.', 409);
        }
        return compact('invoice', 'rows', 'total', 'paid');
    }

    public static function refresh(int $id, int $actor): void
    {
        $state = self::snapshot($id);
        $status = $state['paid'] === $state['total'] ? 'paid' : ($state['paid'] > 0 ? 'partial' : 'issued');
        DB::table('academy_branch_course_term_invoices')->where('term_invoice_id', $id)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor]);
    }

    public static function revise(int $id, int $actor, array $data): void
    {
        $state = self::snapshot($id);
        $amount = self::cents($data['amount'] ?? $state['invoice']['payable_amount']);
        $requested = (string) ($data['statusCode'] ?? $state['invoice']['status']);
        $attempt = DB::table('academy_term_invoice_payments')->where('invoice_id', $id)->first();
        $hasPaid = (bool) array_filter($state['rows'], fn ($r) => $r['status'] === 'paid');
        if (($attempt || $hasPaid) && ($amount !== $state['total'] || $requested !== $state['invoice']['status'])) {
            throw new RuntimeException('فاکتور دارای سابقه پرداخت است؛ مبلغ و وضعیت آن دستی تغییر نمی‌کند.', 409);
        }
        if (!$attempt && !$hasPaid && !in_array($requested, ['draft', 'issued', 'canceled'], true)) {
            throw new RuntimeException('وضعیت پرداخت فقط از پرداخت ثبت‌شده محاسبه می‌شود.', 422);
        }
        if ($amount !== $state['total']) {
            $count = count($state['rows']);
            foreach ($state['rows'] as $index => $row) {
                DB::table('academy_branch_course_term_invoice_installments')->where('term_invoice_installment_id', (int) $row['term_invoice_installment_id'])->update(['amount' => self::share($amount, $count, $index), 'updated_by' => $actor]);
            }
        }
        DB::table('academy_branch_course_term_invoices')->where('term_invoice_id', $id)->update(['payable_amount' => self::amount($amount), 'status' => $requested, 'due_date' => ($data['dueDate'] ?? '') ?: null, 'updated_by' => $actor]);
    }
}
