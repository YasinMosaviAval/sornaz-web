<?php
namespace Modules\Analytics\Controllers\Web;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminGuideService;

class AdminGuideController {
    public function __construct(private AdminGuideService $service) {}
    public function save(){try{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new \RuntimeException('اطلاعات راهنما معتبر نیست.');$this->service->save((int)auth()->id(),(string)($data['key']??''),$data);return ResponseFactory::json(['success'=>true]);}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
