<?php
namespace Core\http { class ResponseFactory {public static function json($data,$status=200){return ['status'=>$status,'data'=>$data];}} }
namespace Core\database { class DB {public static function table($table){return new class {public function where(...$a){return $this;}public function first(){return ['created_by'=>$GLOBALS['creator']??42];}};}} }
namespace Modules\System\Services {
 class MobileAuthTokenService {public function userFromRequest(){return match($_SERVER['HTTP_AUTHORIZATION']??''){'Bearer valid'=>['user_id'=>7],'Bearer admin'=>['user_id'=>1],default=>null};}}
 class MailService {public function sendRegistrationOtp($to,$code,$minutes){$GLOBALS['otp_code']=$code;return true;}}
 class SmsService {public function sendRegistrationOtp($to,$code,$minutes){$GLOBALS['otp_code']=$code;return true;}}
}
namespace Modules\Academy\Services {
 class AcademyBranchService {}
 class AcademyRegistrationService {
  public int $academies=0,$branches=0;public array $managers=[];
  public function register(array $data,?int $actor=null):int {$this->academies++;$GLOBALS['creator']=$actor?:42;return 42;}
  public function registerMainBranch(int $academy,int $manager,array $data):int {$this->branches++;$this->managers[]=$manager;return 90;}
 }
}
namespace {
require __DIR__.'/../core/session/Session.php';
require __DIR__.'/../core/validation/ValidationException.php';
require __DIR__.'/../Modules/System/Services/RegistrationOtpService.php';
require __DIR__.'/../Modules/Academy/Controllers/Web/AcademyRegistrationController.php';
require __DIR__.'/../Modules/Academy/Controllers/Api/RegistrationController.php';
function session(){static $s;return $s??=new \Core\session\Session();}
function app(){static $a;return $a??=new class {public string $locale='fa';public function getLocale(){return $this->locale;}public function setLocale($v){$this->locale=$v;}public function container(){return $this;}public function make($class){return new $class;}};}
function locale(){return app()->getLocale();}
function trans($key,$fallback=''){return $fallback;}
function env($key,$default=null){return $default;}
function check($condition,$message){if(!$condition)throw new \Exception($message);}
$service=new \Modules\Academy\Services\AcademyRegistrationService;
$otp=new \Modules\System\Services\RegistrationOtpService(new \Modules\System\Services\MailService,new \Modules\System\Services\SmsService);
$controller=new class($service,$otp,new \Modules\Academy\Services\AcademyBranchService) extends \Modules\Academy\Controllers\Api\RegistrationController {
 protected function validatedData():array{return ['academy_name'=>$_POST['academy_name']??'Academy','register_method'=>'email','email'=>'owner@example.test','username'=>'academy','password'=>'strong-test-password'];}
 protected function validatedBranchData():array{return ['name'=>'Main','register_method'=>'email','email'=>'branch@example.test','username'=>'branch','password'=>'strong-test-password'];}
};
foreach(['','Bearer valid'] as $authorization){
 $_SESSION=['registration_otp'=>['unrelated'=>'kept']];$_SERVER['HTTP_AUTHORIZATION']=$authorization;$_POST=[];
 $state=$controller->state();check($state['data']['stage']==='academy','Initial stage');
 $_SERVER['HTTP_X_ACADEMY_FLOW']='forged';check($controller->sendCode()['status']===403,'Forged flow accepted');
 $_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
 check($controller->sendCode()['status']===200,'OTP send');
 check($controller->sendCode()['status']===429,'Resend limit lost');
 $_POST=['otp'=>'000000'];check($controller->submit()['status']===422,'Incorrect OTP accepted');
 $_POST=['otp'=>$GLOBALS['otp_code'],'academy_name'=>'Changed'];check($controller->submit()['status']===422,'Changed data accepted');
 $_POST=['otp'=>$GLOBALS['otp_code']];$result=$controller->submit();check($result['data']['stage']==='choice','Optional branch choice missing');
 $count=$service->academies;$controller->submit();check($service->academies===$count,'Retry created duplicate academy');
 $before=$service->branches;check($controller->withoutBranch()['data']['without_branch']===true,'Skip failed');check($service->branches===$before,'Skip created branch');
 check($_SESSION['registration_otp']===['unrelated'=>'kept'],'Other OTP session changed');
}
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='';$_POST=[];$state=$controller->state();$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
$controller->sendCode();$_POST=['otp'=>$GLOBALS['otp_code']];$controller->submit();
$_POST=['step'=>'branch'];check($controller->sendCode()['status']===200,'Branch code failed');
$_POST=['step'=>'branch','otp'=>$GLOBALS['otp_code']];check($controller->submit()['data']['stage']==='complete','Branch completion failed');
check(end($service->managers)===42,'Guest academy owner lost');$count=$service->branches;$controller->submit();check($service->branches===$count,'Duplicate branch on retry');
$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';check($controller->state()['status']===401,'Invalid bearer accepted');
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer admin';$_POST=[];
$state=$controller->state();check($state['data']['otp_required']===false,'Admin should not require OTP');
$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
unset($GLOBALS['otp_code']);
check($controller->sendCode()['data']['otp_required']===false,'Admin send-code should be a no-op');
check(!isset($GLOBALS['otp_code']),'Admin registration sent OTP');
check($controller->submit()['data']['stage']==='choice','Admin academy needs OTP');
$_POST=['step'=>'branch'];check($controller->submit()['data']['stage']==='complete','Admin branch needs OTP');
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer valid';$_POST=['user_id'=>1];
$state=$controller->state();$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
check($state['data']['otp_required']===true,'Posted user ID bypassed OTP');
check($controller->submit()['status']===422,'Non-admin registration accepted without OTP');
echo "Academy registration: guest/member flows, OTP binding and limits, optional no-branch completion, guest ownership and idempotent retries passed.\n";
}
