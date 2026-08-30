<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminNotificationService;
use RuntimeException;

class AdminNotificationController {
    public function __construct(private AdminNotificationService $service) {}
    public function index() { return $this->run(fn()=>['success'=>true,'data'=>['notifications'=>$this->service->all((int)auth()->id())]]); }
    public function store() { return $this->run(fn()=>['success'=>true,'data'=>['id'=>$this->service->create((int)auth()->id(),$this->payload())]]); }
    public function publish(int$id) { return $this->run(function()use($id){$this->service->setStatus($id,'published',(int)auth()->id());return['success'=>true];}); }
    public function expire(int$id) { return $this->run(function()use($id){$this->service->setStatus($id,'expired',(int)auth()->id());return['success'=>true];}); }
    public function read(int$id) { return $this->run(function()use($id){$this->service->markRead($id,(int)auth()->id());return['success'=>true];}); }
    public function delete(int$id) { return $this->run(function()use($id){$this->service->delete($id,(int)auth()->id());return['success'=>true];}); }
    private function payload():array{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new RuntimeException('اطلاعات اعلان معتبر نیست.');return$data;}
    private function run(callable$callback){try{return ResponseFactory::json($callback());}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
