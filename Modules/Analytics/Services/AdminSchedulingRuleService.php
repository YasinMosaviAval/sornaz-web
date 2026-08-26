<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Core\translation\TranslationManager;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminSchedulingRuleService
{
    public function all(int $actor): array
    {
        $scope=$this->scope($actor);$rows=[];
        $academyIds=array_column(array_filter($scope['visible'],fn(array$o):bool=>$o['kind']==='academy'),'id');
        $branchIds=array_column(array_filter($scope['visible'],fn(array$o):bool=>$o['kind']==='branch'),'id');
        if($academyIds)$rows=array_merge($rows,DB::table('academy_branch_scheduling_rules')->whereIn('academy_id',$academyIds)->whereNull('branch_id')->whereNull('deleted_at')->get());
        if($branchIds)$rows=array_merge($rows,DB::table('academy_branch_scheduling_rules')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get());
        $rows=array_values(array_reduce($rows,function(array$c,array$r):array{$c[(int)$r['scheduling_rule_id']]=$r;return$c;},[]));
        usort($rows,fn(array$a,array$b):int=>(int)$b['scheduling_rule_id']<=>(int)$a['scheduling_rule_id']);
        $visible=[];foreach($scope['visible']as$o)$visible[$o['key']]=$o;
        return['organizations'=>$scope['manageable'],'filter_organizations'=>$scope['visible'],'organization_selection'=>$scope['fixed']?'fixed':'select','show_status_field'=>!$scope['receptionist'],'rules'=>array_map(function(array$r)use($visible,$scope):array{$key=$r['branch_id']!==null?'branch:'.(int)$r['branch_id']:'academy:'.(int)$r['academy_id'];return$this->map($r,$visible[$key]??['key'=>$key,'kind'=>$r['branch_id']!==null?'branch':'academy','id'=>(int)($r['branch_id']??$r['academy_id']),'name'=>'سازمان'],in_array($key,$scope['manageable_keys'],true),!$scope['receptionist']);},$rows)];
    }

    public function create(int$actor,array$data):int{$v=$this->validate($actor,$data);$pdo=db();$pdo->beginTransaction();try{$id=(int)DB::table('academy_branch_scheduling_rules')->insertGetId($v['rule']+['created_by'=>$actor]);$this->saveTranslations($id,$v['translations']);$pdo->commit();return$id;}catch(\Throwable$e){$pdo->rollBack();throw$e;}}
    public function update(int$actor,int$id,array$data):void{$this->findEditable($actor,$id);$v=$this->validate($actor,$data);$pdo=db();$pdo->beginTransaction();try{DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->update($v['rule']+['updated_by'=>$actor]);$this->saveTranslations($id,$v['translations']);$pdo->commit();}catch(\Throwable$e){$pdo->rollBack();throw$e;}}

    public function cycleStatus(int$actor,int$id):string
    {
        $row=$this->findEditable($actor,$id);if($this->scope($actor)['receptionist'])throw new RuntimeException('تغییر وضعیت برای کاربر پذیرش مجاز نیست.');
        $next=(['pending'=>'active','active'=>'inactive','inactive'=>'deleted','deleted'=>'pending'])[(string)$row['status']]??'active';$values=['status'=>$next,'updated_by'=>$actor];
        if($next==='pending'){$values['approved_by']=null;$values['approved_at']=null;}else{$values['approved_by']=$actor;$values['approved_at']=date('Y-m-d H:i:s');}
        DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->update($values);return$next;
    }

    public function delete(int$actor,int$id):void{$this->findEditable($actor,$id);$scope=$this->scope($actor);$values=['status'=>'deleted','updated_by'=>$actor];if(!$scope['receptionist']){$values['approved_by']=$actor;$values['approved_at']=date('Y-m-d H:i:s');}DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->update($values);}
    public function realtimeVersion(int$actor):array{$data=$this->all($actor);$payload=array_map(fn(array$r):array=>[$r['id'],$r['organizationKey'],$r['status'],$r['title'],$r['type'],$r['value'],$r['summary'],$r['description']],$data['rules']);return['resource'=>'scheduling_rules','version'=>sha1(json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))];}

    private function validate(int$actor,array$data):array
    {
        $scope=$this->scope($actor);$key=trim((string)($data['organizationKey']??''));if($scope['fixed']&&count($scope['manageable'])===1)$key=$scope['manageable'][0]['key'];$organization=null;foreach($scope['manageable']as$candidate)if($candidate['key']===$key)$organization=$candidate;if(!$organization)throw new RuntimeException('سازمان انتخاب‌شده معتبر نیست.');
        $title=trim((string)($data['title']??''));$value=filter_var($data['value']??null,FILTER_VALIDATE_FLOAT);if($title===''||$value===false||$value<0)throw new RuntimeException('عنوان و مقدار عددی معتبر الزامی است.');
        $types=['لغو'=>'cancellation','جبرانی'=>'makeup','رزرو'=>'reservation','زمان‌بندی'=>'scheduling'];$units=['ساعت'=>'hour','دقیقه'=>'minute','روز'=>'day','جلسه'=>'session','غیبت'=>'absence','نفر'=>'person','سال'=>'year','بله/خیر'=>'boolean','درصد'=>'percent','مبلغ'=>'currency'];$type=$types[(string)($data['type']??'')]??null;$unit=$units[(string)($data['valueUnit']??'')]??null;if(!$type||!$unit)throw new RuntimeException('نوع یا واحد مقدار معتبر نیست.');if($unit==='boolean'&&!in_array((float)$value,[0.0,1.0],true))throw new RuntimeException('مقدار بله/خیر فقط باید صفر یا یک باشد.');
        $status=$scope['receptionist']?'pending':(['فعال'=>'active','غیرفعال'=>'inactive'][(string)($data['status']??'فعال')]??null);if(!$status)throw new RuntimeException('وضعیت انتخاب‌شده معتبر نیست.');$now=date('Y-m-d H:i:s');
        return['rule'=>['academy_id'=>$organization['kind']==='academy'?$organization['id']:(int)$organization['academyId'],'branch_id'=>$organization['kind']==='branch'?$organization['id']:null,'rule_type'=>$type,'rule_value'=>$value,'rule_value_unit'=>$unit,'status'=>$status,'approved_by'=>$scope['receptionist']?null:$actor,'approved_at'=>$scope['receptionist']?null:$now],'translations'=>['title'=>$title,'summary'=>trim((string)($data['summary']??'')),'description'=>trim((string)($data['description']??''))]];
    }

    private function findEditable(int$actor,int$id):array{$row=DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->whereNull('deleted_at')->first();if(!$row)throw new RuntimeException('قانون زمان‌بندی یافت نشد.');$key=$row['branch_id']!==null?'branch:'.(int)$row['branch_id']:'academy:'.(int)$row['academy_id'];if(!in_array($key,$this->scope($actor)['manageable_keys'],true))throw new RuntimeException('امکان ویرایش این قانون برای شما وجود ندارد.');return$row;}

    private function scope(int$actor):array
    {
        $user=DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();if(!$user)throw new RuntimeException('کاربر معتبر نیست.');$siteAdmin=SiteAdminAccess::allows($user);$academyIds=[];$branchIds=[];$roleNames=[];
        if($siteAdmin)foreach(DB::table('academies')->whereNull('deleted_at')->get()as$row)$academyIds[]=(int)$row['academy_id'];
        if(($user['type']??'')==='academy')foreach(DB::table('academies')->where('user_id',$actor)->whereNull('deleted_at')->get()as$row)$academyIds[]=(int)$row['academy_id'];
        foreach(DB::table('academies')->where('created_by',$actor)->whereNull('deleted_at')->get()as$row)$academyIds[]=(int)$row['academy_id'];
        if(($user['type']??'')==='branch')foreach(DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->get()as$row)$branchIds[]=(int)$row['branch_id'];
        foreach(DB::table('academy_branch_members')->where('user_id',$actor)->whereNull('deleted_at')->get()as$member){$memberRoles=[];$roles=DB::table('academy_branch_member_roles')->join('access_system_roles','access_system_roles.role_id','=','academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id',(int)$member['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();foreach($roles as$role){$memberRoles[]=(string)$role['name'];$roleNames[]=(string)$role['name'];}$contract=DB::table('academy_branch_member_contracts')->where('member_id',(int)$member['member_id'])->whereNull('deleted_at')->first();if($contract&&in_array((string)$contract['type'],['manager','receptionist'],true)){$derived=($member['branch_id']===null?'academy_':'branch_').(string)$contract['type'];$memberRoles[]=$derived;$roleNames[]=$derived;}$branchRole=(bool)array_filter($memberRoles,fn(string$n):bool=>str_contains($n,'branch_manager')||str_contains($n,'branch_receptionist'));$academyRole=(bool)array_filter($memberRoles,fn(string$n):bool=>str_contains($n,'academy_owner')||str_contains($n,'academy_manager')||str_contains($n,'academy_receptionist'));if($member['branch_id']!==null&&$branchRole)$branchIds[]=(int)$member['branch_id'];if($member['branch_id']===null&&$academyRole)$academyIds[]=(int)$member['academy_id'];}
        $academyIds=array_values(array_unique(array_filter($academyIds)));$branchIds=array_values(array_unique(array_filter($branchIds)));if($academyIds)foreach(DB::table('academy_branches')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get()as$row)$branchIds[]=(int)$row['branch_id'];$branchIds=array_values(array_unique($branchIds));$manageable=$this->organizations($academyIds,$branchIds);$parentAcademyIds=$academyIds;if($branchIds)foreach(DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get()as$row)$parentAcademyIds[]=(int)$row['academy_id'];$visible=$this->organizations(array_values(array_unique($parentAcademyIds)),$branchIds);
        $direct=in_array((string)($user['type']??''),['academy','branch'],true);$manager=(bool)array_filter($roleNames,fn(string$n):bool=>str_contains($n,'owner')||str_contains($n,'manager'));$hasReceptionist=(bool)array_filter($roleNames,fn(string$n):bool=>str_contains($n,'receptionist'));$receptionist=!$direct&&!$manager&&$hasReceptionist;$fixed=!$siteAdmin&&$manageable&&!array_filter($manageable,fn(array$o):bool=>$o['kind']!=='branch');return['manageable'=>$manageable,'visible'=>$visible,'manageable_keys'=>array_column($manageable,'key'),'fixed'=>$fixed,'receptionist'=>$receptionist];
    }

    private function organizations(array$academyIds,array$branchIds):array{$tr=new TranslationManager();$result=[];if($academyIds)foreach(DB::table('academies')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get()as$row){$id=(int)$row['academy_id'];$result[]=['key'=>'academy:'.$id,'id'=>$id,'kind'=>'academy','name'=>$tr->get('academies',$id,'title','fa')?:$tr->get('academies',$id,'name','fa')?:'آموزشگاه '.$id];}if($branchIds)foreach(DB::table('academy_branches')->whereIn('branch_id',$branchIds)->whereNull('deleted_at')->get()as$row){$id=(int)$row['branch_id'];$result[]=['key'=>'branch:'.$id,'id'=>$id,'kind'=>'branch','academyId'=>(int)$row['academy_id'],'name'=>$tr->get('academy_branches',$id,'name','fa')?:'شعبه '.$id];}usort($result,fn(array$a,array$b):int=>($a['kind']==='academy'?0:1)<=>($b['kind']==='academy'?0:1));return$result;}
    private function saveTranslations(int$id,array$values):void{$tr=new TranslationManager();foreach($values as$field=>$value)$tr->set('academy_branch_scheduling_rules',$id,$field,$value,'fa');}
    private function map(array$row,array$o,bool$canEdit,bool$canChangeStatus):array{$tr=new TranslationManager();$id=(int)$row['scheduling_rule_id'];$types=['cancellation'=>'لغو','makeup'=>'جبرانی','reservation'=>'رزرو','scheduling'=>'زمان‌بندی'];$statuses=['active'=>'فعال','inactive'=>'غیرفعال','pending'=>'در انتظار تأیید','deleted'=>'حذف‌شده'];$units=['hour'=>'ساعت','minute'=>'دقیقه','day'=>'روز','session'=>'جلسه','absence'=>'غیبت','person'=>'نفر','year'=>'سال','boolean'=>'بله/خیر','percent'=>'درصد','currency'=>'مبلغ'];$amount=(float)$row['rule_value'];$amount=$amount===(float)(int)$amount?(string)(int)$amount:(string)$amount;$unit=$units[$row['rule_value_unit']]??$row['rule_value_unit'];return['id'=>$id,'organizationKey'=>$o['key'],'organizationKind'=>$o['kind'],'organizationName'=>$o['name'],'branchId'=>$o['kind']==='branch'?$o['id']:0,'branchName'=>$o['name'],'title'=>$tr->get('academy_branch_scheduling_rules',$id,'title','fa')??'','type'=>$types[$row['rule_type']]??$row['rule_type'],'value'=>$amount.' '.$unit,'valueAmount'=>$amount,'valueUnit'=>$unit,'status'=>$statuses[$row['status']]??$row['status'],'summary'=>$tr->get('academy_branch_scheduling_rules',$id,'summary','fa')??'','description'=>$tr->get('academy_branch_scheduling_rules',$id,'description','fa')??'','canEdit'=>$canEdit,'canChangeStatus'=>$canEdit&&$canChangeStatus];}
}
