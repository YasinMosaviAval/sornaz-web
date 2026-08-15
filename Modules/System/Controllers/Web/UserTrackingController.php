<?php

namespace Modules\System\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\System\Services\UserTrackingService;

final class UserTrackingController
{
    public function __construct(private UserTrackingService $service) {}

    public function ingest()
    {
        try {
            $raw=file_get_contents('php://input');
            if(strlen((string)$raw)>524288) return ResponseFactory::json(['success'=>false,'message'=>'حجم بسته رهگیری بیش از حد مجاز است.'],413);
            $data=json_decode($raw?:'',true);
            if(!is_array($data)) $data=$_POST;
            $token=(string)($data['_token']??($_SERVER['HTTP_X_CSRF_TOKEN']??''));
            if(!csrf()->verify($token)) return ResponseFactory::json(['success'=>false,'message'=>'CSRF Token Mismatch'],419);
            return ResponseFactory::json(['success'=>true,'data'=>$this->service->ingest(auth()->id(),$data)]);
        } catch (\Throwable $e) {
            error_log('[User Tracking] '.$e->getMessage());
            return ResponseFactory::json(['success'=>false,'message'=>'ثبت اطلاعات رهگیری ناموفق بود.'],422);
        }
    }
}
