<?php
namespace Modules\CourseMarket\Services;

use Modules\CourseMarket\Repositories\CourseRepository;
use RuntimeException;

class PaymentService
{
    public function __construct(private CourseRepository $repo, private CourseService $courses) {}

    public function start(int $actor, int $id): array
    {
        if ($actor < 1) throw new RuntimeException('ابتدا وارد حساب کاربری شوید.', 401);
        $course = $this->courses->course($id);
        if ($course['status'] !== 'published') throw new RuntimeException('دوره برای فروش فعال نیست.', 422);
        if ($this->courses->hasAccess($actor, $course)) return ['redirectUrl'=>'/course-market/courses/'.$id];
        $amount = (int)$course['price'];
        if ($amount > 0 && (!env('ZARINPAL_MERCHANT_ID') || !filter_var(env('APP_URL'), FILTER_VALIDATE_URL))) throw new RuntimeException('درگاه پرداخت هنوز پیکربندی نشده است.', 503);
        $token = bin2hex(random_bytes(32));
        $order = $this->repo->insert('creator_course_orders', ['course_id'=>$id,'buyer_id'=>$actor,'seller_id'=>$course['owner_id'],'amount'=>$amount,'currency'=>'IRT','status'=>$amount ? 'created' : 'paid','token'=>$token,'paid_at'=>$amount ? null : date('Y-m-d H:i:s')]);
        if (!$amount) return ['redirectUrl'=>'/course-market/courses/'.$id];
        try {
            $result = $this->request('request', ['merchant_id'=>env('ZARINPAL_MERCHANT_ID'),'amount'=>$amount,'currency'=>'IRT','callback_url'=>rtrim(env('APP_URL'), '/').'/course-market/payment/callback?token='.$token,'description'=>'خرید دوره '.$course['title']]);
            $authority = (string)($result['data']['authority'] ?? '');
            if ((int)($result['data']['code'] ?? 0) !== 100 || !preg_match('/^[A-Za-z0-9_-]{10,100}$/D', $authority)) throw new RuntimeException('ایجاد درخواست پرداخت ناموفق بود.', 502);
            $this->repo->query("UPDATE creator_course_orders SET authority=?,status='pending' WHERE id=?", [$authority,$order]);
            return ['redirectUrl'=>$this->host(true).'/pg/StartPay/'.$authority];
        } catch (\Throwable $e) {
            $this->repo->query("UPDATE creator_course_orders SET status='failed' WHERE id=?", [$order]);
            throw $e;
        }
    }

    public function callback(string $token, string $authority, string $status): array
    {
        $order = $this->repo->one('SELECT * FROM creator_course_orders WHERE token=?', [$token]);
        if (!$order || !$order['authority'] || !hash_equals($order['authority'], $authority)) throw new RuntimeException('شناسه پرداخت معتبر نیست.', 422);
        if ($order['status'] === 'paid') return $order;
        if (strtoupper($status) !== 'OK') throw new RuntimeException('پرداخت لغو شد. می‌توانید دوباره اقدام کنید.', 422);
        $result = $this->request('verify', ['merchant_id'=>env('ZARINPAL_MERCHANT_ID'),'amount'=>(int)$order['amount'],'authority'=>$authority]);
        if (!in_array((int)($result['data']['code'] ?? 0), [100,101], true) || empty($result['data']['ref_id'])) throw new RuntimeException('پرداخت تأیید نشد؛ دسترسی به دوره فعال نشده است.', 422);
        $this->repo->transaction(function () use ($order, $result) {
            $fresh = $this->repo->one('SELECT status FROM creator_course_orders WHERE id=? FOR UPDATE', [$order['id']]);
            if ($fresh['status'] !== 'paid') $this->repo->query("UPDATE creator_course_orders SET status='paid',reference_id=?,paid_at=CURRENT_TIMESTAMP WHERE id=?", [(string)$result['data']['ref_id'],$order['id']]);
        });
        return $this->repo->one('SELECT * FROM creator_course_orders WHERE id=?', [$order['id']]);
    }

    private function host(bool $checkout = false): string
    {
        return filter_var(env('ZARINPAL_SANDBOX', false), FILTER_VALIDATE_BOOL) ? 'https://sandbox.zarinpal.com' : ($checkout ? 'https://www.zarinpal.com' : 'https://api.zarinpal.com');
    }

    private function request(string $action, array $payload): array
    {
        $curl = curl_init($this->host().'/pg/v4/payment/'.$action.'.json');
        curl_setopt_array($curl, [CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode($payload, JSON_UNESCAPED_UNICODE),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>30,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json']]);
        $raw = curl_exec($curl);
        $http = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = is_string($raw) ? json_decode($raw, true) : null;
        if ($http !== 200 || !is_array($result)) throw new RuntimeException('ارتباط با درگاه برقرار نشد. دوباره تلاش کنید.', 502);
        return $result;
    }
}
