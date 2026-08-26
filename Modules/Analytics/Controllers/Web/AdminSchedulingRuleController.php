<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminSchedulingRuleService;
use RuntimeException;

class AdminSchedulingRuleController {
    public function __construct(private AdminSchedulingRuleService $service) {}
    public function index(){return $this->run(fn()=>['success'=>true,'data'=>$this->service->all((int)auth()->id())]);}
    public function store(){return $this->run(fn()=>['success'=>true,'data'=>['id'=>$this->service->create((int)auth()->id(),$this->payload())]]);}
    public function update(int$id){return $this->run(function()use($id){$this->service->update((int)auth()->id(),$id,$this->payload());return['success'=>true];});}
    public function cycleStatus(int$id){return $this->run(fn()=>['success'=>true,'data'=>['status'=>$this->service->cycleStatus((int)auth()->id(),$id)]]);}
    public function delete(int$id){return $this->run(function()use($id){$this->service->delete((int)auth()->id(),$id);return['success'=>true];});}
    public function realtimeVersion(){return $this->run(fn()=>['success'=>true,'data'=>$this->service->realtimeVersion((int)auth()->id())]);}
    private function payload():array{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new RuntimeException('اطلاعات قانون معتبر نیست.');return$data;}
    private function run(callable$callback){try{return ResponseFactory::json($callback());}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
