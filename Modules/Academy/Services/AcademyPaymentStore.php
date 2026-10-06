<?php

namespace Modules\Academy\Services;

use Core\database\Builder;
use Core\database\DB;

/** Tuition requests share the generic store but never select unrelated receipts. */
final class AcademyPaymentStore
{
    public static function query(): Builder
    {
        return DB::table('financial_system_payments')->where('record_type', 'academy_term');
    }

    public static function create(array $data): int
    {
        $data['record_type'] = 'academy_term';
        $data['payer_id'] = $data['user_id'];
        unset($data['user_id']);
        $data['method'] = $data['payment_method'] ?? 'online';
        $data['gateway'] = $data['gateway'] ?? 'zarinpal';
        $data['reference_code'] = $data['reference_id'] ?? null;
        $data['paid_at'] = ($data['status'] ?? '') === 'paid' ? ($data['verified_at'] ?? null) : null;
        $currency = DB::table('financial_system_currency')->where('code', $data['currency'])->first();
        $data['currency_id'] = $currency['currency_id'] ?? null;
        $data['gateway'] = $data['gateway'] ?? 'zarinpal';
        $data['reference_code'] = $data['reference_id'] ?? null;
        $data['paid_at'] = ($data['status'] ?? '') === 'paid' ? ($data['verified_at'] ?? null) : null;
        unset($data['payment_method']);
        return (int) self::query()->insertGetId($data);
    }
}
