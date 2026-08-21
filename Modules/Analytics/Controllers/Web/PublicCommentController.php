<?php
namespace Modules\Analytics\Controllers\Web;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\PublicCommentService;

class PublicCommentController
{
    public function __construct(private PublicCommentService $service){}
    public function store(int $id){try{$comment=$this->service->store($id,$_POST,auth()->check()?((int)auth()->id()):null);return ResponseFactory::json(['success'=>true,'message'=>'نظر شما پس از بررسی نمایش داده می‌شود.','id'=>$comment]);}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
