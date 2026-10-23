<?php
namespace Core\database {
 class DB { public static array $rows=[];public static int $calls=0; public static function table(string $table):object{self::$calls++;return new class($table){
  private array $joins=[];public function __construct(private string $table){}
  public function __call(string $name,array $args):mixed{if($name==='join')$this->joins[]=$args[0];if($name==='first'){if(in_array('access_system_roles',$this->joins,true))return DB::$rows['management-role']??null;if(in_array('academy_branch_member_contracts',$this->joins,true))return DB::$rows['management-contract']??null;return DB::$rows[$this->table]??null;}return $this;}
 };}}
}
namespace {
 require_once __DIR__.'/../Modules/Analytics/Services/PanelTableRenderer.php';
 require_once __DIR__.'/../Modules/Analytics/Services/MobilePanelNavigation.php';
 $map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
 spl_autoload_register(static function(string $class)use($map):void{if(isset($map[$class]))require_once $map[$class];});
 $testDb=new \PDO('sqlite::memory:');$testDb->exec('CREATE TABLE auth_remember_tokens(token_hash TEXT PRIMARY KEY,user_id INTEGER,expires_at INTEGER)');
 $testDb->sqliteCreateFunction('NOW', static fn (): string => '2026-10-15 12:00:00');
 $testDb->exec('CREATE TABLE access_system_permissions(permission_id INTEGER,name TEXT,deleted_at TEXT);
 CREATE TABLE user_permissions(permission_id INTEGER,user_id INTEGER,deleted_at TEXT,expires_at TEXT);
 CREATE TABLE user_roles(role_id INTEGER,user_id INTEGER,deleted_at TEXT,expires_at TEXT);
 CREATE TABLE access_system_role_permissions(permission_id INTEGER,role_id INTEGER,deleted_at TEXT);
 CREATE TABLE access_system_roles(role_id INTEGER,deleted_at TEXT);');
 function db(){return $GLOBALS['testDb'];}
 function env($key,$default=null){return $default;}
 if(!function_exists('locale')){function locale():string{return app()->getLocale();}}
 if(!function_exists('e')){function e(mixed $value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}}
 function base_path(string $path=''):string{return dirname(__DIR__).'/'.$path;}
 $testRoot=dirname(__DIR__).'/storage/mobile-panel-test-'.bin2hex(random_bytes(6));
 function storage_path(string $path):string{return $GLOBALS['testRoot'].'/'.$path;}
 register_shutdown_function(static function()use($testRoot){if(session_status()===PHP_SESSION_ACTIVE)session_destroy();foreach(glob($testRoot.'/sessions/sess_*')as$file)unlink($file);if(is_dir($testRoot.'/sessions'))rmdir($testRoot.'/sessions');if(is_dir($testRoot))rmdir($testRoot);});
 function config(string $key,mixed $default=null):mixed{return $key==='app.key'?'isolated-test-key':$default;}
 function session():\Core\session\Session{static $s;return $s??=new \Core\session\Session();}
 function auth():\Core\auth\Auth{return $GLOBALS['testAuth'];}
 function request():\Core\http\Request{return new \Core\http\Request();}
 function app():object{static $a;return $a??=new class{
  public string $locale='fa';public function setLocale(string $locale):void{$this->locale=$locale;}public function getLocale():string{return $this->locale;}
  public function container():object{return new class{public function make(string $class):object{
   if($class===\Modules\Academy\Controllers\Web\AcademyCourseController::class)return new $class($GLOBALS['courseService']);
   if($class===\Modules\Analytics\Controllers\Web\AdminDashboardController::class)return new class{public function index(){return \Core\http\ResponseFactory::json(['actor'=>auth()->id(),'locale'=>app()->getLocale()]);}};
   return new $class();
  }};}
 };}
 function check(bool $ok,string $message):void{if(!$ok)throw new \RuntimeException($message);}
 function field(object $object,string $name):mixed{return(new \ReflectionProperty($object,$name))->getValue($object);}
 $users=new class extends \Modules\System\Repositories\UserRepository{public array $rows=[];public function find(int $userId):?array{return $this->rows[$userId]??null;}};
 $users->rows=[7=>['user_id'=>7,'password'=>'hash','status'=>'approved','type'=>'human'],1=>['user_id'=>1,'password'=>'admin-hash','status'=>'approved','type'=>'admin']];
 $GLOBALS['testAuth']=new \Core\auth\Auth($users);
 $GLOBALS['courseService']=new class extends \Modules\Academy\Services\AcademyCourseService{public array $received=[];public function saveCourse(int $actor,array $data,int $id=0):array{$this->received=compact('actor','data','id');return['id'=>14];}};
 require base_path('Modules/Analytics/Routes/routes.php');require base_path('Modules/Academy/Routes/web.php');require base_path('Modules/Analytics/Routes/api.php');
 if (!in_array('--api-only', $argv, true)) {
 require_once base_path('Modules/Analytics/Controllers/Web/PublicUiPreviewController.php');
 $previewRoute=\Core\router\Router::dispatch('GET','/analytics/public-ui-preview');
 check($previewRoute!==null && $previewRoute['middlewares']===[],'Public design preview must be available without a login');
 $previewResponse=(new \Modules\Analytics\Controllers\Web\PublicUiPreviewController(new \Modules\Analytics\Services\MobilePanelCatalog()))->index();
 $previewView=(new \ReflectionProperty($previewResponse,'view'))->getValue($previewResponse);
 $previewData=(new \ReflectionProperty($previewView,'data'))->getValue($previewView)['previewSections']??[];
 check(count($previewData)>=50,'Public preview omitted panel sections');
 check(!str_contains(json_encode($previewData),'/analytics/admin-account') && !array_key_exists('path',$previewData[0]['actions'][0]??[]),'Public preview leaked action route paths');
 if(!function_exists('locale')){function locale():string{return 'fa';}}
 if(!function_exists('e')){function e(mixed $value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}}
 if(!function_exists('csrf_token')){function csrf_token():string{return 'preview-fixture-token';}}
 if(!function_exists('component')){function component(string $name,array $data=[]):void{extract($data);require base_path('Modules/Analytics/Resources/Views/sections/'.$name.'.php');}}
 $previewSections=$previewData;
 $previewDatabaseReads=\Core\database\DB::$calls;
 ob_start();require base_path('Modules/Analytics/Resources/Views/public-ui-preview.php');$previewHtml=ob_get_clean();
 check(\Core\database\DB::$calls===$previewDatabaseReads,'Public preview read live account or tenant tables');
 check(str_contains($previewHtml,'sornazPreviewData') && str_contains($previewHtml,'notation-preview') && str_contains($previewHtml,'auth-preview') && str_contains($previewHtml,'خروجی اکسل') && str_contains($previewHtml,'خروجی پی‌دی‌اف') && str_contains($previewHtml,'سوابق و دستاوردها') && !str_contains($previewHtml,'/analytics/admin-account'),'Public preview must render original panel shell and actions without private action paths');
 check(!str_contains($previewHtml,'panelNotationFrame') && !str_contains($previewHtml,'کتابخانهٔ نمایشی رابط کاربری'),'Public preview must not load the live notation iframe or add an introduction above the panel');
 check(str_contains($previewHtml,'id="students"') && str_contains($previewHtml,'id="finance"') && str_contains($previewHtml,'studentBranchTabs') && str_contains($previewHtml,'financeSummaryCards'),'Public preview must include the original students and finance layouts');
 check(str_contains($previewHtml,'id="branch-types"') && str_contains($previewHtml,'id="classroom-types"') && str_contains($previewHtml,'id="national-holidays"') && str_contains($previewHtml,'تعطیل نمونه'),'Protected admin designs must use isolated fixture markup');
 $previewRole='user';
 ob_start();require base_path('Modules/Analytics/Resources/Views/public-ui-preview.php');$userPreviewHtml=ob_get_clean();
 check(\Core\database\DB::$calls===$previewDatabaseReads,'Ordinary-user design preview read live account or tenant tables');
 check(str_contains($userPreviewHtml,'data-dashboard-kind="student"') && str_contains($userPreviewHtml,'data-personal-section="1"') && str_contains($userPreviewHtml,'ترم نمونهٔ پیانو') && str_contains($userPreviewHtml,'تمرین مقدماتی'),'Ordinary-user design preview must render the real personal-panel structure with fixture data');
 check(substr_count($userPreviewHtml,'id="finance"')===1 && substr_count($userPreviewHtml,'id="dashboard"')===1,'Ordinary-user design preview duplicated section identifiers');
 }
 $tokens=new \Modules\System\Services\MobileAuthTokenService($users);
 require base_path('Modules/System/Routes/web.php');
 $catalog=new \Modules\Analytics\Services\MobilePanelCatalog();
 $panel=new \Modules\Analytics\Controllers\Api\MobilePanelController($tokens,new \Modules\Analytics\Services\MobilePanelAccess(),$catalog);
 session()->start();$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';
 check(field($panel->index(),'status')===401,'Invalid bearer accepted');
 $_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens->issue($users->rows[7]);$_SERVER['HTTP_ACCEPT_LANGUAGE']='en';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/api/sornaz/v1/panel/dashboard/list';$_SESSION=$originalSession=['_auth_user'=>1,'_auth_password_fingerprint'=>substr(hash('sha256','admin-hash'),0,24),'sentinel'=>'preserved'];
 $data=field($panel->index(),'data');$keys=array_column($data['sections'],'key');
 $ordinary = array_column($data['sections'], null, 'key');
 check(($ordinary['account']['actions']['invite']['method'] ?? '') === 'GET'
   && ($ordinary['account']['actions']['invite']['hidden'] ?? false) === true
   && !($ordinary['account']['actions']['invite']['row'] ?? true),
   'Personal invitation endpoint must be authorized as a hidden non-row read action');
 check(!isset($ordinary['page-guidance']['actions']['save']) && $ordinary['account']['pageGuidance']['canEdit'] === false, 'Ordinary user received guidance editor permission');
 $_SERVER['REQUEST_METHOD']='POST';check(field($panel->execute('page-guidance','save'),'status')===403,'Ordinary user invoked guidance mutation');$_SERVER['REQUEST_METHOD']='GET';
 check(in_array('account',$keys,true)&&in_array('my-classrooms',$keys,true)&&in_array('chat',$keys,true),'Common destinations missing');
 check(in_array('messages',$keys,true)&&in_array('notifications',$keys,true),'Ordinary user inbox missing');
 foreach (field($panel->index(),'data')['sections'] as $section) {
  if ($section['key']==='messages') check(!isset($section['actions']['create'])&&isset($section['actions']['read']),'Ordinary message actions crossed role boundary');
  if ($section['key']==='notifications') check(!isset($section['actions']['create'])&&!isset($section['actions']['publish'])&&isset($section['actions']['read']),'Ordinary notification actions crossed role boundary');
  if ($section['key']==='account') check(!isset($section['actions']['backup'])&&!isset($section['actions']['download-backup']),'Ordinary user export was exposed');
 }
 foreach (['my-points','my-settings','my-gallery','my-lessons','my-schedule','my-finance'] as $personalKey) {
  check(in_array($personalKey,$keys,true),'Personal mobile destination missing: '.$personalKey);
 }
 check(!in_array('branches',$keys,true)&&!in_array('roles',$keys,true),'Ordinary user received management destinations');
 foreach (['awards','badges','experiences','certificates','educations','events','polls','publications'] as $profileKey) {
  check(in_array($profileKey,$keys,true),'Own-profile section missing for ordinary user: '.$profileKey);
 }
 check($_SESSION===$originalSession,'Bearer mutated browser session');
 $result=field($panel->execute('dashboard','list'),'data');
 check($result['actor']===7&&$result['locale']==='en','Controller did not receive bearer identity and locale');
 check(auth()->id()===1,'Original identity not restored');
 check(field($panel->execute('courses','list'),'status')===403,'Ordinary user invoked management operation');
 check(field($panel->execute('../../_test','delete'),'status')===404,'Uncatalogued operation accepted');
 check(field($panel->execute('chat','send'),'status')===405,'GET invoked write');
 \Core\database\DB::$rows=['academy_branch_members'=>['member_id'=>3]];$keys=array_column(field($panel->index(),'data')['sections'],'key');
 check(in_array('notifications',$keys,true)&&!in_array('roles',$keys,true)&&!in_array('branches',$keys,true),'Member boundaries lost');
 \Core\database\DB::$rows['management-role']=['role_id'=>7];$keys=array_column(field($panel->index(),'data')['sections'],'key');
 check(in_array('branches',$keys,true)&&!in_array('roles',$keys,true),'Manager boundaries lost');
 foreach(['posts','post-categories','comments','media','settings'] as $key){
  check(!in_array($key,$keys,true),'Manager received site content section');
  check(field($panel->execute($key,'list'),'status')===403,'Manager invoked global content through mobile API');
 }
 foreach (['academies'=>['academy_id'=>9], 'academy_branches'=>['branch_id'=>5]] as $ownershipTable=>$ownershipRow) {
  \Core\database\DB::$rows=[$ownershipTable=>$ownershipRow];
  $ownerKeys=array_column(field($panel->index(),'data')['sections'],'key');
  check(in_array('branches',$ownerKeys,true)&&!in_array('roles',$ownerKeys,true),'Organization owner capability differs from web or grants site admin');
 }
 \Core\database\DB::$rows=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens->issue($users->rows[1]);$sections=field($panel->index(),'data')['sections'];
 check(in_array('roles',array_column($sections,'key'),true),'Admin role management missing');
 $_SERVER['HTTP_X_SORNAZ_PANEL_FORMS']='2';
 $modern=array_column(field($panel->index(),'data')['sections'],null,'key');
 check(($modern['members']['actions']['create']['formContractVersion']??0)===2,'Modern client did not receive web dialog schema');
 check(in_array('onlineUrl',array_column($modern['classrooms']['actions']['create']['fields'],'key'),true),'Modern classroom fields missing');
 unset($_SERVER['HTTP_X_SORNAZ_PANEL_FORMS']);
 $legacy=array_column(field($panel->index(),'data')['sections'],null,'key');
 check(!isset($legacy['members']['actions']['create']['formContractVersion']),'New dialog schema leaked to old clients');
 foreach($sections as$section)foreach($section['actions']as$action){check((!str_contains($action['path'],'admin-panel')||$action['path']==='/analytics/admin-panel-guide')&&!str_contains($action['path'],'_test'),'HTML or development action exposed');check(\Core\router\Router::dispatch($action['method'],preg_replace('/\{\w+\}/','1',$action['path']))!==null,'Unregistered action');}
 check(isset($modern['page-guidance']['actions']['save']) && $modern['finance']['pageGuidance']['canEdit'] === true, 'Founder guidance editor missing');
 check($modern['page-guidance']['actions']['save']['access'] === 'founder', 'Guidance mutation must remain founder-only');
 check(isset($modern['finance']['actions']['subscriptions'], $modern['finance']['actions']['subscription-pay'], $modern['term-discounts']['actions']['update']), 'Finance and discount operations missing');
 $_SERVER['REQUEST_METHOD']='POST';$_GET=[];$input=['name'=>'Piano','organizationUserId'=>12,'lesson_id'=>3,'teacher_capacity'=>1,'student_capacity'=>5,'user_id'=>999,'sessions'=>[['date'=>'2026-09-12']]];
 $_POST=['payload_b64'=>base64_encode(json_encode($input))];$_REQUEST=$_POST;$previous=$_POST;
 check(field($panel->execute('courses','create'),'status')===200,'Native create failed');
 check($GLOBALS['courseService']->received['actor']===1&&$GLOBALS['courseService']->received['data']===$input,'Payload or actor lost');
 check($_POST===$previous&&$_SESSION===$originalSession,'Request state leaked');
 $_GET=['id'=>'../1'];check(field($panel->execute('courses','update'),'status')===422,'Unsafe parameter accepted');
 $_GET=['value'=>'../outside'];check(field($panel->execute('classroom-categories','delete'),'status')===422,'Unsafe category parameter accepted');
 $definitions=$catalog->sections();
 foreach (['my-gallery','my-lessons','my-schedule','my-finance'] as $personalKey) {
  check(($definitions[$personalKey]['access']??null)==='common','Personal mobile section restricted by academy role');
  check(isset($definitions[$personalKey]['actions']['list']),'Personal mobile list route missing');
 }
 foreach (['my-gallery','my-lessons','my-schedule','my-finance'] as $personalKey) {
  foreach ($definitions[$personalKey]['actions'] as $personalAction) {
   if ($personalAction['method'] !== 'POST') continue;
   $route=\Core\router\Router::dispatch('POST',preg_replace('/\{\w+\}/','1',$personalAction['path']));
   check(in_array('auth',$route['middlewares'],true)&&in_array('csrf',$route['middlewares'],true),'Personal mutation lacks auth or CSRF');
  }
 }
 check($definitions['posts']['rows']==='posts'&&$definitions['comments']['rows']==='comments'&&$definitions['points']['rows']==='rules','List response contracts lost');
 $offlineKeys=array_column($definitions['finance']['actions']['offline']['fields'],'key');
 check(!array_diff(['method','payerName','reference','bankCardType','paymentDate','paymentTime'],$offlineKeys),'Payment form is missing required service fields');
 $mergeKeys=array_column($definitions['account']['actions']['merge']['fields'],'key');
 check(in_array('userId',$mergeKeys,true)&&in_array('memberId',$mergeKeys,true),'Merge form contract lost');
 $authMethod=new \ReflectionMethod($panel,'authenticated');
 $oldOtp=['web'=>'untouched'];$_SESSION['registration_otp']=$oldOtp;
 $authMethod->invoke($panel,function(){check($_SESSION['registration_otp']===null,'Mobile staff inherited web registration OTP');$_SESSION['registration_otp']=['code_hash'=>'fixture','attempts'=>1];return true;},true);
 check($_SESSION['registration_otp']===$oldOtp,'Mobile staff overwrote web OTP');
 $authMethod->invoke($panel,function(){check($_SESSION['registration_otp']['attempts']===1,'Staff OTP lost between API requests');$_SESSION['registration_otp']['attempts']=2;return true;},true);
 check($_SESSION['mobile_staff_otp'][1]['attempts']===2,'OTP attempt count was not retained');
 $browserSelections=['browser'=>'untouched'];$_SESSION['member_user_selections']=$browserSelections;
 $authMethod->invoke($panel,function(){check($_SESSION['member_user_selections']===[],'Mobile selection inherited browser selection');$_SESSION['member_user_selections']=['fixture'=>['actor'=>1,'organization'=>10,'user'=>7,'expires'=>time()+60]];return true;},false,false,true);
 check($_SESSION['member_user_selections']===$browserSelections,'Mobile lookup overwrote browser selections');
 $authMethod->invoke($panel,function(){check(isset($_SESSION['member_user_selections']['fixture']),'Mobile selection lost before enrollment');return true;},false,false,true);
 $authMethod->invoke($panel,function(){check($_SESSION['registration_otp']===null,'Branch OTP inherited browser or staff state');$_SESSION['registration_otp']=['branch'=>'fixture'];return true;},false,true);
 check($_SESSION['registration_otp']===$oldOtp && $_SESSION['mobile_branch_otp'][1]===['branch'=>'fixture'],'Branch OTP isolation failed');
 $users->rows[1]['password']='rotated';check(field($panel->index(),'status')===401,'Rotated bearer accepted');session_destroy();
 echo 'Native panel: '.count($sections)." sections; bearer isolation, roles, real-controller payloads and routing passed.\n";
}
