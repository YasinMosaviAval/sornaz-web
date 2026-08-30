<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyBranchService;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;
use Throwable;

class AcademyBranchController {


    public function __construct(
        protected AcademyBranchService $service
    ) {}


    public function index() {
        $user = auth()->user();
        return ResponseFactory::json(['success'=>true, 'csrf_token'=>csrf_token()] + $this->service->bootstrap((int)auth()->id(), SiteAdminAccess::allows($user)));
    }

    public function realtimeVersion() {
        $user = auth()->user();
        return ResponseFactory::json(['success' => true, 'data' => $this->service->realtimeVersion((int)auth()->id(), SiteAdminAccess::allows($user))]);
    }

    public function staffIndex() {
        return $this->run(fn()=>$this->service->staffData((int)auth()->id(),SiteAdminAccess::allows(auth()->user())));
    }

    public function staffRealtimeVersion() {
        return $this->run(fn()=>$this->service->staffRealtimeVersion((int)auth()->id(),SiteAdminAccess::allows(auth()->user())));
    }
    public function memberSchedules(){return $this->run(fn()=>$this->service->memberSchedules((int)auth()->id(),SiteAdminAccess::allows(auth()->user())));}
    public function storeMemberSchedule(){return $this->run(fn()=>$this->service->saveMemberSchedule((int)auth()->id(),$this->payload(),0,SiteAdminAccess::allows(auth()->user())),201);}
    public function updateMemberSchedule(int$id){return $this->run(fn()=>$this->service->saveMemberSchedule((int)auth()->id(),$this->payload(),$id,SiteAdminAccess::allows(auth()->user())));}
    public function deleteMemberSchedule(int$id){return $this->run(function()use($id){$this->service->deleteMemberSchedule((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));return null;});}
    public function cycleMemberScheduleStatus(int$id){return $this->run(fn()=>$this->service->cycleMemberScheduleStatus((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user())));}
    public function availabilityExceptions(){return $this->run(fn()=>$this->service->availabilityExceptions((int)auth()->id(),SiteAdminAccess::allows(auth()->user())));}
    public function storeAvailabilityException(){return $this->run(fn()=>$this->service->saveAvailabilityException((int)auth()->id(),$this->payload(),0,SiteAdminAccess::allows(auth()->user())),201);}
    public function updateAvailabilityException(int$id){return $this->run(fn()=>$this->service->saveAvailabilityException((int)auth()->id(),$this->payload(),$id,SiteAdminAccess::allows(auth()->user())));}
    public function deleteAvailabilityException(int$id){return $this->run(function()use($id){$this->service->deleteAvailabilityException((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));return null;});}
    public function cycleAvailabilityExceptionStatus(int$id){return $this->run(fn()=>$this->service->cycleAvailabilityExceptionStatus((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user())));}


    public function store() { 
        return $this->run(fn() => $this->service->store((int)auth()->id(), $this->payload(), SiteAdminAccess::allows(auth()->user())), 201); 
    }


    public function update(int $id) { 
        return $this->run(fn() => $this->service->update((int)auth()->id(), $id, $this->payload(), SiteAdminAccess::allows(auth()->user()))); 
    }

    public function cycleStatus(int $id) {
        return $this->run(fn() => $this->service->cycleStatus((int)auth()->id(), $id, SiteAdminAccess::allows(auth()->user())));
    }


    public function destroy(int $id) { 
        return $this->run(
            function() use($id) {
                $this->service->delete((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));
                return null;
            }
        ); 
    }


    public function storeType() { 
        return $this->run(fn() => $this->service->addType((int)auth()->id(), $this->payload()), 201); 
    }


    public function updateType(int $id) { 
        $this->requireSiteAdmin(); 
        return $this->run(fn()=>$this->service->updateType((int)auth()->id(),$id,$this->payload())); 
    }


    public function deleteType(int $id) { 
        $this->requireSiteAdmin(); 
        return $this->run(
            function() use($id) {
                $this->service->deleteType((int)auth()->id(),$id);
                return null;
            }
        );
    }


    public function updateMember(int $id) { 
        return $this->run(fn()=>$this->service->updateMember((int)auth()->id(),$id,$this->payload(),SiteAdminAccess::allows(auth()->user()))); 
    }

    public function storeMember() {
        return $this->run(fn()=>$this->service->storeMember((int)auth()->id(),$this->payload(),SiteAdminAccess::allows(auth()->user())), 201);
    }

    public function cycleMemberStatus(int $id) {
        return $this->run(fn()=>$this->service->cycleMemberStatus((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user())));
    }


    public function deleteMember(int $id) { 
        return $this->run(
            function() use($id) {
                $this->service->deleteMember((int)auth()->id(),$id,SiteAdminAccess::allows(auth()->user()));
                return null;
            }
        ); 
    }


    private function payload(): array {
        $encoded = (string)($_POST['payload_b64'] ?? '');
        $raw = $encoded !== '' ? base64_decode($encoded, true) : (string)($_POST['payload'] ?? '');
        if ($raw === false) throw new RuntimeException('رمزگشایی داده فرم ناموفق بود.');
        $data = json_decode($raw, true);
        if (!is_array($data)) throw new RuntimeException('داده فرم نامعتبر است: ' . json_last_error_msg());
        return $data;
    }


    private function requireSiteAdmin(): void { 
        if(!SiteAdminAccess::allows(auth()->user())) abort(403);
    }


    private function run(callable $callback, int $status=200) {
        try{
            return ResponseFactory::json(['success' => true, 'data' => $callback()], $status);
        }
        catch(Throwable $e){
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
