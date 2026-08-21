<?php
namespace Modules\Analytics\Controllers\Web;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\PublicCommentService;

class PublicCommentController
{
    public function __construct(private PublicCommentService $service){}
    public function store(int $id){$isEnglish=locale()==='en';try{$comment=$this->service->store($id,$_POST,auth()->check()?((int)auth()->id()):null);return ResponseFactory::json(['success'=>true,'message'=>$isEnglish?'Your comment was submitted successfully and will be displayed after review.':'نظر شما با موفقیت ارسال شد و پس از بررسی نمایش داده می‌شود.','id'=>$comment]);}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$isEnglish?'Comment submission failed.':$e->getMessage()],422);}}
}
