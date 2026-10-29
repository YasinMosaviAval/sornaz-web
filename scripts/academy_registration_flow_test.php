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
require __DIR__.'/../core/http/ResponseInterface.php';
require __DIR__.'/../core/http/RedirectResponse.php';
require __DIR__.'/../core/validation/ValidationException.php';
require __DIR__.'/../Modules/System/Services/RegistrationOtpService.php';
require __DIR__.'/../Modules/System/Services/RegistrationOtpPolicy.php';
require __DIR__.'/../Modules/Academy/Controllers/Web/AcademyRegistrationController.php';
require __DIR__.'/../Modules/Academy/Controllers/Api/RegistrationController.php';
function session(){static $s;return $s??=new \Core\session\Session();}
function app(){static $a;return $a??=new class {public string $locale='fa';public function getLocale(){return $this->locale;}public function setLocale($v){$this->locale=$v;}public function container(){return $this;}public function make($class){return new $class;}};}
function locale(){return app()->getLocale();}
function trans($key,$fallback=''){return $fallback;}
function env($key,$default=null){return $default;}
function check($condition,$message){if(!$condition)throw new \Exception($message);}
function auth(){return new class {public function id(){return $GLOBALS['webActor']??null;}public function check(){return(bool)$this->id();}};}
function redirect($url){return new \Core\http\RedirectResponse($url);}
function destination($response){return(new \ReflectionProperty($response,'url'))->getValue($response);}
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
 $_POST=['otp'=>$GLOBALS['otp_code']];$result=$controller->submit();check($result['data']['stage']==='complete','Academy registration must finish immediately');
 check($result['data']['without_branch']===true&&$result['data']['next_section']==='branches','Organization continuation missing');
 check($result['data']['next_url']==='/analytics/admin-panel#branches'&&$result['data']['requires_sign_in']===($authorization===''),'Guest/member continuation mismatch');
 $count=$service->academies;$controller->submit();check($service->academies===$count,'Retry created duplicate academy');
 $before=$service->branches;check($controller->withoutBranch()['data']['without_branch']===true,'Legacy skip compatibility failed');
 $_POST=['step'=>'branch'];check($controller->sendCode()['data']['stage']==='complete','Legacy branch code did not finish registration');
 check($controller->submit()['data']['stage']==='complete','Legacy branch submit did not finish registration');
 check($service->branches===$before,'Completed registration created branch');
 check($_SESSION['registration_otp']===['unrelated'=>'kept'],'Other OTP session changed');
}
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='';$_POST=[];$state=$controller->state();$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
$_POST=['step'=>'branch'];check($controller->sendCode()['status']===409,'Branch stage allowed before academy creation');
check($controller->submit()['status']===409,'Branch submit allowed before academy creation');
$_POST=[];$controller->sendCode();$_POST=['otp'=>$GLOBALS['otp_code']];$controller->submit();
$_SESSION['mobile_academy_registration']['stage']='choice';$_SESSION['mobile_academy_registration']['manager_id']=42;
$_SESSION['mobile_academy_registration']['otp']=['legacy'=>'unused'];$counts=[$service->academies,$service->branches];
check($controller->state()['data']['stage']==='complete','Stored old-client choice was not completed');
check(!isset($_SESSION['mobile_academy_registration']['otp']),'Legacy branch OTP retained');
$_POST=['step'=>'branch'];check($controller->submit()['data']['stage']==='complete','Stored old-client branch submit failed');
check([$service->academies,$service->branches]===$counts,'Legacy choice created records');
$_SERVER['HTTP_X_ACADEMY_FLOW']='forged';check($controller->submit()['status']===403,'Completed flow accepted a forged token');
$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];$_POST=['step'=>'other'];check($controller->submit()['status']===422,'Invalid step accepted after completion');
$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';check($controller->state()['status']===401,'Invalid bearer accepted');
$_SERVER['HTTP_AUTHORIZATION']='Bearer valid';$_POST=[];check($controller->state()['data']['stage']==='academy','Guest completed flow leaked to another actor');
$_SESSION['mobile_academy_registration']['stage']='choice';$_SESSION['mobile_academy_registration']['expires_at']=time()-1;
check($controller->state()['data']['stage']==='academy','Expired stored choice was resumed');
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer admin';$_POST=[];
$state=$controller->state();check($state['data']['otp_required']===false,'Admin should not require OTP');
$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
unset($GLOBALS['otp_code']);
check($controller->sendCode()['data']['otp_required']===false,'Admin send-code should be a no-op');
check(!isset($GLOBALS['otp_code']),'Admin registration sent OTP');
check($controller->submit()['data']['stage']==='complete','Admin registration did not complete without OTP');
$before=$service->branches;$_POST=['step'=>'branch'];check($controller->submit()['data']['stage']==='complete','Admin legacy continuation failed');check($service->branches===$before,'Admin onboarding created a branch');
$_SESSION=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer valid';$_POST=['user_id'=>1];
$state=$controller->state();$_SERVER['HTTP_X_ACADEMY_FLOW']=$state['data']['flow_token'];
check($state['data']['otp_required']===true,'Posted user ID bypassed OTP');
check($controller->submit()['status']===422,'Non-admin registration accepted without OTP');
$web=new class($service,$otp,new \Modules\Academy\Services\AcademyBranchService) extends \Modules\Academy\Controllers\Web\AcademyRegistrationController {
 protected function validatedData():array{return ['academy_name'=>'Academy','register_method'=>'email','email'=>'owner@example.test','username'=>'academy','password'=>'strong-test-password'];}
};
foreach([0,7,1]as$actor){
 $GLOBALS['webActor']=$actor;$_SESSION=['academy_branch_setup'=>['academy_id'=>99,'manager_id'=>88]];$_POST=[];
 $expected=$actor?'/analytics/admin-panel#branches':'/system/login';$before=[$service->academies,$service->branches];
 if($actor!==1){check($web->sendOtp()['status']===200,'Web OTP send failed');$_POST['otp']=$GLOBALS['otp_code'];}
 check(destination($web->store())===$expected,'Web registration kept an onboarding branch stage');
 check($service->academies===$before[0]+1&&$service->branches===$before[1],'Web registration created unexpected records');
 check(!isset($_SESSION['academy_branch_setup']),'Web registration kept branch setup');
 check(str_contains($_SESSION['_flash']['auth_success'],'سازمان'),'Organization success guidance missing');
 foreach(['createMainBranch','branchChoice','finishWithoutBranch','storeMainBranch']as$method){
  $_SESSION=['academy_branch_setup'=>['academy_id'=>99,'manager_id'=>88],'registration_otp'=>['unrelated'=>'kept']];$before=[$service->academies,$service->branches];
  check(destination($web->$method())===$expected,'Legacy web route did not redirect to panel/login');
  check(!isset($_SESSION['academy_branch_setup']),'Legacy web route retained setup');
  check($_SESSION['registration_otp']===['unrelated'=>'kept'],'Legacy route altered unrelated OTP');
  check([$service->academies,$service->branches]===$before,'Legacy web route created records');
 }
 $_SESSION=['academy_branch_setup'=>['academy_id'=>99],'registration_otp'=>['unrelated'=>'kept']];$GLOBALS['otp_code']='no-send';
 $result=$web->sendMainBranchOtp();check($result['status']===409&&$result['data']['next_url']===$expected,'Legacy branch OTP endpoint did not explain Organization continuation');
 check($GLOBALS['otp_code']==='no-send'&&$_SESSION['registration_otp']===['unrelated'=>'kept'],'Legacy branch endpoint sent or changed OTP');
 $_SESSION=[];check(destination($web->branchChoice())===$expected,'Stale completion URL restarted registration');
 check(!isset($_SESSION['_flash']['auth_success']),'Stale completion URL claimed a new academy was registered');
}
check($service->branches===0,'Onboarding created a branch');
$GLOBALS['webActor']=7;app()->setLocale('en');$_SESSION=['academy_branch_setup'=>['academy_id'=>99]];
check(destination($web->branchChoice())==='/analytics/admin-panel#branches','English completion destination differs');
check(str_contains($_SESSION['_flash']['auth_success'],'Use Organization'),'English Organization guidance missing');
app()->setLocale('fa');
echo "Academy registration: immediate guest/member/admin completion, Organization continuation, OTP binding and limits, legacy route/flow compatibility and idempotent retries passed.\n";
}
