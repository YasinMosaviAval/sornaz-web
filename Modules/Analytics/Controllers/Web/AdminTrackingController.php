<?php
namespace Modules\Analytics\Controllers\Web;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTrackingService;
final class AdminTrackingController{
    public function __construct(private AdminTrackingService $service){}
    public function index(){try{return ResponseFactory::json(['success'=>true,'data'=>$this->service->data($_GET)]);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
