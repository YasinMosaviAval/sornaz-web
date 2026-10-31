<?php
namespace Modules\Education\Services;

use RuntimeException;

final class MusicPracticeResults
{
    private array $legacyCache=[];
    private ?int $legacyActor=null;
    public function __construct(private MusicPracticeStore $store) {}
    public function progress(int $actor): array
    {
        if($actor<1)throw new RuntimeException('ابتدا وارد شوید.',401);
        $this->store->ready();
        $rows=$this->store->rows("SELECT exam_id,version,stage FROM f_music_practice_runs WHERE user_id=? AND status='passed' GROUP BY exam_id,version,stage",[$actor]);
        $completed=[];
        foreach($rows as $row)$completed[$row['exam_id'].':'.$row['version']][]=(int)$row['stage'];
        $progress=[];
        $access=new MusicPracticeAccess($this->store);
        foreach($this->store->rows('SELECT e.*,v.definition FROM f_music_practice_exams e JOIN f_music_practice_versions v ON v.exam_id=e.id AND v.version=e.version') as $exam){
            $definition=json_decode($exam['definition'],true,512,JSON_THROW_ON_ERROR);
            $stages=$completed[$exam['id'].':'.$exam['version']]??[];
            $legacy=(int)$exam['version']===1?$this->legacy($actor,$exam['legacy_key']):0;
            if($legacy>0)$stages=array_values(array_unique([...$stages,...range(1,min($legacy,$definition['stageCount']))]));
            if(!$stages&&!$access->permitted($actor,$exam))continue;
            sort($stages);$next=1;while(in_array($next,$stages,true))$next++;
            $progress[]=['examId'=>(int)$exam['id'],'version'=>(int)$exam['version'],'completedStages'=>$stages,'nextStage'=>min($next,$definition['stageCount']+1),'stars'=>min(5,(int)floor(count($stages)*5/$definition['stageCount'])),'legacyStages'=>$legacy];
        }
        return ['progress'=>$progress,'recent'=>$this->store->rows('SELECT client_id runId,exam_id examId,version,stage,status,answer_count answerCount,best_streak bestStreak,updated_at updatedAt FROM f_music_practice_runs WHERE user_id=? ORDER BY id DESC LIMIT 100',[$actor])];
    }
    private function legacy(int $actor,?string $key): int
    {
        if(!$key||!preg_match('/^(western|iranian):(\d+)$/D',$key,$m))return 0;
        if($this->legacyActor!==$actor){
            $this->legacyCache=[];$this->legacyActor=$actor;
            try{
                foreach($this->store->rows("SELECT music,level_number,COUNT(*) total FROM f_practice_sessions WHERE user_id=? AND tool_key='interval_recognition' AND curriculum_version=1 AND status='passed' GROUP BY music,level_number",[$actor]) as $row)$this->legacyCache[$row['music'].':'.$row['level_number']]=(int)$row['total'];
                foreach($this->store->rows("SELECT music,level_number FROM f_practice_legacy_progress WHERE user_id=? AND tool_key='interval_recognition' AND curriculum_version=1",[$actor]) as $row){$k=$row['music'].':'.$row['level_number'];$this->legacyCache[$k]=($this->legacyCache[$k]??0)+1;}
            }catch(\PDOException $e){if(!str_contains($e->getMessage(),'no such table')&&$e->getCode()!=='42S02')throw $e;}
        }
        return min(5,$this->legacyCache[$key]??0);
    }
    private function prepare(int $actor,array $input): array
    {
        if($actor<1)throw new RuntimeException('ابتدا وارد شوید.',401);
        $this->store->ready();
        foreach(['examId','version','stage'] as $field)MusicPracticeDefinition::integer($input[$field]??null,1,PHP_INT_MAX);
        $client=$input['runId']??'';
        if(!is_string($client)||!preg_match('/^[a-f0-9]{32}$/D',$client))throw new RuntimeException('شناسهٔ اجرا معتبر نیست.',422);
        $answers=$input['answers']??null;
        if(!is_array($answers)||!array_is_list($answers)||count($answers)>100||!is_bool($input['closed']??null))throw new RuntimeException('فهرست پاسخ‌ها معتبر نیست.',422);
        $exam=$this->store->one('SELECT e.*,v.definition,v.was_published FROM f_music_practice_exams e JOIN f_music_practice_versions v ON v.exam_id=e.id WHERE e.id=? AND v.version=?',[$input['examId'],$input['version']]);
        if(!$exam)throw new RuntimeException('نسخهٔ آزمون پیدا نشد.',404);
        $expiry=$this->permitExpiry($actor,$exam,$input);
        $definition=json_decode($exam['definition'],true,512,JSON_THROW_ON_ERROR);
        MusicPracticeDefinition::integer($input['stage'],1,$definition['stageCount']);
        $normalized=[];
        foreach($answers as $answer){
            if(!is_array($answer))throw new RuntimeException('پاسخ معتبر نیست.',422);
            $row=MusicPracticeDefinition::answer($answer,$definition['config']);
            if(strtotime($row['answeredAt'])>$expiry||strtotime($row['answeredAt'])>time()+300)throw new RuntimeException('پاسخ خارج از مهلت دسترسی ثبت شده است.',403);
            $normalized[]=$row;
        }
        return [$client,$definition,$normalized,$exam];
    }
    private function permitExpiry(int $actor,array $exam,array $input): int
    {
        $access=new MusicPracticeAccess($this->store);
        if($this->store->onlineOnly){
            if(!$access->permitted($actor,$exam))throw new RuntimeException('دسترسی به آزمون مجاز نیست.',403);
            if((int)$exam['version']!==$input['version'])throw new RuntimeException('نسخهٔ آزمون تغییر کرده است؛ صفحه را تازه کنید.',409);
            return time()+300;
        }
        if(!$access->validPermit($actor,$input))throw new RuntimeException('مجوز دریافت آزمون معتبر نیست؛ آن را دوباره دریافت کنید.',403);
        return empty($exam['deleted_at'])?(int)$input['permitExpiresAt']:min((int)$input['permitExpiresAt'],strtotime($exam['deleted_at'].' UTC'));
    }
    public function store(int $actor,array $input): array
    {
        [$client,$definition,$normalized,$exam]=$this->prepare($actor,$input);
        return $this->store->transaction(function()use($actor,$input,$client,$definition,$normalized,$exam){
            $insert=$this->store->connection()->getAttribute(\PDO::ATTR_DRIVER_NAME)==='mysql'?'INSERT IGNORE':'INSERT OR IGNORE';
            $this->store->execute($insert.' INTO f_music_practice_runs(user_id,client_id,exam_id,version,stage,created_at,updated_at) VALUES(?,?,?,?,?,?,?)',[$actor,$client,$input['examId'],$input['version'],$input['stage'],$this->store->now(),$this->store->now()]);
            $run=$this->store->one('SELECT * FROM f_music_practice_runs WHERE user_id=? AND client_id=?'.$this->store->lock(),[$actor,$client]);
            if((int)$run['exam_id']!==$input['examId']||(int)$run['version']!==$input['version']||(int)$run['stage']!==$input['stage'])throw new RuntimeException('شناسهٔ اجرا قبلاً استفاده شده است.',409);
            $this->requireAnswers($run,$normalized);
            if($input['stage']>1){
                $prior=$this->store->one("SELECT id FROM f_music_practice_runs WHERE user_id=? AND exam_id=? AND version=? AND stage=? AND status='passed'",[$actor,$input['examId'],$input['version'],$input['stage']-1]);
                $legacy=$input['version']===1?$this->legacy($actor,$exam['legacy_key']):0;
                if(!$prior&&$legacy<$input['stage']-1)throw new RuntimeException('ابتدا مرحلهٔ قبلی را کامل کنید.',409);
            }
            foreach($normalized as $answer)$run=$this->append($run,$answer,$definition['requiredWins']);
            if($input['closed']&&$run['status']==='in_progress')$run['status']='abandoned';
            $this->store->execute('UPDATE f_music_practice_runs SET status=?,answer_count=?,streak=?,best_streak=?,updated_at=? WHERE id=? AND user_id=?',[$run['status'],$run['answer_count'],$run['streak'],$run['best_streak'],$this->store->now(),$run['id'],$actor]);
            return ['runId'=>$client,'acceptedThrough'=>(int)$run['answer_count'],'status'=>$run['status']];
        });
    }
    private function append(array $run,array $answer,int $wins): array
    {
        $json=json_encode($answer,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);$hash=hash('sha256',$json);
        if($answer['sequence']<=(int)$run['answer_count']){
            $old=$this->store->one('SELECT payload_hash FROM f_music_practice_answers WHERE run_id=? AND sequence_number=?',[$run['id'],$answer['sequence']]);
            if(!$old||!hash_equals($old['payload_hash'],$hash))throw new RuntimeException('پاسخ قبلی قابل بازنویسی نیست.',409);
            return $run;
        }
        if($run['status']!=='in_progress'||$answer['sequence']!==(int)$run['answer_count']+1)throw new RuntimeException('ترتیب پاسخ‌ها معتبر نیست.',409);
        $correct=$answer['expected']===$answer['selected'];
        $this->store->execute('INSERT INTO f_music_practice_answers(run_id,sequence_number,payload,payload_hash,is_correct,received_at) VALUES(?,?,?,?,?,?)',[$run['id'],$answer['sequence'],$json,$hash,(int)$correct,$this->store->now()]);
        $run['answer_count']=(int)$run['answer_count']+1;$run['streak']=$correct?(int)$run['streak']+1:0;$run['best_streak']=max((int)$run['best_streak'],$run['streak']);
        if($run['streak']>=$wins)$run['status']='passed';
        return $run;
    }
    private function requireAnswers(array $run,array $answers): void
    {
        if(!$answers&&!(int)$run['answer_count'])throw new RuntimeException('اجرا فاقد پاسخ است.',422);
    }
}
