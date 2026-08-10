<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyBranchService;

class AcademyBranchController {
    public function __construct(protected AcademyBranchService $service) {}
    public function index() { return ResponseFactory::json(['success'=>true] + $this->service->bootstrap((int)auth()->id())); }
    public function store() { return $this->run(fn() => $this->service->store((int)auth()->id(), $this->payload()), 201); }
    public function update(int $id) { return $this->run(fn() => $this->service->update((int)auth()->id(), $id, $this->payload())); }
    public function destroy(int $id) { return $this->run(function() use($id){$this->service->delete((int)auth()->id(),$id);return null;}); }
    public function storeType() { return $this->run(fn() => $this->service->addType((int)auth()->id(), trim((string)($_POST['name'] ?? ''))), 201); }
    private function payload(): array { $data=json_decode((string)($_POST['payload'] ?? ''),true); if(!is_array($data))throw new \RuntimeException('داده فرم نامعتبر است.'); return $data; }
    private function run(callable $callback,int $status=200) { try{return ResponseFactory::json(['success'=>true,'data'=>$callback()],$status);}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);} }
}
