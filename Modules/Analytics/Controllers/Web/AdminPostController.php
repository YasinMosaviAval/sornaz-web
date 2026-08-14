<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminPostService;
use RuntimeException;

class AdminPostController
{
    public function __construct(private AdminPostService $service) {}
    public function index() { return $this->run(fn()=>['success'=>true, 'data'=>$this->service->index($_GET)]); }
    public function show(int $id) { return $this->run(fn()=>['success'=>true, 'data'=>$this->service->find($id)]); }
    public function store() { return $this->run(fn()=>['success'=>true, 'data'=>['id'=>$this->service->create((int)auth()->id(), $this->payload())]]); }
    public function update(int $id) { return $this->run(function()use($id){$this->service->update((int)auth()->id(), $id, $this->payload());return['success'=>true];}); }
    public function trash(int $id) { return $this->run(function()use($id){$this->service->trash((int)auth()->id(), $id);return['success'=>true];}); }
    public function restore(int $id) { return $this->run(function()use($id){$this->service->restore((int)auth()->id(), $id);return['success'=>true];}); }
    public function destroy(int $id) { return $this->run(function()use($id){$this->service->destroy((int)auth()->id(), $id);return['success'=>true];}); }
    private function payload(): array { $raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new RuntimeException('اطلاعات نوشته معتبر نیست.');return$data; }
    private function run(callable $callback) { try{return ResponseFactory::json($callback());}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);} }
}
