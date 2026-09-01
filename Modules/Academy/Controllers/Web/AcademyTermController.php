<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyTermService;
use RuntimeException;
use Throwable;

class AcademyTermController {


    public function __construct(private AcademyTermService $service) {}


    public function index() {
        return $this->run(function(){
            $actor=(int)auth()->id();
            $data=$this->service->bootstrap($actor);
            $statusLabels=['pending'=>'در انتظار تأیید','open'=>'باز','ongoing'=>'در حال برگزاری','finished'=>'پایان یافته'];
            foreach($data['terms'] as &$term)$term['status']=$statusLabels[$term['status_code']??'']??$term['status'];
            unset($term);
            $branchIds=array_map(fn($branch)=>(int)$branch['id'],$data['branches']);
            $data['branchAcademies']=[];
            if($branchIds)foreach(\Core\database\DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get() as $branch)$data['branchAcademies'][(int)$branch['branch_id']]=(int)$branch['academy_id'];
            $data['timezones']=array_map(fn($timezone)=>['id'=>(int)$timezone['timezone_id'],'name'=>(string)$timezone['timezone']],\Core\database\DB::table('f_timezone')->where('status','active')->whereNull('deleted_at')->orderBy('sort_order')->get());
            $isReceptionist=$this->isReceptionist($actor);
            $data['permissions']=['isReceptionist'=>$isReceptionist,'isBranchContext'=>$this->isBranchContext($actor),'canCancelSessions'=>true,'canApproveSessionCancellations'=>!$isReceptionist];
            return['success'=>true,'data'=>$data];
        });
    }


    public function invoices() {
        return $this->run(fn()=>['success'=>true,'data'=>$this->service->invoiceBootstrap((int)auth()->id())]);
    }

    public function realtimeVersion(){return$this->run(fn()=>['success'=>true,'data'=>$this->service->realtimeVersion((int)auth()->id())]);}


    public function updateInvoice(int $id) {
        return $this->run(
            function() use($id) {
                $this->service->updateInvoice((int)auth()->id(),$id,$this->payload());
                return['success'=>true];
            }
        );
    }


    public function payInstallment(int $id, int $installmentId) {
        return $this->run(
            function() use($id, $installmentId) {
                $this->service->payInstallment((int)auth()->id(),$id,$installmentId);
                return['success'=>true];
            }
        );
    }


    public function store() {
        return $this->save();
    }


    public function update(int $id) {
        return $this->save($id);
    }


    public function storeDiscount() {
        return $this->run(fn()=>['success'=>true,'data'=>$this->service->saveDiscount((int)auth()->id(),$this->payload())]);
    }


    public function destroy(int $id) {
        return $this->run(function()use($id){$this->service->delete((int)auth()->id(),$id);return['success'=>true];});
    }

    public function cycleStatus(int $id) {
        return $this->run(function()use($id){$actor=(int)auth()->id();if($this->isReceptionist($actor))throw new RuntimeException('کاربر پذیرش مجوز تغییر وضعیت ترم را ندارد.');return['success'=>true,'data'=>$this->service->cycleStatus($actor,$id)];});
    }

    public function cancelSession(int$id,int$sessionId){return$this->run(function()use($id,$sessionId){$actor=(int)auth()->id();return['success'=>true,'data'=>$this->service->cancelSession($actor,$id,$sessionId,$this->payload(),$this->isReceptionist($actor))];});}
    public function approveSessionCancellation(int$id,int$sessionId){return$this->run(function()use($id,$sessionId){$actor=(int)auth()->id();if($this->isReceptionist($actor))throw new RuntimeException('کاربر پذیرش مجوز تأیید لغو جلسه را ندارد.');return['success'=>true,'data'=>$this->service->decideSessionCancellation($actor,$id,$sessionId,true)];});}
    public function rejectSessionCancellation(int$id,int$sessionId){return$this->run(function()use($id,$sessionId){$actor=(int)auth()->id();if($this->isReceptionist($actor))throw new RuntimeException('کاربر پذیرش مجوز رد لغو جلسه را ندارد.');return['success'=>true,'data'=>$this->service->decideSessionCancellation($actor,$id,$sessionId,false)];});}
    public function restoreSession(int$id,int$sessionId){return$this->run(function()use($id,$sessionId){$actor=(int)auth()->id();return['success'=>true,'data'=>$this->service->restoreCanceledSession($actor,$id,$sessionId,$this->isReceptionist($actor))];});}


