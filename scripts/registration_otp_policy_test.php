<?php
namespace Modules\System\Repositories {
    class UserRepository {
        public function find(int $id): ?array { return in_array($id,[1,7],true) ? ['user_id'=>$id,'password'=>'stored-test-hash'] : null; }
    }
}
namespace {
require __DIR__.'/../Modules/System/Services/MobileAuthTokenService.php';
require __DIR__.'/../Modules/System/Services/RegistrationOtpPolicy.php';
function config($key,$default=null){return 'local-test-signing-key';}
function auth(){return new class {public function id(){return $GLOBALS['web_actor']??null;}};}
function app(){return new class {public function container(){return $this;}public function make($class){return $GLOBALS['tokens'];}};}
function check($ok,$message){if(!$ok)throw new \RuntimeException($message);}
$tokens=new \Modules\System\Services\MobileAuthTokenService(new \Modules\System\Repositories\UserRepository);
$policy=\Modules\System\Services\RegistrationOtpPolicy::class;
foreach([null,7,1] as $id){$GLOBALS['web_actor']=$id;check($policy::web()===($id===1),'Web actor policy');}
foreach([1,7] as $id){$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens->issue(['user_id'=>$id,'password'=>'stored-test-hash']);check($policy::api()===($id===1),'Signed bearer policy');}
$_POST=['user_id'=>1,'admin'=>true];$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';
check(!$policy::api(),'Untrusted form/header accepted');
$_SERVER['HTTP_AUTHORIZATION']='';check(!$policy::api(),'API incorrectly trusted administrator browser cookie');
$token=$tokens->issue(['user_id'=>1,'password'=>'stored-test-hash']);
[$payload,$signature]=explode('.',$token);$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$payload.'.forged';
check(!$policy::api(),'Invalid signature accepted');
echo "Registration OTP policy: web identity, signed bearer, non-admin, forged fields and credentials passed.\n";
}
