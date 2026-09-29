<?php
// Isolated fixtures only: no application bootstrap, network or production database.
namespace Modules\System\Repositories {
    class UserRepository {
        public array $user = ['user_id'=>7,'status'=>'approved','password'=>'old-hash'];
        public function findByContact($method,$value) { return $value === 'known@example.test' ? $this->user : null; }
        public function find($id) { return $id===7 ? $this->user : null; }
        public function updatePassword($id,$hash) { $this->user['password']=$hash; return $id===7; }
    }
}
namespace Modules\System\Services {
    class MailService { public string $code=''; public function sendPasswordResetOtp($to,$code,$ttl) { $this->code=$code; return true; } }
    class SmsService { public function sendPasswordResetOtp($to,$code,$ttl) { throw new \RuntimeException('Unexpected SMS'); } }
}
namespace {
foreach(['core/session/Session.php','core/csrf/Csrf.php','Modules/System/Services/AccountSecurityStore.php','Modules/System/Services/MobileAuthTokenService.php','Modules/System/Services/PasswordResetOtpService.php'] as $file) require __DIR__.'/../'.$file;
$pdo=new \PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE auth_rate_limits(bucket_key TEXT PRIMARY KEY,attempts INTEGER,expires_at INTEGER); CREATE TABLE auth_remember_tokens(token_hash TEXT PRIMARY KEY,user_id INTEGER,expires_at INTEGER)');
function db(){return $GLOBALS['pdo'];}
function session(){static $session;return $session??=new \Core\session\Session;}
function config($key,$default=null){return 'fixture-signing-secret';}
function check($condition,$label){if(!$condition)throw new \RuntimeException($label);$GLOBALS['checks']++;}
$checks=0;$_SESSION=[];
foreach(['core/http/ResponseInterface.php','core/http/JsonResponse.php','core/http/ResponseFactory.php','core/http/Request.php','core/csrf/CsrfMiddleware.php','Modules/System/Middleware/AuthRateLimitMiddleware.php'] as $file) require __DIR__.'/../'.$file;
function app(){return new class {public function container(){return $this;}public function make($class){return new $class;}};}
$csrf=new \Core\csrf\Csrf;
check(!$csrf->verify(''),'Empty CSRF accepted');
check(!$csrf->verify(null),'Missing CSRF accepted');
$token=$csrf->token();check($csrf->verify($token),'Valid CSRF denied');check(!$csrf->verify('wrong'),'Invalid CSRF accepted');
$guard=new \Core\csrf\CsrfMiddleware;
$_SERVER['REQUEST_URI']='/login';$_SERVER['SCRIPT_NAME']='/index.php';
foreach(['POST','PUT','PATCH','DELETE'] as $method){
    $_SERVER['REQUEST_METHOD']=$method;$_POST=[];unset($_SERVER['HTTP_X_CSRF_TOKEN']);
    check($guard->handle(new \Core\http\Request,fn()=>true) instanceof \Core\http\JsonResponse,'Missing mutation CSRF accepted');
    $_SERVER['HTTP_X_CSRF_TOKEN']=$token;check($guard->handle(new \Core\http\Request,fn()=>true)===true,'Valid mutation CSRF denied');
}
$_SERVER['REQUEST_URI']='/login';$_SERVER['SCRIPT_NAME']='/index.php';$_SERVER['REMOTE_ADDR']='192.0.2.1';$_POST=['identifier'=>'fixture@example.test'];
$limiter=new \Modules\System\Middleware\AuthRateLimitMiddleware;
for($i=0;$i<30;$i++)check($limiter->handle(new \Core\http\Request,fn()=>true)===true,'Login throttled prematurely');
check($limiter->handle(new \Core\http\Request,fn()=>true) instanceof \Core\http\JsonResponse,'Login limiter not enforced');
check(\Core\session\Session::safeInput(['email'=>'x','password'=>'secret','nested'=>['otp'=>'123','name'=>'a']])===['email'=>'x','nested'=>['name'=>'a']],'Old input contains secrets');
$store=new \Modules\System\Services\AccountSecurityStore;
for($i=0;$i<3;$i++)check($store->allow('fixture',3,86400),'Rate limit early');
check(!$store->allow('fixture',3,86400),'Rate limit bypass');
$opaque=$store->issue(7,time()+60);check($store->valid($opaque,7),'Token missing');check(!$store->valid($opaque,8),'Wrong owner accepted');$store->revoke($opaque);check(!$store->valid($opaque,7),'Revoked token accepted');
$expired=$store->issue(7,time()-1);check(!$store->valid($expired,7),'Expired token accepted');
$users=new \Modules\System\Repositories\UserRepository;
$mobile=new \Modules\System\Services\MobileAuthTokenService($users);
$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$mobile->issue($users->user);
check($mobile->userFromRequest()!==null,'Mobile login denied');$mobile->revokeFromRequest();check($mobile->userFromRequest()===null,'Mobile logout not revoked');
$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$mobile->issue($users->user);
$mail=new \Modules\System\Services\MailService;
$reset=new \Modules\System\Services\PasswordResetOtpService($users,$mail,new \Modules\System\Services\SmsService);
$known=$reset->send('email','known@example.test');check($known['ok'],'Reset delivery');check($reset->verify($mail->code)['ok'],'OTP verify');
$data=session()->get('password_reset_otp');$data['expires_at']=time()-1;session()->put('password_reset_otp',$data);
check(!$reset->reset('new-password')['ok'],'Expired verified OTP reset');
$reset->send('email','known@example.test');$reset->verify($mail->code);check($reset->reset('new-password')['ok'],'Reset failed');
check($mobile->userFromRequest()===null,'Password change kept bearer alive');check(!$reset->reset('second-password')['ok'],'OTP reused');
$reset->clear();$unknown=$reset->send('email','unknown@example.test');check($known===$unknown,'Account existence exposed');
check(!$reset->verify($mail->code)['ok'],'Unknown account OTP accepted');
require __DIR__.'/../core/http/RedirectResponse.php';
$_SERVER['REQUEST_METHOD']='POST';$_SERVER['HTTP_ACCEPT']='text/html';
unset($_SERVER['HTTP_X_CSRF_TOKEN']);
$_POST=['identifier'=>'fixture','password'=>'must-not-persist'];
$result=$guard->handle(new \Core\http\Request,fn()=>throw new \RuntimeException('CSRF failure reached login'));
check($result instanceof \Core\http\RedirectResponse,'HTML login CSRF did not return to login');
check((new \ReflectionProperty($result,'url'))->getValue($result)==='/login','CSRF redirected to home');
check(session()->getFlash('_old_input')===['identifier'=>'fixture'],'Login retry persisted password');
check(!empty(session()->getFlash('_errors')['identifier']),'Login retry message missing');
check(session()->getFlash('_errors')===null,'Login error was not consumed');
$_SERVER['HTTP_ACCEPT']='application/json';
check($guard->handle(new \Core\http\Request,fn()=>true) instanceof \Core\http\JsonResponse,'JSON CSRF contract changed');
echo "Account security: $checks checks passed.\n";
}
