<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

final class AdminGalleryService
{
    private const COLLECTIONS = ['cover', 'logo', 'intro_video', 'gallery'];
    private const IMAGE_MIMES = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    private const VIDEO_MIMES = ['video/mp4'=>'mp4', 'video/webm'=>'webm', 'video/quicktime'=>'mov'];

    public function data(int $actor): array
    {
        [$owners, $branchAccount] = $this->owners($actor);
        $ids = array_column($owners, 'userId');
        $rows = $ids ? DB::table('media_files')->whereIn('user_id', $ids)->whereIn('collection', self::COLLECTIONS)->whereNull('deleted_at')->orderBy('media_file_id', 'DESC')->get() : [];
        $ownerMap=[];foreach($owners as $owner)$ownerMap[(int)$owner['userId']]=$owner;
        $rows=array_values(array_filter($rows,fn($row)=>!str_starts_with((string)($row['path']??''),'assets/media/users/')));
        return ['owners'=>$owners,'items'=>array_map(fn($row)=>$this->map($row,$ownerMap[(int)$row['user_id']]),$rows),'hideOwnerFilters'=>$branchAccount];
    }

    public function realtimeVersion(int $actor): array
    {
        $data = $this->data($actor);
        return ['resource'=>'gallery','version'=>sha1(json_encode([$data['owners'],$data['items'],$data['hideOwnerFilters']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))];
    }

    public function store(int $actor, array $input, array $file): array
    {
        $owner=$this->allowedOwner($actor,(int)($input['ownerId']??0));
        $collection=$this->collection($input['collection']??'gallery');
        return $this->persist($actor,$owner,$collection,$input,$file,null);
    }

    public function update(int $actor,int $id,array $input,array $file): array
    {
        $row=$this->ownedMedia($actor,$id);$owner=$this->allowedOwner($actor,(int)$row['user_id']);
        return $this->persist($actor,$owner,$this->collection($row['collection']),$input,$file,$row);
    }

    public function delete(int $actor,int $id): void
    {
        $row=$this->ownedMedia($actor,$id);$now=date('Y-m-d H:i:s');
        DB::table('media_files')->where('media_file_id',$id)->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_by'=>$actor]);
    }

    private function persist(int$actor,array$owner,string$collection,array$input,array$file,?array$existing):array
    {
        $hasFile=($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_OK;
        if(!$existing&&!$hasFile)throw new RuntimeException('انتخاب فایل الزامی است.');
        $values=['updated_by'=>$actor];
        if($hasFile){
            $mime=(new \finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);$allowed=$collection==='intro_video'?self::VIDEO_MIMES:($collection==='gallery'?self::IMAGE_MIMES+self::VIDEO_MIMES:self::IMAGE_MIMES);
            if(!isset($allowed[$mime]))throw new RuntimeException('نوع فایل برای این بخش مجاز نیست.');
            $limit=str_starts_with($mime,'video/')?100*1024*1024:10*1024*1024;if((int)$file['size']<1||(int)$file['size']>$limit)throw new RuntimeException('حجم فایل بیش از حد مجاز است.');
            $dir='storage/account-media/'.(int)$owner['userId'].'/'.date('Y/m');$abs=base_path($dir);if(!is_dir($abs)&&!mkdir($abs,0775,true)&&!is_dir($abs))throw new RuntimeException('ایجاد پوشه رسانه ناموفق بود.');
            $filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];$path=$dir.'/'.$filename;if(!move_uploaded_file((string)$file['tmp_name'],base_path($path)))throw new RuntimeException('ذخیره فایل ناموفق بود.');
            $values+=['user_id'=>(int)$owner['userId'],'disk'=>'public','directory'=>$dir,'filename'=>$filename,'extension'=>$allowed[$mime],'mime_type'=>$mime,'type'=>str_starts_with($mime,'video/')?'video':'image','collection'=>$collection,'path'=>$path,'original_filename'=>(string)$file['name'],'fileable_type'=>$owner['kind'],'fileable_id'=>(int)$owner['entityId'],'size'=>(int)$file['size'],'checksum'=>hash_file('sha256',base_path($path)),'visibility'=>'public'];
        }
        if(!$existing){$values+=['user_id'=>(int)$owner['userId'],'collection'=>$collection,'fileable_type'=>$owner['kind'],'fileable_id'=>(int)$owner['entityId'],'created_by'=>$actor];$id=(int)DB::table('media_files')->insertGetId($values);}else{$id=(int)$existing['media_file_id'];DB::table('media_files')->where('media_file_id',$id)->update($values);}
        foreach(['title','summary','description']as$field)$this->setText($id,$field,trim((string)($input[$field]??'')),$actor);
        if(in_array($collection,['cover','logo','intro_video'],true)){
            $others=DB::table('media_files')->where('user_id',(int)$owner['userId'])->where('collection',$collection)->where('media_file_id','!=',$id)->whereNull('deleted_at')->get();$now=date('Y-m-d H:i:s');foreach($others as$item)DB::table('media_files')->where('media_file_id',(int)$item['media_file_id'])->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_by'=>$actor]);
        }
        return $this->map(DB::table('media_files')->where('media_file_id',$id)->first(),$owner);
    }

    private function owners(int$actor):array
    {
        $user=DB::table('users')->where('user_id',$actor)->whereNull('deleted_at')->first();if(!$user)throw new RuntimeException('کاربر معتبر نیست.');
        $branch=DB::table('academy_branches')->where('user_id',$actor)->whereNull('deleted_at')->first();if($branch)return[[ $this->ownerForBranch($branch) ],true];
        $academies=array_merge(DB::table('academies')->where('user_id',$actor)->whereNull('deleted_at')->get(),DB::table('academies')->where('created_by',$actor)->whereNull('deleted_at')->get());
        if(SiteAdminAccess::allows($user))$academies=DB::table('academies')->whereNull('deleted_at')->get();
        $out=[];$isAcademy=($user['type']??'')==='academy';if(!$isAcademy)$out[]=['userId'=>$actor,'entityId'=>$actor,'kind'=>'user','name'=>'من'];
        foreach($academies as$academy){$academyOwner=$this->ownerForAcademy($academy);$out[$academyOwner['kind'].':'.$academyOwner['entityId']]=$academyOwner;foreach(DB::table('academy_branches')->where('academy_id',(int)$academy['academy_id'])->whereNull('deleted_at')->get()as$b){$o=$this->ownerForBranch($b);$out[$o['kind'].':'.$o['entityId']]=$o;}}
        return[array_values($out),false];
    }

    private function ownerForAcademy(array$a):array{$uid=(int)$a['user_id'];return['userId'=>$uid,'entityId'=>(int)$a['academy_id'],'kind'=>'academy','name'=>$this->text('academies',(int)$a['academy_id'],'title')?:$this->text('users',$uid,'full_name')?:'آموزشگاه'];}
    private function ownerForBranch(array$b):array{$uid=(int)$b['user_id'];return['userId'=>$uid,'entityId'=>(int)$b['branch_id'],'kind'=>'branch','name'=>$this->text('academy_branches',(int)$b['branch_id'],'name')?:$this->text('users',$uid,'full_name')?:'شعبه '.(int)$b['branch_id']];}
    private function allowedOwner(int$a,int$uid):array{foreach($this->owners($a)[0]as$o)if((int)$o['userId']===$uid)return$o;throw new RuntimeException('مالک رسانه معتبر نیست.');}
    private function ownedMedia(int$a,int$id):array{$r=DB::table('media_files')->where('media_file_id',$id)->whereNull('deleted_at')->first();if(!$r)$this->deny();$this->allowedOwner($a,(int)$r['user_id']);return$r;}
    private function deny():never{throw new RuntimeException('رسانه یافت نشد.');}
    private function collection(mixed$v):string{$v=(string)$v;if(!in_array($v,self::COLLECTIONS,true))throw new RuntimeException('مجموعه رسانه معتبر نیست.');return$v;}
    private function map(array$r,array$o):array{$id=(int)$r['media_file_id'];return['id'=>$id,'ownerId'=>(int)$r['user_id'],'ownerName'=>$o['name'],'category'=>$r['collection'],'type'=>$r['type'],'title'=>$this->text('media_files',$id,'title')?:($r['original_filename']?:$r['filename']),'summary'=>$this->text('media_files',$id,'summary'),'description'=>$this->text('media_files',$id,'description'),'url'=>'/'.ltrim((string)$r['path'],'/'),'date'=>$r['created_at']];}
    private function text(string$t,int$id,string$f):string{return(string)(TranslationService::manager()->get($t,$id,$f,locale())?:TranslationService::manager()->get($t,$id,$f,'fa')?:'');}
    private function setText(int$id,string$f,string$v,int$a):void{TranslationService::manager()->set('media_files',$id,$f,$v,locale());DB::table('translations')->where('table_name','media_files')->where('table_id',$id)->where('field',$f)->where('locale',locale())->whereNull('deleted_at')->update(['updated_by'=>$a]);}
}