    private function save(int$id=0) {
        return $this->run(function() use ($id) {
            $data = $this->payload();
            $receptionist=$this->isReceptionist((int)auth()->id());
            $data['status']=$receptionist?'pending':(in_array($data['status']??'',['open','ongoing'],true)?$data['status']:'open');
            if($data['status']==='ongoing')$data['students']=[];
            if((float)($data['cost']??0)<=0){$data['cost']=0;$data['discountId']=0;$data['installmentCount']=1;}
            if(count($data['sessions']??[])===1)$data['repeatType']='no-period';
            $sessionCount = count($data['sessions'] ?? []);
            $installmentCount = max(1, (int) ($data['installmentCount'] ?? 1));
            $maximum = max(2, $sessionCount);
            if ($installmentCount > $maximum) {
                throw new RuntimeException("تعداد اقساط نمی‌تواند بیشتر از {$maximum} باشد.");
            }
            $course=\Core\database\DB::table('academy_branch_courses')->where('course_id',(int)($data['courseId']??0))->whereNull('deleted_at')->first();
            $saved=$course&&$course['branch_id']===null&&$course['academy_id']!==null
                ?$this->service->saveAcademyTerm((int)auth()->id(),$data,$id)
                :$this->service->save((int)auth()->id(),$data,$id);
            $termId=(int)($saved['id']??$id);$savedSessions=\Core\database\DB::table('academy_branch_course_term_sessions')->where('term_id',$termId)->whereNull('deleted_at')->orderBy('term_session_id')->get();
            foreach($savedSessions as $index=>$session){$timezoneId=(int)($data['sessions'][$index]['timezoneId']??0);if($timezoneId)\Core\database\DB::table('academy_branch_bookings')->where('booking_id',(int)$session['booking_id'])->update(['timezone_id'=>$timezoneId,'updated_by'=>(int)auth()->id()]);}
            return ['success'=>true,'data'=>$saved];
        });
    }


    private function payload() : array {
        $raw = base64_decode(strtr((string) request()->input('payload_b64', ''), '-_', '+/'), true);
        $data = $raw === false ? null : json_decode($raw, true);
        if(!is_array($data)) throw new RuntimeException('اطلاعات ارسالی معتبر نیست.');
        return $data;
    }

    private function isReceptionist(int $actor): bool {
        $role=(bool)\Core\database\DB::table('academy_branch_members')
            ->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')
            ->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id',$actor)->whereRaw("access_system_roles.name LIKE '%receptionist%'")
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
        if($role)return true;
        return(bool)\Core\database\DB::table('academy_branch_members')
            ->join('academy_branch_member_contracts','academy_branch_member_contracts.member_id','=','academy_branch_members.member_id')
            ->where('academy_branch_members.user_id',$actor)->where('academy_branch_member_contracts.type','receptionist')
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_contracts.deleted_at')->first();
    }

    private function isBranchContext(int $actor): bool {
        $user=\Core\database\DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();
        if(($user['type']??'')==='branch')return true;
        return(bool)\Core\database\DB::table('academy_branch_members')
            ->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')
            ->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')
            ->where('academy_branch_members.user_id',$actor)->whereRaw("access_system_roles.name LIKE 'branch_%'")
            ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->first();
    }


    private function run(callable$callback) {
        try{
            return ResponseFactory::json($callback());
        }
        catch(Throwable $e) {
            return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);
        }
    }


}
