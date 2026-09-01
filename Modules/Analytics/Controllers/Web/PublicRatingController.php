<?php
namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\PublicRatingService;

class PublicRatingController
{
    public function __construct(private PublicRatingService $service){}
    public function show(string$type,int$id){try{return ResponseFactory::json(['success'=>true,'rating'=>$this->service->summary($type,$id,auth()->check()?(int)auth()->id():null)]);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404);}}
    public function store(string$type,int$id){try{return ResponseFactory::json(['success'=>true,'message'=>locale()==='en'?'Your rating was saved.':'امتیاز شما ثبت شد.','rating'=>$this->service->rate($type,$id,(int)($_POST['score']??0),(int)auth()->id())]);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
