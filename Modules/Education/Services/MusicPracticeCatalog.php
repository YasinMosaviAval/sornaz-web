<?php
namespace Modules\Education\Services;

use RuntimeException;

final class MusicPracticeCatalog
{
    private array $managedUsers=[];
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
            if(!$row||!empty($row['deleted_at']))throw new RuntimeException('مورد پیدا نشد.',404);
            if($actor!==1&&(int)$row[$owner]!==$actor)throw new RuntimeException('اجازهٔ انتشار محتوای کاربر دیگر را ندارید.',403);
            if((int)$row['version']!==$version)throw new RuntimeException('نسخه تغییر کرده است؛ دوباره دریافت کنید.',409);
            $this->store->execute('UPDATE '.$table.' SET published=?,updated_at=?'.($kind==='node'?',version=version+1':'').' WHERE id=?',[(int)$input['published'],$this->store->now(),$id]);
            if($kind==='exam'&&$input['published'])$this->store->execute('UPDATE f_music_practice_versions SET was_published=1 WHERE exam_id=? AND version=?',[$id,$version]);
            return ['id'=>$id,'published'=>$input['published']];
        });
    }
    public function deleteExam(int $actor,array $input): array
    {
        $this->store->require($actor,'delete');
        if(!$this->store->supportsLevelDeletion())throw new RuntimeException('ساختار حذف سطح‌ها روی سرور آماده نیست.',503);
        $id=MusicPracticeDefinition::integer($input['id']??null,1,PHP_INT_MAX);
        return $this->store->transaction(function()use($actor,$input,$id){
            $row=$this->store->one('SELECT * FROM f_music_practice_exams WHERE id=?'.$this->store->lock(),[$id]);
            if(!$row)throw new RuntimeException('سطح پیدا نشد.',404);
            if($actor!==1&&(int)$row['owner_id']!==$actor)throw new RuntimeException('سطح متعلق به شما نیست.',403);
            if(!empty($row['deleted_at']))return ['id'=>$id,'deleted'=>true];
            if(MusicPracticeDefinition::integer($input['version']??null,1,PHP_INT_MAX)!==(int)$row['version'])throw new RuntimeException('سطح تغییر کرده است؛ فهرست را تازه کنید.',409);
            $this->store->execute('UPDATE f_music_practice_exams SET published=0,deleted_at=?,deleted_by=?,updated_at=? WHERE id=?',[$this->store->now(),$actor,$this->store->now(),$id]);
            return ['id'=>$id,'deleted'=>true];
        });
    }
    public function deleteNode(int $actor,array $input): array
    {
        $this->store->require($actor,'delete');
        if(!$this->store->supportsNodeDeletion()||!$this->store->supportsLevelDeletion())throw new RuntimeException('ساختار حذف بخش‌ها روی سرور آماده نیست.',503);
        $id=MusicPracticeDefinition::integer($input['id']??null,1,PHP_INT_MAX);
        return $this->store->transaction(function()use($actor,$input,$id){
            $row=$this->store->one('SELECT * FROM f_music_practice_nodes WHERE id=?'.$this->store->lock(),[$id]);
            if(!$row)throw new RuntimeException('بخش پیدا نشد.',404);
            if($actor!==1&&(int)$row['created_by']!==$actor)throw new RuntimeException('بخش متعلق به شما نیست.',403);
            if(!empty($row['deleted_at']))return ['id'=>$id,'deleted'=>true];
            if(MusicPracticeDefinition::integer($input['version']??null,1,PHP_INT_MAX)!==(int)$row['version'])throw new RuntimeException('بخش تغییر کرده است؛ فهرست را تازه کنید.',409);
            if($this->store->one('SELECT id FROM f_music_practice_nodes WHERE parent_id=? AND deleted_at IS NULL',[$id])||$this->store->one('SELECT id FROM f_music_practice_exams WHERE node_id=? AND deleted_at IS NULL',[$id]))throw new RuntimeException('ابتدا زیر‌بخش‌ها و سطح‌های این بخش را حذف کنید. سوابق آن‌ها حفظ می‌شوند.',409);
            $this->store->execute('UPDATE f_music_practice_nodes SET published=0,deleted_at=?,deleted_by=?,updated_at=? WHERE id=?',[$this->store->now(),$actor,$this->store->now(),$id]);
            return ['id'=>$id,'deleted'=>true];
        });
    }
    public function listing(int $actor, bool $manage = false): array
    {
        if ($manage && !$this->store->allows($actor, 'create') && !$this->store->allows($actor, 'edit') && !$this->store->allows($actor, 'publish') && !$this->store->allows($actor,'delete')) throw new RuntimeException('اجازهٔ مدیریت آزمون‌ها را ندارید.',403);
        $this->store->ready();
        $nodes = $this->store->translatedTitles('music_practice_nodes',$this->store->rows('SELECT n.* FROM f_music_practice_nodes n WHERE 1=1'.$this->store->liveNodeSql('n').' ORDER BY n.depth,n.position,n.id'));
        if($manage)$this->loadManagedUsers($actor);
        $visible = [];
        foreach ($nodes as $node) {
            if (($manage&&($actor===1||(int)$node['published']||(int)$node['created_by']===$actor)) || ((int)$node['published'] && (!$node['parent_id'] || isset($visible[$node['parent_id']])))) $visible[$node['id']] = $node;
        }
        $access = new MusicPracticeAccess($this->store);
        $exams = [];
        $definitions=$this->definitions();
        foreach ($this->store->translatedTitles('music_practice_exams',$definitions) as $exam) {
            if (!isset($visible[$exam['node_id']])) continue;
            $row=$this->examRow($actor,$exam,$manage,$access);
            if($row)$exams[]=$row;
        }
        return ['viewerId'=>$actor,'nodes'=>array_map(static fn($n)=>['id'=>(int)$n['id'],'parentId'=>$n['parent_id']?(int)$n['parent_id']:null,'slug'=>$n['slug'],'titleFa'=>$n['titleFa'],'titleEn'=>$n['titleEn'],'canManage'=>$actor===1||(int)$n['created_by']===$actor,'depth'=>(int)$n['depth'],'position'=>(int)$n['position'],'published'=>(bool)$n['published'],'version'=>(int)$n['version']],array_values($visible)), 'exams'=>$exams]+$this->catalogState($actor,$access);
    }
    private function definitions(): array
    {
        return $this->store->rows('SELECT e.*,v.definition FROM f_music_practice_exams e JOIN f_music_practice_versions v ON v.exam_id=e.id AND v.version=e.version WHERE 1=1'.$this->store->liveExamSql('e').' ORDER BY e.position,e.id');
    }
    private function catalogState(int $actor,MusicPracticeAccess $access): array
    {
        return ['capabilities'=>$this->capabilities($actor),'offlineMessage'=>$this->offlineMessage($actor,$access),'sectionSubscriptions'=>$this->store->supportsSectionSubscriptions(),'levelDeletion'=>$this->store->supportsLevelDeletion(),'nodeDeletion'=>$this->store->supportsNodeDeletion()];
    }
    private function offlineMessage(int $actor,MusicPracticeAccess $access): ?string
    {
        if($this->store->onlineOnly||$actor<1||$access->signingReady())return null;
        return locale()==='en'?'Catalog downloaded; secure offline access is not configured on the server. Please contact the site administrator.':'فهرست دریافت شد؛ امضای امن اجرای آفلاین روی سرور آماده نیست. مدیر سایت باید تنظیمات کلید آفلاین را تکمیل کند.';
    }
    private function examRow(int $actor,array $exam,bool $manage,MusicPracticeAccess $access): ?array
    {
            if ($manage && $actor !== 1 && (int)$exam['owner_id'] !== $actor) return null;
            if (!$manage && !(int)$exam['published']) return null;
            $allowed = $access->permitted($actor,$exam);
            if (!$manage && !$allowed && $exam['audience'] !== 'subscribers') return null;
            $definition = json_decode($exam['definition'],true,512,JSON_THROW_ON_ERROR);
            $row = ['id'=>(int)$exam['id'],'nodeId'=>(int)$exam['node_id'],'ownerId'=>(int)$exam['owner_id'],'titleFa'=>$exam['titleFa'],'titleEn'=>$exam['titleEn'],'version'=>(int)$exam['version'],'position'=>(int)$exam['position'],'published'=>(bool)$exam['published'],'audience'=>$exam['audience'],'access'=>$allowed,'stageCount'=>$definition['stageCount'],'requiredWins'=>$definition['requiredWins'],'legacyKey'=>$exam['legacy_key']];
            if ($allowed || $manage) $row['config']=$definition['config'];
            if ($allowed && !$this->store->onlineOnly && $access->signingReady()) $row += $access->permit($actor,$exam);
            if ($exam['audience']==='subscribers' && $actor !== 1 && $actor !== (int)$exam['owner_id']) $row['accessExpiresAt']=$access->expires($actor,(int)$exam['owner_id'],(int)$exam['id']);
            if ($manage) $row += $this->managedExam($exam);
            return $row;
    }
    private function managedExam(array $exam): array
    {
        $users=$this->managedUsers[$exam['id']]??[];
        return ['userIds'=>array_column($users,'id'),'users'=>$users,'teacherBps'=>(int)$exam['teacher_bps'],'shareVersion'=>(int)$exam['share_version']];
    }
    private function loadManagedUsers(int $actor): void
    {
        $this->managedUsers=[];
        $scope=$actor===1?'':' WHERE e.owner_id=?';
        foreach($this->store->rows('SELECT g.exam_id,u.user_id,u.username FROM f_music_practice_grants g JOIN f_users u ON u.user_id=g.user_id AND u.deleted_at IS NULL JOIN f_music_practice_exams e ON e.id=g.exam_id'.$scope.' ORDER BY u.username',$actor===1?[]:[$actor]) as $user){
            $this->managedUsers[$user['exam_id']][]=['id'=>(int)$user['user_id'],'username'=>$user['username']];
        }
    }
    public function saveNode(int $actor,array $input): array
    {
        $id=MusicPracticeDefinition::integer($input['id']??0,0,PHP_INT_MAX);
        $this->store->require($actor,$id?'edit':'create');
        return $this->store->transaction(function()use($actor,$input,$id){
            $old=$id?$this->store->one('SELECT * FROM f_music_practice_nodes WHERE id=?'.$this->store->lock(),[$id]):null;
            if($id&&(!$old||!empty($old['deleted_at'])))throw new RuntimeException('بخش پیدا نشد.',404);
            if($old&&$actor!==1&&(int)$old['created_by']!==$actor)throw new RuntimeException('بخش متعلق به کاربر دیگری است.',403);
            [$parent,$p]=$this->parentNode($input,$old,$id);
            $published=$input['published']??false;
            if(!is_bool($published))throw new RuntimeException('وضعیت معتبر نیست.',422);
            if($published!== (bool)($old['published']??false))$this->store->require($actor,'publish');
            $values=[$parent?:null,$this->nodeSlug($input,$id),$p?(int)$p['depth']+1:1,MusicPracticeDefinition::integer($input['position']??0,0,100000),(int)$published,$this->store->now()];
            if($id){
                if(($input['version']??null)!==(int)$old['version'])throw new RuntimeException('بخش تغییر کرده است؛ دوباره دریافت کنید.',409);
                $this->store->execute('UPDATE f_music_practice_nodes SET parent_id=?,slug=?,depth=?,position=?,published=?,updated_at=?,version=version+1 WHERE id=?',[...$values,$id]);
            }else{
                $id=$this->store->insertTitled('music_practice_nodes',array_combine(['parent_id','slug','depth','position','published','updated_at','created_by'],[...$values,$actor]));
            }
            $this->store->saveTitles('music_practice_nodes',$id,MusicPracticeDefinition::text($input['titleFa']??''),MusicPracticeDefinition::text($input['titleEn']??''),$actor);
            return ['id'=>$id];
        });
    }
    private function nodeSlug(array $input,int $id): string
    {
        $slug=$this->slug($input['slug']??'',$input['titleEn']??'');
        if($this->store->one('SELECT id FROM f_music_practice_nodes WHERE slug=? AND id<>?',[$slug,$id]))throw new RuntimeException('این نشانی بخش قبلاً استفاده شده است؛ نشانی دیگری انتخاب کنید.',422);
        return $slug;
    }
    private function slug(mixed $value,mixed $title=''): string
    {
        if(is_string($value)&&trim($value)===''&&is_string($title)){
            $value=trim((string)preg_replace('/[^a-z0-9]+/','-',strtolower($title)),'-');
            if($value!==''&&!preg_match('/^[a-z]/',$value))$value='section-'.$value;
            $value=substr($value,0,100);
        }
        $slug=strtolower(MusicPracticeDefinition::text($value,100));
        if(!preg_match('/^[a-z][a-z0-9-]*$/D',$slug))throw new RuntimeException('نشانی بخش باید با حرف انگلیسی شروع شود و فقط حرف انگلیسی، عدد یا خط تیره داشته باشد؛ مانند ear-training.',422);
        return $slug;
    }
    private function parentNode(array $input,?array $old,int $id): array
    {
        $parent=MusicPracticeDefinition::integer($input['parentId']??0,0,PHP_INT_MAX);
        $p=$parent?$this->store->one('SELECT * FROM f_music_practice_nodes WHERE id=?'.$this->store->lock(),[$parent]):null;
        if(($parent&&(!$p||!empty($p['deleted_at'])))||($parent===$id&&$id)||($p&&(int)$p['depth']>=3))throw new RuntimeException('حداکثر سه سطح بخش مجاز است.',422);
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
            if($id&&(!$old||!empty($old['deleted_at'])))throw new RuntimeException('آزمون پیدا نشد.',404);
            if($old&&$actor!==1&&(int)$old['owner_id']!==$actor)throw new RuntimeException('آزمون متعلق به کاربر دیگری است.',403);
            [$published,$node,$audience,$users]=$this->examSettings($actor,$input,$old);
            $fa=MusicPracticeDefinition::text($input['titleFa']??'');$en=MusicPracticeDefinition::text($input['titleEn']??'');
            $values=[$node,MusicPracticeDefinition::integer($input['position']??0,0,100000),(int)$published,$audience,$this->store->now()];
            [$id,$version]=$this->writeExam($actor,$input,$old,$values);
            $this->store->saveTitles('music_practice_exams',$id,$fa,$en,$actor);
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
        if(!$this->store->one('SELECT id FROM f_music_practice_nodes WHERE id=?'.$this->store->liveNodeSql().$this->store->lock(),[$node]))throw new RuntimeException('بخش پیدا نشد.',404);
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
            $this->store->execute('UPDATE f_music_practice_exams SET node_id=?,position=?,published=?,audience=?,updated_at=?,version=? WHERE id=?',[...$values,$version,$id]);
        }else{
            $id=$this->store->insertTitled('music_practice_exams',['node_id'=>$values[0],'position'=>$values[1],'published'=>$values[2],'audience'=>$values[3],'updated_at'=>$values[4],'owner_id'=>$actor]);
        }
        return [$id,$version];
    }
    private function capabilities(int $actor): array
    {
        return ['create'=>$this->store->allows($actor,'create'),'edit'=>$this->store->allows($actor,'edit'),'publish'=>$this->store->allows($actor,'publish'),'delete'=>$this->store->allows($actor,'delete'),'approvePlans'=>$actor===1];
    }
}
