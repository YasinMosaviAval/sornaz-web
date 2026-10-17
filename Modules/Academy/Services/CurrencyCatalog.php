<?php
namespace Modules\Academy\Services;

use Core\database\DB;

final class CurrencyCatalog
{
    public static function all(): array
    {
        $english = locale() === 'en';
        $out = [];
        foreach (DB::table('financial_system_currency')->get() as $row) {
            $code = strtoupper((string) ($row['code'] ?? ''));
            $names = ['IRR'=>['تومان','Toman'], 'IRT'=>['تومان','Toman'], 'TMN'=>['تومان','Toman'], 'USD'=>['دلار','US Dollar'], 'EUR'=>['یورو','Euro'], 'GBP'=>['پوند','British Pound'], 'PND'=>['پوند','British Pound']];
            $text = DB::table('f_translations')->where('table_name', 'financial_system_currency')->where('table_id', (int) $row['currency_id'])->where('field', 'title')->where('locale', $english ? 'en' : 'fa')->whereNull('deleted_at')->orderBy('translation_id', 'desc')->first();
            $name = trim((string) ($text['value'] ?? ''));
            if ($name === '' || strtoupper($name) === $code) $name = $names[$code][$english ? 1 : 0] ?? $code;
            $out[] = ['id'=>(int) $row['currency_id'], 'name'=>$name];
        }
        return $out;
    }
}
