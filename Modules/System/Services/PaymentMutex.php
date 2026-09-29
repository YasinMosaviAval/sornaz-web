<?php
namespace Modules\System\Services;

use PDO;
use RuntimeException;

/** Serialize all payment transitions for one purchase on the database server. */
final class PaymentMutex
{
    private static array $held = [];

    public static function run(PDO $db, string $resource, callable $work): mixed
    {
        $key = 'sornaz:' . substr(hash('sha256', $resource), 0, 56);
        $local = spl_object_id($db) . ':' . $key;
        if (isset(self::$held[$local])) {
            return $work();
        }
        $mysql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
        $file = null;
        if ($mysql) {
            $query = $db->prepare('SELECT GET_LOCK(?, 5)');
            $query->execute([$key]);
            if ((int) $query->fetchColumn() !== 1) {
                throw new RuntimeException('پرداخت در حال پردازش است. کمی بعد دوباره تلاش کنید.', 409);
            }
        } else {
            // SQLite fixture/local deployments use a process-shared filesystem lock.
            $directory = storage_path('payment-locks');
            if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
                throw new RuntimeException('Payment lock directory unavailable');
            }
            $file = fopen($directory . '/' . hash('sha256', $key) . '.lock', 'c');
            if (!$file || !flock($file, LOCK_EX | LOCK_NB)) {
                if ($file) {
                    fclose($file);
                }
                throw new RuntimeException('Payment is already being processed', 409);
            }
        }
        self::$held[$local] = true;
        try {
            return $work();
        } finally {
            unset(self::$held[$local]);
            if ($mysql) {
                $query = $db->prepare('SELECT RELEASE_LOCK(?)');
                $query->execute([$key]);
            } else {
                flock($file, LOCK_UN);
                fclose($file);
            }
        }
    }

    public static function resume(?array $pending, int $amount): ?array
    {
        if (!$pending) {
            return null;
        }
        if ((int) $pending['amount'] !== $amount || ($pending['_plan_matches'] ?? true) === false) {
            throw new RuntimeException('یک پرداخت با مبلغ یا پلن قبلی در انتظار تعیین تکلیف است.', 409);
        }
        if (empty($pending['authority'])) {
            throw new RuntimeException('درخواست قبلی هنوز تعیین تکلیف نشده است. با پشتیبانی تماس بگیرید.', 409);
        }
        $host = filter_var(env('ZARINPAL_SANDBOX', false), FILTER_VALIDATE_BOOL) ? 'https://sandbox.zarinpal.com' : 'https://www.zarinpal.com';
        return ['redirectUrl' => $host . '/pg/StartPay/' . rawurlencode($pending['authority'])];
    }

    public static function reconcile(?array $pending, int $amount, callable $inquire, callable $paid, callable $closed): ?array
    {
        if (!$pending) {
            return null;
        }
        if (empty($pending['authority'])) {
            return self::resume($pending, $amount);
        }
        $result = $inquire((string) $pending['authority']);
        if ((int) ($result['data']['code'] ?? 0) !== 100) {
            throw new RuntimeException('وضعیت پرداخت قبلی مشخص نشد. دوباره پرداخت نکنید و کمی بعد تلاش کنید.', 503);
        }
        $status = (string) ($result['data']['status'] ?? '');
        if (in_array($status, ['PAID', 'VERIFIED'], true)) {
            return $paid($pending);
        }
        if (in_array($status, ['FAILED', 'REVERSED'], true)) {
            $closed($pending);
            return null;
        }
        if ($status === 'IN_BANK') {
            return self::resume($pending, $amount);
        }
        throw new RuntimeException('وضعیت پرداخت قبلی نیازمند بررسی است.', 503);
    }
}
