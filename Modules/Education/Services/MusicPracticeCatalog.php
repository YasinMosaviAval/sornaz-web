<?php
namespace Modules\Education\Services;

use RuntimeException;

final class MusicPracticeCatalog
{
    public function __construct(private MusicPracticeStore $store) {}
    public function people(int $actor,mixed $query): array
    {
        if(!$this->store->allows($actor,'create')&&!$this->store->allows($actor,'edit'))throw new RuntimeException('مجوز انتخاب مخاطبان را ندارید.',403);
        if(!is_string($query))throw new RuntimeException('عبارت جستجو معتبر نیست.',422);
        $query=trim($query);if(mb_strlen($query)<2)return ['users'=>[],'viewerId'=>$actor];
        $query=MusicPracticeDefinition::text($query,80);
        $pattern=str_replace(['!','%','_'],['!!','!%','!_'],$query).'%';
        return ['users'=>array_map(static fn($u)=>['id'=>(int)$u['user_id'],'username'=>$u['username']],$this->store->rows("SELECT user_id,username FROM f_users WHERE deleted_at IS NULL AND username LIKE ? ESCAPE '!' ORDER BY username LIMIT 25",[$pattern])),'viewerId'=>$actor];
    }
    public function publish(int $actor,array $input): array
    {
        $this->store->require($actor,'publish');$this->store->ready();
        $kind=$input['kind']??'exam';
        if(!in_array($kind,['exam','node'],true)||!is_bool($input['published']??null))throw new RuntimeException('وضعیت انتشار معتبر نیست.',422);
        $id=MusicPracticeDefinition::integer($input['id']??null,1,PHP_INT_MAX);
        $version=MusicPracticeDefinition::integer($input['version']??null,1,PHP_INT_MAX);
        return $this->store->transaction(function()use($actor,$input,$kind,$id,$version){
            $table=$kind==='exam'?'f_music_practice_exams':'f_music_practice_nodes';$owner=$kind==='exam'?'owner_id':'created_by';
            $row=$this->store->one('SELECT * FROM '.$table.' WHERE id=?'.$this->store->lock(),[$id]);
            if(!$row)throw new RuntimeException('مورد پیدا نشد.',404);
            if($actor!==1&&(int)$row[$owner]!==$actor)throw new RuntimeException('اجازهٔ انتشار محتوای کاربر دیگر را ندارید.',403);
            if((int)$row['version']!==$version)throw new RuntimeException('نسخه تغییر کرده است؛ دوباره دریافت کنید.',409);
            $this->store->execute('UPDATE '.$table.' SET published=?,updated_at=?'.($kind==='node'?',version=version+1':'').' WHERE id=?',[(int)$input['published'],$this->store->now(),$id]);
            if($kind==='exam'&&$input['published'])$this->store->execute('UPDATE f_music_practice_versions SET was_published=1 WHERE exam_id=? AND version=?',[$id,$version]);
            return ['id'=>$id,'published'=>$input['published']];
        });
    }
    public function listing(int $actor, bool $manage = false): array
    {
        if ($manage && !$this->store->allows($actor, 'create') && !$this->store->allows($actor, 'edit') && !$this->store->allows($actor, 'publish')) throw new RuntimeException('اجازهٔ مدیریت آزمون‌ها را ندارید.',403);
        $this->store->ready();
        $nodes = $this->store->rows('SELECT n.*,'.$this->store->titleColumns('music_practice_nodes','n').' FROM f_music_practice_nodes n ORDER BY n.depth,n.position,n.id');
        $visible = [];
        foreach ($nodes as $node) {
            if (($manage&&($actor===1||(int)$node['published']||(int)$node['created_by']===$actor)) || ((int)$node['published'] && (!$node['parent_id'] || isset($visible[$node['parent_id']])))) $visible[$node['id']] = $node;
        }
        $access = new MusicPracticeAccess($this->store);
        $exams = [];
        foreach ($this->store->rows('SELECT e.*,v.definition,'.$this->store->titleColumns('music_practice_exams','e').' FROM f_music_practice_exams e JOIN f_music_practice_versions v ON v.exam_id=e.id AND v.version=e.version ORDER BY e.position,e.id') as $exam) {
            if (!isset($visible[$exam['node_id']])) continue;
            $row=$this->examRow($actor,$exam,$manage,$access);
            if($row)$exams[]=$row;
        }
        return ['viewerId'=>$actor,'nodes'=>array_map(static fn($n)=>['id'=>(int)$n['id'],'parentId'=>$n['parent_id']?(int)$n['parent_id']:null,'slug'=>$n['slug'],'titleFa'=>$n['title_fa'],'titleEn'=>$n['title_en'],'canManage'=>$actor===1||(int)$n['created_by']===$actor,'depth'=>(int)$n['depth'],'position'=>(int)$n['position'],'published'=>(bool)$n['published'],'version'=>(int)$n['version']],array_values($visible)), 'exams'=>$exams,'capabilities'=>$this->capabilities($actor)];
    }
    private function examRow(int $actor,array $exam,bool $manage,MusicPracticeAccess $access): ?array
    {
            if ($manage && $actor !== 1 && (int)$exam['owner_id'] !== $actor) return null;
            if (!$manage && !(int)$exam['published']) return null;
            $allowed = $access->permitted($actor,$exam);
            if (!$manage && !$allowed && $exam['audience'] !== 'subscribers') return null;
            $definition = json_decode($exam['definition'],true,512,JSON_THROW_ON_ERROR);
            $row = ['id'=>(int)$exam['id'],'nodeId'=>(int)$exam['node_id'],'ownerId'=>(int)$exam['owner_id'],'titleFa'=>$exam['title_fa'],'titleEn'=>$exam['title_en'],'version'=>(int)$exam['version'],'position'=>(int)$exam['position'],'published'=>(bool)$exam['published'],'audience'=>$exam['audience'],'access'=>$allowed,'stageCount'=>$definition['stageCount'],'requiredWins'=>$definition['requiredWins'],'legacyKey'=>$exam['legacy_key']];
            if ($allowed || $manage) $row['config']=$definition['config'];
            if ($allowed) $row += $access->permit($actor,$exam);
            if ($exam['audience']==='subscribers' && $actor !== 1 && $actor !== (int)$exam['owner_id']) $row['accessExpiresAt']=$access->expires($actor,(int)$exam['owner_id'],(int)$exam['id']);
            if ($manage) $row += $this->managedExam($exam);
            return $row;
    }
    private function managedExam(array $exam): array
    {
        $users=array_map(static fn($u)=>['id'=>(int)$u['user_id'],'username'=>$u['username']],$this->store->rows('SELECT u.user_id,u.username FROM f_music_practice_grants g JOIN f_users u ON u.user_id=g.user_id AND u.deleted_at IS NULL WHERE g.exam_id=? ORDER BY u.username',[$exam['id']]));
        return ['userIds'=>array_column($users,'id'),'users'=>$users,'teacherBps'=>(int)$exam['teacher_bps'],'shareVersion'=>(int)$exam['share_version']];
    }
    public function saveNode(int $actor,array $input): array
    {
        $id=MusicPracticeDefinition::integer($input['id']??0,0,PHP_INT_MAX);
        $this->store->require($actor,$id?'edit':'create');
        return $this->store->transaction(function()use($actor,$input,$id){
            $old=$id?$this->store->one('SELECT * FROM f_music_practice_nodes WHERE id=?'.$this->store->lock(),[$id]):null;
            if($id&&!$old)throw new RuntimeException('بخش پیدا نشد.',404);
            if($old&&$actor!==1&&(int)$old['created_by']!==$actor)throw new RuntimeException('بخش متعلق به کاربر دیگری است.',403);
            [$parent,$p]=$this->parentNode($input,$old,$id);
            $published=$input['published']??false;
            if(!is_bool($published))throw new RuntimeException('وضعیت معتبر نیست.',422);
            if($published!== (bool)($old['published']??false))$this->store->require($actor,'publish');
            $slug=$this->slug($input['slug']??'');
            $values=[$parent?:null,$slug,MusicPracticeDefinition::text($input['titleFa']??''),MusicPracticeDefinition::text($input['titleEn']??''),$p?(int)$p['depth']+1:1,MusicPracticeDefinition::integer($input['position']??0,0,100000),(int)$published,$this->store->now()];
            if($id){
                if(($input['version']??null)!==(int)$old['version'])throw new RuntimeException('بخش تغییر کرده است؛ دوباره دریافت کنید.',409);
                $this->store->execute('UPDATE f_music_practice_nodes SET parent_id=?,slug=?,title_fa=?,title_en=?,depth=?,position=?,published=?,updated_at=?,version=version+1 WHERE id=?',[...$values,$id]);
            }else{
                $this->store->execute('INSERT INTO f_music_practice_nodes(parent_id,slug,title_fa,title_en,depth,position,published,updated_at,created_by) VALUES(?,?,?,?,?,?,?,?,?)',[...$values,$actor]);
                $id=(int)$this->store->connection()->lastInsertId();
            }
            $this->store->saveTitles('music_practice_nodes',$id,$values[2],$values[3],$actor);
            return ['id'=>$id];
        });
    }
    private function slug(mixed $value): string
    {
        $slug=MusicPracticeDefinition::text($value,100);
        if(!preg_match('/^[a-z][a-z0-9-]*$/D',$slug))throw new RuntimeException('نشانی بخش معتبر نیست.',422);
        return $slug;
    }
    private function parentNode(array $input,?array $old,int $id): array
    {
        $parent=MusicPracticeDefinition::integer($input['parentId']??0,0,PHP_INT_MAX);
        $p=$parent?$this->store->one('SELECT * FROM f_music_practice_nodes WHERE id=?'.$this->store->lock(),[$parent]):null;
        if(($parent&&!$p)||($parent===$id&&$id)||($p&&(int)$p['depth']>=3))throw new RuntimeException('حداکثر سه سطح بخش مجاز است.',422);
        if($old&&(int)$old['parent_id']!==$parent&&$this->store->one('SELECT id FROM f_music_practice_nodes WHERE parent_id=?',[$id]))throw new RuntimeException('بخش دارای زیرمجموعه قابل جابه‌جایی نیست.',422);
        return [$parent,$p];
    }
    public function saveExam(int $actor,array $input): array
    {
        $id=MusicPracticeDefinition::integer($input['id']??0,0,PHP_INT_MAX);
        $this->store->require($actor,$id?'edit':'create');
        $definition=['stageCount'=>MusicPracticeDefinition::integer($input['stageCount']??5,1,100),'requiredWins'=>MusicPracticeDefinition::integer($input['requiredWins']??10,1,100),'config'=>MusicPracticeDefinition::config($input['config']??[])];
        return $this->store->transaction(function()use($actor,$input,$id,$definition){
            $old=$id?$this->store->one('SELECT * FROM f_music_practice_exams WHERE id=?'.$this->store->lock(),[$id]):null;
            if($id&&!$old)throw new RuntimeException('آزمون پیدا نشد.',404);
            if($old&&$actor!==1&&(int)$old['owner_id']!==$actor)throw new RuntimeException('آزمون متعلق به کاربر دیگری است.',403);
            [$published,$node,$audience,$users]=$this->examSettings($actor,$input,$old);
            $values=[$node,MusicPracticeDefinition::text($input['titleFa']??''),MusicPracticeDefinition::text($input['titleEn']??''),MusicPracticeDefinition::integer($input['position']??0,0,100000),(int)$published,$audience,$this->store->now()];
            [$id,$version]=$this->writeExam($actor,$input,$old,$values);
            $this->store->saveTitles('music_practice_exams',$id,$values[1],$values[2],$actor);
            $this->store->execute('INSERT INTO f_music_practice_versions(exam_id,version,definition,was_published,created_at) VALUES(?,?,?,?,?)',[$id,$version,json_encode($definition,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),(int)$published,$this->store->now()]);
            $this->store->execute('DELETE FROM f_music_practice_grants WHERE exam_id=?',[$id]);
            foreach(array_unique($users) as $user)$this->store->execute('INSERT INTO f_music_practice_grants(exam_id,user_id) VALUES(?,?)',[$id,$user]);
            return ['id'=>$id,'version'=>$version];
        });
    }
    private function examSettings(int $actor,array $input,?array $old): array
    {
        $published=$input['published']??false;
        if(!is_bool($published))throw new RuntimeException('وضعیت معتبر نیست.',422);
        if($published||(bool)($old['published']??false))$this->store->require($actor,'publish');
        $node=MusicPracticeDefinition::integer($input['nodeId']??null,1,PHP_INT_MAX);
        if(!$this->store->one('SELECT id FROM f_music_practice_nodes WHERE id=?',[$node]))throw new RuntimeException('بخش پیدا نشد.',404);
        $audience=$input['audience']??'public';
        if(!in_array($audience,['public','users','students','subscribers'],true))throw new RuntimeException('گروه مخاطبان معتبر نیست.',422);
        $users=$input['userIds']??[];
        if(!is_array($users)||!array_is_list($users)||count($users)>1000)throw new RuntimeException('فهرست مخاطبان معتبر نیست.',422);
        foreach($users as $user)MusicPracticeDefinition::integer($user,1,PHP_INT_MAX);
        if($users){$count=$this->store->one('SELECT COUNT(*) total FROM f_users WHERE deleted_at IS NULL AND user_id IN ('.implode(',',array_fill(0,count(array_unique($users)),'?')).')',array_values(array_unique($users)));if((int)$count['total']!==count(array_unique($users)))throw new RuntimeException('کاربر انتخاب‌شده پیدا نشد.',422);}
        return [$published,$node,$audience,$users];
    }
    private function writeExam(int $actor,array $input,?array $old,array $values): array
    {
        $id=(int)($old['id']??0);$version=1;
        if($id){
            if(($input['version']??null)!==(int)$old['version'])throw new RuntimeException('آزمون تغییر کرده است؛ دوباره دریافت کنید.',409);
            $version=(int)$old['version']+1;
            $this->store->execute('UPDATE f_music_practice_exams SET node_id=?,title_fa=?,title_en=?,position=?,published=?,audience=?,updated_at=?,version=? WHERE id=?',[...$values,$version,$id]);
        }else{
            $this->store->execute('INSERT INTO f_music_practice_exams(node_id,title_fa,title_en,position,published,audience,updated_at,owner_id) VALUES(?,?,?,?,?,?,?,?)',[...$values,$actor]);$id=(int)$this->store->connection()->lastInsertId();
        }
        return [$id,$version];
    }
    private function capabilities(int $actor): array
    {
        return ['create'=>$this->store->allows($actor,'create'),'edit'=>$this->store->allows($actor,'edit'),'publish'=>$this->store->allows($actor,'publish'),'approvePlans'=>$actor===1];
    }
}
