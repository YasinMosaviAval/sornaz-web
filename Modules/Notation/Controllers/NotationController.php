<?php
namespace Modules\Notation\Controllers;

use Core\http\ResponseFactory;
use Modules\CourseMarket\Repositories\CourseRepository;
use Modules\Notation\Services\NotationService;
use Modules\System\Repositories\UserRepository;
use Modules\System\Services\MobileAuthTokenService;

class NotationController
{
    private function service():NotationService{return new NotationService(new CourseRepository(db()));}
    private function actor(bool $write=false):int
    {
        $mobile=str_starts_with((string)($_SERVER['REQUEST_URI']??''),'/api/sornaz/v1/music-sheets');
        if($mobile){$user=(new MobileAuthTokenService(new UserRepository()))->userFromRequest();$id=(int)($user['user_id']??0);if(!$id&&trim((string)($_SERVER['HTTP_AUTHORIZATION']??''))!=='')throw new \RuntimeException('Sign in to continue.',401);}
        else{$id=(int)auth()->id();if($write){$token=(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'');if($token===''||!csrf()->verify($token))throw new \RuntimeException('Refresh the page and try again.',403);}}
        if($write&&!$id)throw new \RuntimeException('Sign in to continue.',401);return $id;
    }
    public function page()
    {
        return ResponseFactory::view('Notation::index',['boot'=>['locale'=>locale()==='en'?'en':'fa','userId'=>(int)auth()->id(),'csrf'=>csrf_token(),'api'=>'/music-sheets/api','embedded'=>false]]);
    }
    private function payload():array
    {
        $raw=file_get_contents('php://input',false,null,0,300001);if(strlen($raw)>300000)throw new \RuntimeException('Sheet is too large.',422);
        $data=json_decode($raw,true);if(!is_array($data))throw new \RuntimeException('Invalid score data.',422);return $data;
    }
    private function run(callable $f,bool $write=false)
    {
        try{return ResponseFactory::json(['success'=>true,'data'=>$f($this->actor($write))]);}
        catch(\Throwable $e){$status=in_array($e->getCode(),[401,403,404,409,422],true)?$e->getCode():500;$message=$status===500?'Notation service unavailable.':$e->getMessage();if($status===500)error_log('Notation: '.$e->getMessage());return ResponseFactory::json(['success'=>false,'message'=>$message],$status);}
    }
    public function index(){return $this->run(fn($a)=>$this->service()->listing($a,(string)($_GET['mode']??'all'),max(1,(int)($_GET['page']??1))));}
    public function show(int $id){return $this->run(fn($a)=>$this->service()->show($a,$id));}
    public function create(){return $this->run(fn($a)=>$this->service()->save($a,0,$this->payload()),true);}
    public function update(int $id){return $this->run(fn($a)=>$this->service()->save($a,$id,$this->payload()),true);}
    public function remove(int $id){return $this->run(fn($a)=>$this->service()->remove($a,$id,(int)($this->payload()['version']??0)),true);}
    public function bookmark(int $id){return $this->run(fn($a)=>$this->service()->bookmark($a,$id,($this->payload()['active']??false)===true),true);}
}