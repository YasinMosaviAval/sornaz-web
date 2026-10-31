<?php
namespace Modules\Education\Services;

use Modules\System\Services\PaymentMutex;
use Modules\System\Services\ZarinpalGateway;
use RuntimeException;

final class MusicPracticePayment
{
    public function __construct(private MusicPracticeStore $store,private ZarinpalGateway $gateway) {}
    public function start(int $actor,int $planId,string $checkoutKey,int $planVersion): array
    {
        if($actor<1)throw new RuntimeException('ابتدا وارد شوید.',401);
        if(!preg_match('/^[a-f0-9]{32}$/D',$checkoutKey))throw new RuntimeException('شناسهٔ درخواست خرید معتبر نیست.',422);
        $title=\Core\translation\EntityText::expression('music_practice_plans','p.id','title');
        $plan=$this->store->one("SELECT p.*,$title title FROM f_music_practice_plans p WHERE p.id=? AND p.status='approved' AND p.teacher_bps IS NOT NULL",[$planId]);
        if(!$plan)throw new RuntimeException('پلن تأییدشده پیدا نشد.',404);
        if($plan['exam_id'])throw new RuntimeException('خرید سطح تکی متوقف شده است؛ اشتراک کل آزمون یا مدرس را انتخاب کنید.',422);
        if((int)$plan['version']!==$planVersion)throw new RuntimeException('قیمت یا پلن تغییر کرده است؛ پیشنهاد را دوباره دریافت کنید.',409);
        $this->validateScope($plan);
        if((int)$plan['teacher_id']===$actor)throw new RuntimeException('این پلن متعلق به شماست.',422);
        if($plan['exam_id']){
            $exam=$this->store->one('SELECT * FROM f_music_practice_exams WHERE id=? AND owner_id=? AND published=1',[$plan['exam_id'],$plan['teacher_id']]);
            if(!$exam)throw new RuntimeException('آزمون برای فروش فعال نیست.',422);
            if((new MusicPracticeAccess($this->store))->permitted($actor,$exam))return ['redirectUrl'=>'/exams'];
            $plan['teacher_bps']=$exam['teacher_bps'];
        }
        return PaymentMutex::run($this->store->connection(),$this->resource($actor,$plan),fn()=>$this->startLocked($actor,$plan,$checkoutKey));
    }
    private function startLocked(int $actor,array $plan,string $checkoutKey): array
    {
        $intent=$this->store->one('SELECT * FROM f_music_practice_orders WHERE buyer_id=? AND checkout_key=?',[$actor,$checkoutKey]);
        if($intent&&(int)$intent['plan_id']!==(int)$plan['id'])throw new RuntimeException('شناسهٔ خرید قبلاً استفاده شده است.',409);
        if($intent&&$intent['status']==='paid')return ['redirectUrl'=>'/exams'];
        $fresh=$this->store->one("SELECT version FROM f_music_practice_plans WHERE id=? AND status='approved'",[$plan['id']]);
        if(!$fresh||(int)$fresh['version']!==(int)$plan['version'])throw new RuntimeException('پلن تغییر کرده است؛ دوباره دریافت کنید.',409);
        $this->validateScope($plan);
        if($plan['exam_id']){
            $exam=$this->store->one('SELECT * FROM f_music_practice_exams WHERE id=? AND owner_id=? AND published=1',[$plan['exam_id'],$plan['teacher_id']]);
            if(!$exam)throw new RuntimeException('آزمون برای فروش فعال نیست.',422);
            if((new MusicPracticeAccess($this->store))->permitted($actor,$exam))return ['redirectUrl'=>'/exams'];
            $plan['teacher_bps']=$exam['teacher_bps'];
        }
        $resume=$this->resumePending($actor,$plan);
        if($resume)return $resume;
        if($intent)throw new RuntimeException('این درخواست خرید قبلاً ثبت شده است؛ پیشنهاد را دوباره باز کنید.',409);
        return $this->createOrder($actor,$plan,$checkoutKey);
    }
    private function resumePending(int $actor,array $plan): ?array
    {
        $scope=$plan['exam_id']?'exam_id=?':'exam_id IS NULL';
        $nodeScope=$this->store->hasNodeScope('music_practice_orders')?' AND COALESCE(node_id,0)=?':'';
        $pending=$this->store->one("SELECT * FROM f_music_practice_orders WHERE buyer_id=? AND teacher_id=? AND $scope$nodeScope AND (status IN ('created','pending','uncertain') OR (status IN ('failed','canceled') AND authority IS NOT NULL)) ORDER BY id DESC LIMIT 1",[$actor,$plan['teacher_id'],...($plan['exam_id']?[$plan['exam_id']]:[]),...($nodeScope?[(int)($plan['node_id']??0)]:[])]);
        if($pending)$pending['_plan_matches']=(int)$pending['plan_id']===(int)$plan['id']&&(int)$pending['days']===(int)$plan['days']&&(int)$pending['teacher_bps']===(int)$plan['teacher_bps'];
        $resume=PaymentMutex::reconcile($pending,(int)$plan['amount'],fn($authority)=>$this->gateway->request('inquiry',['merchant_id'=>env('ZARINPAL_MERCHANT_ID'),'authority'=>$authority]),function($order){$this->callbackLocked($order['token'],$order['authority'],'OK');return ['redirectUrl'=>'/exams'];},fn($order)=>$this->store->execute("UPDATE f_music_practice_orders SET status='failed' WHERE id=? AND status<>'paid'",[$order['id']]));
        return $resume;
    }
    private function createOrder(int $actor,array $plan,string $checkoutKey): array
    {
        if(!env('ZARINPAL_MERCHANT_ID')||!filter_var(env('APP_URL'),FILTER_VALIDATE_URL)||strtolower((string)parse_url(env('APP_URL'),PHP_URL_SCHEME))!=='https')throw new RuntimeException('درگاه امن پرداخت هنوز آماده نیست.',503);
        $token=bin2hex(random_bytes(32));
        $allocation=[['examId'=>(int)$plan['exam_id'],'amount'=>(int)$plan['amount'],'teacherBps'=>(int)$plan['teacher_bps']]];
        $fields=['plan_id'=>$plan['id'],'buyer_id'=>$actor,'checkout_key'=>$checkoutKey,'teacher_id'=>$plan['teacher_id'],'exam_id'=>$plan['exam_id'],'amount'=>$plan['amount'],'days'=>$plan['days'],'teacher_bps'=>$plan['teacher_bps'],'allocations'=>json_encode($allocation,JSON_THROW_ON_ERROR),'token'=>$token,'created_at'=>$this->store->now()];
        $id=$this->insertOrder($fields,$plan['node_id']??null);
        try{
            $result=$this->gateway->request('request',['merchant_id'=>env('ZARINPAL_MERCHANT_ID'),'amount'=>(int)$plan['amount'],'currency'=>'IRT','callback_url'=>rtrim(env('APP_URL'),'/').'/exams/payment/callback?token='.$token,'description'=>'اشتراک آزمون‌های مدرس - '.$plan['title']]);
            $authority=$result['data']['authority']??'';
            if((int)($result['data']['code']??0)!==100||!is_string($authority)||!preg_match('/^[A-Za-z0-9_-]{10,100}$/D',$authority))throw new RuntimeException('پاسخ درگاه معتبر نیست.',502);
            $this->store->execute("UPDATE f_music_practice_orders SET authority=?,status='pending' WHERE id=?",[$authority,$id]);
            return ['redirectUrl'=>$this->gateway->host(true).'/pg/StartPay/'.rawurlencode($authority)];
        }catch(\Throwable $e){$this->store->execute("UPDATE f_music_practice_orders SET status='uncertain' WHERE id=? AND status='created'",[$id]);throw $e;}
    }
    private function insertOrder(array $fields,?int $node): int
    {
        if($this->store->hasNodeScope('music_practice_orders'))$fields['node_id']=$node;
        $this->store->execute('INSERT INTO f_music_practice_orders('.implode(',',array_keys($fields)).') VALUES('.implode(',',array_fill(0,count($fields),'?')).')',array_values($fields));
        return (int)$this->store->connection()->lastInsertId();
    }
    public function callback(string $token,string $authority,string $status): array
    {
        if(!preg_match('/^[a-f0-9]{64}$/D',$token))throw new RuntimeException('شناسهٔ پرداخت معتبر نیست.',422);
        $order=$this->store->one('SELECT * FROM f_music_practice_orders WHERE token=?',[$token]);
        if(!$order)throw new RuntimeException('پرداخت پیدا نشد.',404);
        return PaymentMutex::run($this->store->connection(),$this->resource((int)$order['buyer_id'],$order),fn()=>$this->callbackLocked($token,$authority,$status));
    }
    private function resource(int $actor,array $product): string {return 'music-practice:'.$actor.':'.$product['teacher_id'].':'.($product['exam_id']??(!empty($product['node_id'])?'vip-node-'.$product['node_id']:'vip'));}
    private function validateScope(array $plan): void
    {
        if(empty($plan['node_id']))return;
        if(!$this->store->supportsSectionSubscriptions())throw new RuntimeException('ساختار اشتراک بخش‌ها آماده نیست.',503);
        if($plan['exam_id']||!$this->store->one('SELECT id FROM f_music_practice_nodes WHERE id=? AND published=1'.$this->store->liveNodeSql(),[$plan['node_id']]))throw new RuntimeException('بخش برای فروش اشتراک فعال نیست.',422);
    }
    private function callbackLocked(string $token,string $authority,string $status): array
    {
        $order=$this->store->one('SELECT * FROM f_music_practice_orders WHERE token=?',[$token]);
        if(!$order||!$order['authority']||!hash_equals($order['authority'],$authority))throw new RuntimeException('شناسهٔ تراکنش معتبر نیست.',422);
        if($order['status']==='paid')return ['paid'=>true,'orderId'=>(int)$order['id']];
        if(strtoupper($status)!=='OK'){$this->store->execute("UPDATE f_music_practice_orders SET status='canceled' WHERE id=? AND status<>'paid'",[$order['id']]);throw new RuntimeException('پرداخت لغو شد.',422);}
        $result=$this->gateway->request('verify',['merchant_id'=>env('ZARINPAL_MERCHANT_ID'),'amount'=>(int)$order['amount'],'authority'=>$authority]);
        if(!in_array((int)($result['data']['code']??0),[100,101],true)||empty($result['data']['ref_id']))throw new RuntimeException('پرداخت تأیید نشد؛ اشتراک فعال نشده است.',422);
        return $this->store->transaction(fn()=>$this->settle($order,$result));
    }
    private function settle(array $order,array $result): array
    {
            $fresh=$this->store->one('SELECT * FROM f_music_practice_orders WHERE id=?'.$this->store->lock(),[$order['id']]);
            if($fresh['status']==='paid')return ['paid'=>true,'orderId'=>(int)$order['id']];
            $nodeScope=$this->store->hasNodeScope('music_practice_orders')?' AND COALESCE(node_id,0)=?':'';
            $last=$this->store->one("SELECT MAX(expires_at) expires_at FROM f_music_practice_orders WHERE buyer_id=? AND teacher_id=? AND exam_id IS NULL AND status='paid'$nodeScope",[$order['buyer_id'],$order['teacher_id'],...($nodeScope?[(int)($order['node_id']??0)]:[])]);
            $starts=$order['exam_id']?$this->store->now():max($this->store->now(),$last['expires_at']??'');
            $expires=$order['exam_id']?null:gmdate('Y-m-d H:i:s',strtotime($starts.' UTC')+(int)$order['days']*86400);
            $this->store->execute("UPDATE f_music_practice_orders SET status='paid',reference_id=?,paid_at=?,starts_at=?,expires_at=? WHERE id=?",[(string)$result['data']['ref_id'],$this->store->now(),$starts,$expires,$order['id']]);
            $teacher=intdiv((int)$order['amount']*(int)$order['teacher_bps'],10000);
            foreach([['teacher',(int)$order['teacher_id'],$teacher],['platform',1,(int)$order['amount']-$teacher]] as [$party,$user,$amount])$this->store->execute('INSERT INTO f_music_practice_revenue(order_id,exam_id,beneficiary,user_id,amount,currency,created_at) VALUES(?,?,?,?,?,?,?)',[$order['id'],(int)$order['exam_id'],$party,$user,$amount,'IRT',$this->store->now()]);
            return ['paid'=>true,'orderId'=>(int)$order['id']];
    }
}
