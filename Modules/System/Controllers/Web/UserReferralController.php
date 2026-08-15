<?php
namespace Modules\System\Controllers\Web;
use Core\http\ResponseFactory;use Modules\System\Services\UserReferralService;
class UserReferralController{public function __construct(private UserReferralService $service){}public function show(){try{return ResponseFactory::json(['success'=>true,'data'=>$this->service->details((int)auth()->id())]);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}}
