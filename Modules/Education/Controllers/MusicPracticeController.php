<?php
namespace Modules\Education\Controllers;

use Core\http\ResponseFactory;
use Core\security\PublicError;
use Modules\Education\Services\MusicPracticeStore;
use Modules\Education\Services\MusicPracticeCatalog;
use Modules\Education\Services\MusicPracticeResults;
use Modules\Education\Services\MusicPracticePlans;
use Modules\Education\Services\MusicPracticePayment;
use Modules\System\Services\MobileAuthTokenService;
use Modules\System\Services\ZarinpalGateway;
use Modules\System\Repositories\UserRepository;

final class MusicPracticeController
{
    public function __construct(private ?ZarinpalGateway $gateway=null) {}
    private function actor(bool $required=false): int
    {
        header('Cache-Control: private, no-store');
        if(str_starts_with($_SERVER['REQUEST_URI']??'','/api/')){
            $user=(new MobileAuthTokenService(new UserRepository()))->userFromRequest();
            $actor=(int)($user['user_id']??0);
            if(!$actor&&!empty($_SERVER['HTTP_AUTHORIZATION']))throw new \RuntimeException('نشست معتبر نیست.',401);
        }else{
            $actor=(int)auth()->id();
            if(($_SERVER['REQUEST_METHOD']??'GET')!=='GET'&&isset($_SERVER['HTTP_X_SORNAZ_ACTOR'])&&(string)$_SERVER['HTTP_X_SORNAZ_ACTOR']!==(string)$actor)throw new \RuntimeException('حساب تغییر کرده است؛ صفحه را تازه کنید.',401);
        }
        header('X-Sornaz-Practice-Actor: '.$actor);
        if($required&&$actor<1)throw new \RuntimeException('ابتدا وارد شوید.',401);
        // These endpoints do not mutate session data. Release the cookie lock
        // after authentication/CSRF so a slow API call cannot block other pages.
        if(session_status()===PHP_SESSION_ACTIVE&&!session_write_close())throw new \RuntimeException('آزادسازی نشست ناموفق بود؛ دوباره تلاش کنید.',503);
        return $actor;
    }
    private function input(): array
    {
        $raw=$_POST['payload']??'';
        if(!is_string($raw)||strlen($raw)>262144)throw new \RuntimeException('حجم درخواست معتبر نیست.',422);
        $input=json_decode($raw,true);
        if(!is_array($input)||array_is_list($input))throw new \RuntimeException('قالب درخواست معتبر نیست.',422);
        return $input;
    }
    private function run(callable $work)
    {
        try{return ResponseFactory::json(['success'=>true,'data'=>$work(new MusicPracticeStore(null,str_starts_with($_SERVER['REQUEST_URI']??'','/exams/api/')))]);}
        catch(\Throwable $error){return ResponseFactory::json(['success'=>false,'message'=>PublicError::message($error)],PublicError::status($error));}
    }
    public function catalog(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->listing($this->actor(),($_GET['manage']??'')==='1'));}
    public function people(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->people($this->actor(true),$_GET['q']??''));}
    public function progress(){return $this->run(fn($s)=>(new MusicPracticeResults($s))->progress($this->actor(true)));}
    public function result(){return $this->run(fn($s)=>(new MusicPracticeResults($s))->store($this->actor(true),$this->input()));}
    public function node(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->saveNode($this->actor(true),$this->input()));}
    public function exam(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->saveExam($this->actor(true),$this->input()));}
    public function deleteExam(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->deleteExam($this->actor(true),$this->input()));}
    public function deleteNode(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->deleteNode($this->actor(true),$this->input()));}
    public function publish(){return $this->run(fn($s)=>(new MusicPracticeCatalog($s))->publish($this->actor(true),$this->input()));}
    public function plans(){return $this->run(function($s){$actor=$this->actor();$exam=isset($_GET['examId'])?filter_var($_GET['examId'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]):0;if($exam===false)throw new \RuntimeException('شناسه آزمون معتبر نیست.',422);return (new MusicPracticePlans($s))->listing($actor,$exam);});}
    public function propose(){return $this->run(fn($s)=>(new MusicPracticePlans($s))->propose($this->actor(true),$this->input()));}
    public function approve(){return $this->run(fn($s)=>(new MusicPracticePlans($s))->approve($this->actor(true),$this->input()));}
    public function share(){return $this->run(fn($s)=>(new MusicPracticePlans($s))->share($this->actor(true),$this->input()));}
    public function purchase(){return $this->run(function($s){$actor=$this->actor(true);$input=$this->input();return (new MusicPracticePayment($s,$this->gateway??new ZarinpalGateway()))->start($actor,\Modules\Education\Services\MusicPracticeDefinition::integer($input['planId']??null,1,PHP_INT_MAX),\Modules\Education\Services\MusicPracticeDefinition::text($input['checkoutKey']??'',32),\Modules\Education\Services\MusicPracticeDefinition::integer($input['planVersion']??null,1,PHP_INT_MAX));});}
    public function callback()
    {
        header('Cache-Control: private, no-store');
        try{(new MusicPracticePayment(new MusicPracticeStore(),$this->gateway??new ZarinpalGateway()))->callback((string)($_GET['token']??''),(string)($_GET['Authority']??''),(string)($_GET['Status']??''));return new \Core\http\RedirectResponse('/exams?payment=complete');}
        catch(\Throwable $error){PublicError::message($error);return new \Core\http\RedirectResponse('/exams?payment=failed');}
    }
    public function page()
    {
        header('Cache-Control: private, no-store');
        return ResponseFactory::view('Education::music-practice',['practiceBoot'=>['api'=>'/exams/api','userId'=>(int)auth()->id(),'csrf'=>csrf_token(),'locale'=>locale()]])->layout('public-app')->title(locale()==='en'?'Tests':'آزمون‌ها');
    }
}
