<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyBranchService;
use Modules\System\Services\SiteAdminAccess;

class AcademyBranchController {
    public function __construct(protected AcademyBranchService $service) {}
    public function index() {
        $user = auth()->user();
        return ResponseFactory::json(['success'=>true, 'csrf_token'=>csrf_token()] + $this->service->bootstrap((int)auth()->id(), SiteAdminAccess::allows($user)));
    }
    public function store() { return $this->run(fn() => $this->service->store((int)auth()->id(), $this->payload(), SiteAdminAccess::allows(auth()->user())), 201); }
    public function update(int $id) { return $this->run(fn() => $this->service->update((int)auth()->id(), $id, $this->payload(), SiteAdminAccess::allows(auth()->user()))); }
    public function destroy(int $id) { return $this->run(function() use($id){$this->service->delete((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));return null;}); }
    public function storeType() { return $this->run(fn() => $this->service->addType((int)auth()->id(), $this->payload()), 201); }
    public function updateType(int $id) { $this->requireSiteAdmin(); return $this->run(fn()=>$this->service->updateType((int)auth()->id(),$id,$this->payload())); }
    public function deleteType(int $id) { $this->requireSiteAdmin(); return $this->run(function()use($id){$this->service->deleteType((int)auth()->id(),$id);return null;}); }
    public function updateMember(int $id) { return $this->run(fn()=>$this->service->updateMember((int)auth()->id(),$id,$this->payload(),SiteAdminAccess::allows(auth()->user()))); }
    public function deleteMember(int $id) { return $this->run(function()use($id){$this->service->deleteMember((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));return null;}); }
    private function payload(): array {
        $encoded = (string)($_POST['payload_b64'] ?? '');
        $raw = $encoded !== '' ? base64_decode($encoded, true) : (string)($_POST['payload'] ?? '');
        if ($raw === false) throw new \RuntimeException('رمزگشایی داده فرم ناموفق بود.');
        $data = json_decode($raw, true);
        if (!is_array($data)) throw new \RuntimeException('داده فرم نامعتبر است: ' . json_last_error_msg());
        return $data;
    }
    private function requireSiteAdmin(): void { if(!SiteAdminAccess::allows(auth()->user())) abort(403); }
    private function run(callable $callback,int $status=200) { try{return ResponseFactory::json(['success'=>true,'data'=>$callback()],$status);}catch(\Throwable $e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);} }
}
