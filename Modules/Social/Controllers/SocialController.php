<?php
namespace Modules\Social\Controllers;

use Core\http\ResponseFactory;
use Modules\Social\Repositories\SocialRepository;
use Modules\Social\Services\SocialService;
use Modules\System\Services\MobileAuthTokenService;
use Modules\System\Repositories\UserRepository;
use Modules\Academy\Services\ZarinpalPaymentService;
use Modules\Analytics\Services\ChatService;
use Modules\CourseMarket\Repositories\CourseRepository;
use Modules\CourseMarket\Services\CourseService;
use Modules\CourseMarket\Services\PaymentService;
use Modules\Social\Services\LearningService;

class SocialController
{
    private SocialService $social;
    private CourseService $courses;
    private PaymentService $payment;
    private ChatService $chat;
    public function __construct()
    {
        $this->social=new SocialService(new SocialRepository(db()));
        $r=new CourseRepository(db());$this->courses=new CourseService($r);$this->payment=new PaymentService($r,$this->courses);$this->chat=new ChatService();
    }
    private function actor(): int
    {
        $user=(new MobileAuthTokenService(new UserRepository()))->userFromRequest();
        if(!$user)throw new \RuntimeException('نشست معتبر نیست. دوباره وارد شوید.',401);
        return (int)$user['user_id'];
    }
    private function run(callable $f, bool $public=false)
    {
        try { $actor=$public?(int)((new MobileAuthTokenService(new UserRepository()))->userFromRequest()['user_id']??0):$this->actor(); return ResponseFactory::json(['success'=>true,'data'=>$f($actor)]); }
        catch(\Throwable $e){$code=in_array($e->getCode(),[401,403,404,409,422,500,502,503],true)?$e->getCode():422;
            if($e instanceof \PDOException||!($e instanceof \RuntimeException)){error_log('Social API: '.$e->getMessage());$message='ارتباط با سرویس برقرار نشد.';$code=500;}else $message=$e->getMessage();
            return ResponseFactory::json(['success'=>false,'message'=>$message],$code);
        }
    }
    public function me(){return $this->run(fn($a)=>$this->social->profile($a,$a));}
    public function profile(int $id){return $this->run(fn($a)=>$this->social->profile($a,$id),true);}
    public function updateProfile(){return $this->run(fn($a)=>$this->social->updateProfile($a,$_POST));}
    public function people(){return $this->run(fn($a)=>$this->social->people($a,(string)($_GET['q']??'')));}
    public function followers(int $id){return $this->run(fn($a)=>$this->social->people($a,'',$id,'followers'));}
    public function following(int $id){return $this->run(fn($a)=>$this->social->people($a,'',$id,'following'));}
    public function follow(int $id){return $this->run(fn($a)=>$this->social->follow($a,$id,($_POST['active']??'0')==='1'));}
    public function posts(){return $this->run(fn($a)=>$this->social->posts($a,(string)($_GET['kind']??'post'),(int)($_GET['owner']??0),(int)($_GET['before']??0),($_GET['saved']??'')==='1'),($_GET['saved']??'')!=='1');}
    public function post(int $id){return $this->run(fn($a)=>$this->social->post($a,$id));}
    public function publish(){return $this->run(fn($a)=>$this->social->publish($a,$_POST));}
    public function remove(int $id){return $this->run(fn($a)=>$this->social->remove($a,$id));}
    public function react(int $id){return $this->run(fn($a)=>$this->social->react($a,$id,(string)($_POST['kind']??''),($_POST['active']??'0')==='1'));}
    public function notifications(){return $this->run(fn($a)=>$this->social->notifications($a));}
    public function read(int $id){return $this->run(fn($a)=>$this->social->read($a,$id));}
    public function upload(){return $this->run(fn($a)=>$this->social->upload($a,$_FILES['file']??[]));}
    public function conversations(){return $this->run(fn($a)=>$this->chat->index($a)['conversations']);}
    public function conversation(){return $this->run(function($a){$id=(int)($_POST['user_id']??0);$this->social->user($id);return $this->chat->create($a,['userIds'=>[$id]]);});}
    public function messages(int $id){return $this->run(fn($a)=>$this->chat->messages($a,$id,(int)($_GET['after']??0)));}
    public function send(int $id){return $this->run(function($a)use($id){
        $result=$this->chat->send($a,$id,(string)($_POST['body']??''));
        foreach($this->chat->details($a,$id)['members'] as $member)$this->social->notify((int)$member['id'],$a,'message',$id,'برای شما پیام فرستاد.');return $result;
    });}
    private function learning(): LearningService {return new LearningService(new SocialRepository(db()),$this->courses,$this->social);}
    public function home(){return $this->run(fn($a)=>$this->learning()->home($a),true);}
    public function dashboard(){return $this->run(fn($a)=>$this->learning()->dashboard($a));}
    public function bookmarks(){return $this->run(fn($a)=>$this->learning()->bookmarks($a));}
    public function bookmark(){return $this->run(fn($a)=>$this->learning()->bookmark($a,(string)($_POST['kind']??''),(int)($_POST['id']??0),($_POST['active']??'0')==='1'));}
    public function progress(int $id,int $postId){return $this->run(fn($a)=>$this->learning()->progress($a,$id,$postId,($_POST['completed']??'0')==='1'));}
    public function settings(){return $this->run(fn($a)=>$this->learning()->settings($a));}
    public function updateSettings(){return $this->run(fn($a)=>$this->learning()->updateSettings($a,$_POST));}
    public function courses(){return $this->run(function($a){
        if(!$a&&($_GET['mode']??'catalog')!=='catalog')throw new \RuntimeException('ابتدا وارد شوید.',401);
        $mode=(string)($_GET['mode']??'catalog');$items=$this->courses->listing($a,$mode);
        $owner=(int)($_GET['owner']??0);if($owner)$items=array_values(array_filter($items,fn($c)=>(int)$c['owner_id']===$owner));return $items;
    },true);}
    public function course(int $id){return $this->run(fn($a)=>$this->courses->detail($a,$id),true);}
    public function editCourse(int $id){return $this->run(fn($a)=>$this->courses->owned($a,$id));}
    public function createCourse(){return $this->saveCourse(0);}
    public function updateCourse(int $id){return $this->saveCourse($id);}
    private function saveCourse(int $id){return $this->run(function($a)use($id){$d=json_decode((string)($_POST['payload']??''),true);if(!is_array($d))throw new \RuntimeException('اطلاعات دوره معتبر نیست.',422);return $this->courses->save($a,$id,$d);});}
    public function unlockLesson(int $id,int $postId){return $this->run(fn($a)=>$this->courses->unlockLesson($a,$id,$postId,(string)($_POST['password']??'')));}
    public function courseUpload(int $id){return $this->run(fn($a)=>$this->courses->upload($a,$id,$_FILES['file']??[]));}
    public function buy(int $id){return $this->run(fn($a)=>$this->payment->start($a,$id));}
    public function media(int $id){return $this->stream($id,false);}
    public function courseMedia(int $id){return $this->stream($id,true);}
    private function stream(int $id,bool $course)
    {
        try{$a=(int)((new MobileAuthTokenService(new UserRepository()))->userFromRequest()['user_id']??0);$m=$course?$this->courses->media($a,$id):$this->social->media($a,$id);}catch(\Throwable $e){abort(in_array($e->getCode(),[401,403,404],true)?$e->getCode():404);}
        $path=base_path('storage/'.($course?'course-media':'social-media').'/'.$m['filename']);if(!is_file($path))abort(404);
        $size=filesize($path);$start=0;$end=$size-1;
        header('Content-Type: '.$m['mime']);header('Cache-Control: private, no-store');header('X-Content-Type-Options: nosniff');header('Accept-Ranges: bytes');
        if(isset($_SERVER['HTTP_RANGE'])){
            if(!preg_match('/^bytes=(\d*)-(\d*)$/D',$_SERVER['HTTP_RANGE'],$r)||($r[1]===''&&$r[2]==='')){header('Content-Range: bytes */'.$size);abort(416);}
            if($r[1]==='')$start=max(0,$size-(int)$r[2]);else{$start=(int)$r[1];if($r[2]!=='')$end=min($end,(int)$r[2]);}
            if($start>$end||$start>=$size){header('Content-Range: bytes */'.$size);abort(416);}http_response_code(206);header("Content-Range: bytes $start-$end/$size");
        }
        header('Content-Length: '.($end-$start+1));session_write_close();$f=fopen($path,'rb');fseek($f,$start);$left=$end-$start+1;
        while($left>0&&!feof($f)&&!connection_aborted()){$chunk=fread($f,min(65536,$left));if($chunk===false||$chunk==='')break;echo $chunk;$left-=strlen($chunk);}fclose($f);exit;
    }
}
