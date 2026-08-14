<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Core\translation\TranslationManager;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminSchedulingRuleService {
    public function all(int $actor): array {
        $branches = $this->branches($actor);
        $ids = array_column($branches, 'id');
        $rows = $ids ? DB::table('academy_branch_scheduling_rules')->whereIn('branch_id', $ids)->whereNull('deleted_at')->orderBy('scheduling_rule_id', 'DESC')->get() : [];
        $names = [];
        foreach ($branches as $branch) $names[$branch['id']] = $branch['name'];
        return ['branches'=>$branches, 'rules'=>array_map(fn($row)=>$this->map($row, $names[(int)$row['branch_id']] ?? 'شعبه'), $rows)];
    }

    public function create(int $actor, array $data): int {
        $validated = $this->validate($actor, $data);
        $pdo=db();$pdo->beginTransaction();
        try{$id=(int)DB::table('academy_branch_scheduling_rules')->insertGetId($validated['rule']+['created_by'=>$actor]);$this->saveTranslations($id,$validated['translations']);$pdo->commit();return$id;}catch(\Throwable$e){$pdo->rollBack();throw$e;}
    }

    public function update(int $actor, int $id, array $data): void {
        $this->find($actor, $id);$validated=$this->validate($actor,$data);$pdo=db();$pdo->beginTransaction();
        try{DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->update($validated['rule']+['updated_by'=>$actor]);$this->saveTranslations($id,$validated['translations']);$pdo->commit();}catch(\Throwable$e){$pdo->rollBack();throw$e;}
    }

    public function delete(int $actor, int $id): void {
        $this->find($actor, $id);
        DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id', $id)->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$actor]);
    }

    private function validate(int $actor, array $data): array {
        $branchId = (int)($data['branchId'] ?? 0);
        if (!in_array($branchId, array_column($this->branches($actor), 'id'), true)) throw new RuntimeException('شعبه انتخاب‌شده معتبر نیست.');
        $title=trim((string)($data['title']??''));$value=filter_var($data['value']??null,FILTER_VALIDATE_FLOAT);if($title===''||$value===false||$value<0)throw new RuntimeException('عنوان و مقدار عددی معتبر الزامی است.');
        $types=['لغو'=>'cancellation','جبرانی'=>'makeup','رزرو'=>'reservation','زمان‌بندی'=>'scheduling'];$statuses=['فعال'=>'active','غیرفعال'=>'inactive','در انتظار تأیید'=>'pending','حذف‌شده'=>'deleted'];$units=['ساعت'=>'hour','دقیقه'=>'minute','روز'=>'day','جلسه'=>'session','غیبت'=>'absence','نفر'=>'person','سال'=>'year','بله/خیر'=>'boolean','درصد'=>'percent','مبلغ'=>'currency'];
        $type=$types[(string)($data['type']??'')]??null;$status=$statuses[(string)($data['status']??'فعال')]??null;$unit=$units[(string)($data['valueUnit']??'')]??null;if(!$type||!$status||!$unit)throw new RuntimeException('نوع، وضعیت یا واحد مقدار معتبر نیست.');if($unit==='boolean'&&!in_array((float)$value,[0.0,1.0],true))throw new RuntimeException('مقدار بله/خیر فقط باید صفر یا یک باشد.');
        return ['rule'=>['branch_id'=>$branchId,'rule_type'=>$type,'rule_value'=>$value,'rule_value_unit'=>$unit,'status'=>$status],'translations'=>['title'=>$title,'summary'=>trim((string)($data['summary']??'')),'description'=>trim((string)($data['description']??''))]];
    }

    private function find(int $actor, int $id): array {
        $row=DB::table('academy_branch_scheduling_rules')->where('scheduling_rule_id',$id)->whereNull('deleted_at')->first();
        if(!$row||!in_array((int)$row['branch_id'],array_column($this->branches($actor),'id'),true))throw new RuntimeException('قانون زمان‌بندی یافت نشد.');
        return $row;
    }

    private function branches(int $actor): array {
        $user=DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();
        if(!$user)throw new RuntimeException('کاربر معتبر نیست.');
        if(SiteAdminAccess::allows($user))$rows=DB::table('academy_branches')->whereNull('deleted_at')->orderBy('academy_id')->get();
        elseif(($user['type']??'')==='branch')$rows=DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->get();
        else{$academies=array_merge(DB::table('academies')->where('user_id',$actor)->whereNull('deleted_at')->get(),DB::table('academies')->where('created_by',$actor)->whereNull('deleted_at')->get());$academyIds=array_values(array_unique(array_map(fn($a)=>(int)$a['academy_id'],$academies)));$rows=$academyIds?DB::table('academy_branches')->whereIn('academy_id',$academyIds)->whereNull('deleted_at')->get():[];}
        $tr=new TranslationManager();
        return array_map(function($row)use($tr){$id=(int)$row['branch_id'];$academyId=(int)$row['academy_id'];$branchName=$tr->get('academy_branches',$id,'name','fa')?:$tr->get('users',(int)$row['user_id'],'full_name','fa')?:'شعبه '.$id;$academyName=$tr->get('academies',$academyId,'name','fa')?:'آموزشگاه '.$academyId;return['id'=>$id,'academyId'=>$academyId,'name'=>$branchName,'academyName'=>$academyName];},$rows);
    }

    private function saveTranslations(int$id,array$values):void{$tr=new TranslationManager();foreach($values as$field=>$value)$tr->set('academy_branch_scheduling_rules',$id,$field,$value,'fa');}
    private function map(array $row,string $branchName):array{$tr=new TranslationManager();$id=(int)$row['scheduling_rule_id'];$types=['cancellation'=>'لغو','makeup'=>'جبرانی','reservation'=>'رزرو','scheduling'=>'زمان‌بندی'];$statuses=['active'=>'فعال','inactive'=>'غیرفعال','pending'=>'در انتظار تأیید','deleted'=>'حذف‌شده'];$units=['hour'=>'ساعت','minute'=>'دقیقه','day'=>'روز','session'=>'جلسه','absence'=>'غیبت','person'=>'نفر','year'=>'سال','boolean'=>'بله/خیر','percent'=>'درصد','currency'=>'مبلغ'];$amount=(float)$row['rule_value'];$amount=$amount===(float)(int)$amount?(string)(int)$amount:(string)$amount;$unit=$units[$row['rule_value_unit']]??$row['rule_value_unit'];return['id'=>$id,'branchId'=>(int)$row['branch_id'],'branchName'=>$branchName,'title'=>$tr->get('academy_branch_scheduling_rules',$id,'title','fa')??'','type'=>$types[$row['rule_type']]??$row['rule_type'],'value'=>$amount.' '.$unit,'valueAmount'=>$amount,'valueUnit'=>$unit,'status'=>$statuses[$row['status']]??$row['status'],'summary'=>$tr->get('academy_branch_scheduling_rules',$id,'summary','fa')??'','description'=>$tr->get('academy_branch_scheduling_rules',$id,'description','fa')??''];}
}
