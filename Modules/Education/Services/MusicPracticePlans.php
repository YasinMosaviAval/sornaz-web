<?php
namespace Modules\Education\Services;

use RuntimeException;

final class MusicPracticePlans
{
    public function __construct(private MusicPracticeStore $store) {}
    public function listing(int $actor,int $examId=0): array
    {
        $this->store->ready();
        $title=\Core\translation\EntityText::expression('music_practice_plans','p.id','title');
        $select="SELECT p.*,$title title FROM f_music_practice_plans p";
        $plans=$actor===1?$this->store->rows($select.' ORDER BY p.id DESC'):$this->store->rows($select." WHERE p.status='approved' OR p.teacher_id=? ORDER BY p.id DESC",[$actor]);
        $target=$examId?$this->store->one('SELECT * FROM f_music_practice_exams WHERE id=? AND published=1'.$this->store->liveExamSql(),[$examId]):null;
        if($target&&$target['audience']!=='subscribers'&&!(new MusicPracticeAccess($this->store))->permitted($actor,$target))$target=null;
        $nodes=$target?(new MusicPracticeAccess($this->store))->nodePath($examId,(int)$target['owner_id']):[];
        $plans=array_values(array_filter($plans,static fn($p)=>($p['exam_id']===null&&empty($p['node_id']))||$actor===1||(int)$p['teacher_id']===$actor||($p['exam_id']===null&&$target&&(int)$p['teacher_id']===(int)$target['owner_id']&&in_array((int)$p['node_id'],$nodes,true))));
        foreach($plans as &$plan)foreach(['id','teacher_id','exam_id','node_id','amount','days','teacher_bps','version','approved_by'] as $field)if(isset($plan[$field]))$plan[$field]=(int)$plan[$field];unset($plan);
        return ['plans'=>$plans,'revenue'=>$actor>0?$this->store->rows('SELECT r.*,o.teacher_id,o.buyer_id,o.paid_at FROM f_music_practice_revenue r JOIN f_music_practice_orders o ON o.id=r.order_id WHERE r.user_id=? ORDER BY r.order_id DESC LIMIT 100',[$actor]):[]];
    }
    public function propose(int $actor,array $input): array
    {
        $this->store->require($actor,'create');
        $this->store->ready();
        $id=MusicPracticeDefinition::integer($input['id']??0,0,PHP_INT_MAX);
        return $this->store->transaction(function()use($actor,$input,$id){
            $old=$id?$this->store->one('SELECT * FROM f_music_practice_plans WHERE id=?'.$this->store->lock(),[$id]):null;
            if($id&&(!$old||(int)$old['teacher_id']!==$actor))throw new RuntimeException('پلن متعلق به شما نیست.',403);
            $exam=MusicPracticeDefinition::integer($input['examId']??0,0,PHP_INT_MAX);
            if($exam)throw new RuntimeException('خرید سطح تکی مجاز نیست؛ کل آزمون یا اشتراک مدرس را انتخاب کنید.',422);
            $node=$this->proposalNode($actor,$exam,$input,$old);
            if($node&&!in_array($input['days']??null,[30,90,180,365],true))throw new RuntimeException('مدت اشتراک باید ماهانه، سه‌ماهه، شش‌ماهه یا سالانه باشد.',422);
            $title=MusicPracticeDefinition::text($input['title']??'');
            $values=[MusicPracticeDefinition::integer($input['amount']??null,1000,1000000000),$exam?0:MusicPracticeDefinition::integer($input['days']??null,1,366),$this->store->now(),$exam?:null];
            if($id&&($input['version']??null)!==(int)$old['version'])throw new RuntimeException('پلن تغییر کرده است.',409);
            $id=$this->writeProposal($id,$values,$node,$actor);
            $this->store->saveTitles('music_practice_plans',$id,$title,$title,$actor);
            return ['id'=>$id];
        });
    }
    private function writeProposal(int $id,array $values,int $node,int $actor): int
    {
        $scope=$this->store->hasNodeScope('music_practice_plans');
        if($id){
            $column=$scope?',node_id=?':'';
            $this->store->execute("UPDATE f_music_practice_plans SET amount=?,days=?,updated_at=?,exam_id=?,status='proposed',teacher_bps=NULL,approved_by=NULL,version=version+1$column WHERE id=?",[...$values,...($scope?[$node?:null]:[]),$id]);
            return $id;
        }
        $fields=array_combine(['amount','days','updated_at','exam_id','teacher_id'],[...$values,$actor]);
        if($scope)$fields['node_id']=$node?:null;
        return $this->store->insertTitled('music_practice_plans',$fields);
    }
    private function proposalNode(int $actor,int $exam,array $input,?array $old): int
    {
        $node=MusicPracticeDefinition::integer($input['nodeId']??$old['node_id']??0,0,PHP_INT_MAX);
        if(!$node)return 0;
        if($exam)throw new RuntimeException('خرید تکی نمی‌تواند اشتراک یک بخش باشد.',422);
        if(!$this->store->supportsSectionSubscriptions())throw new RuntimeException('ساختار اشتراک بخش‌ها روی سرور آماده نیست.',503);
        if(!$this->store->one('SELECT id FROM f_music_practice_nodes WHERE id=? AND (published=1 OR created_by=?)'.$this->store->liveNodeSql(),[$node,$actor]))throw new RuntimeException('بخش مجاز نیست.',403);
        return $node;
    }
    public function approve(int $actor,array $input): array
    {
        if($actor!==1)throw new RuntimeException('تعیین سهم و تأیید پلن فقط برای مدیر سایت مجاز است.',403);
        $id=MusicPracticeDefinition::integer($input['id']??null,1,PHP_INT_MAX);
        $bps=MusicPracticeDefinition::integer($input['teacherBps']??8000,0,10000);
        $status=$input['status']??'';if(!in_array($status,['approved','rejected'],true))throw new RuntimeException('وضعیت معتبر نیست.',422);
        $changed=$this->store->execute('UPDATE f_music_practice_plans SET teacher_bps=?,status=?,approved_by=1,version=version+1,updated_at=? WHERE id=? AND version=?',[$bps,$status,$this->store->now(),$id,MusicPracticeDefinition::integer($input['version']??null,1,PHP_INT_MAX)]);
        if(!$changed)throw new RuntimeException('پلن تغییر کرده یا پیدا نشد.',409);
        return ['id'=>$id];
    }
    public function share(int $actor,array $input): array
    {
        if($actor!==1)throw new RuntimeException('تعیین سهم هر آزمون فقط برای مدیر سایت مجاز است.',403);
        $id=MusicPracticeDefinition::integer($input['id']??null,1,PHP_INT_MAX);
        $bps=MusicPracticeDefinition::integer($input['teacherBps']??null,0,10000);
        $version=MusicPracticeDefinition::integer($input['shareVersion']??null,1,PHP_INT_MAX);
        if(!$this->store->execute('UPDATE f_music_practice_exams SET teacher_bps=?,share_version=share_version+1 WHERE id=? AND share_version=?',[$bps,$id,$version]))throw new RuntimeException('سهم آزمون تغییر کرده یا آزمون پیدا نشد.',409);
        return ['id'=>$id,'teacherBps'=>$bps,'platformBps'=>10000-$bps,'shareVersion'=>$version+1];
    }
}
