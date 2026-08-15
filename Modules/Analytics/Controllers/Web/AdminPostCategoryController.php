<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminPostCategoryService;
use RuntimeException;

class AdminPostCategoryController
{
    public function __construct(private AdminPostCategoryService $service) {}
    public function index() { return $this->run(fn() => ['success'=>true,'data'=>$this->service->index((string)($_GET['search']??''))]); }
    public function store() { return $this->run(fn() => ['success'=>true,'data'=>['id'=>$this->service->create((int)auth()->id(),$this->payload())]]); }
    public function update(int $id) { return $this->run(function() use ($id) {$this->service->update((int)auth()->id(),$id,$this->payload());return ['success'=>true];}); }
    public function delete(int $id) { return $this->run(function() use ($id) {$this->service->delete((int)auth()->id(),$id);return ['success'=>true];}); }
    private function payload(): array { $raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new RuntimeException('اطلاعات دسته‌بندی معتبر نیست.');return $data; }
    private function run(callable $callback) { try{return ResponseFactory::json($callback());}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);} }
}
