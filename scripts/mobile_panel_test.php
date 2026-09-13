<?php
namespace Core\database {
 class DB { public static array $rows=[]; public static function table(string $table):object{return new class($table){
  private array $joins=[];public function __construct(private string $table){}
  public function __call(string $name,array $args):mixed{if($name==='join')$this->joins[]=$args[0];if($name==='first'){if(in_array('access_system_roles',$this->joins,true))return DB::$rows['management-role']??null;if(in_array('academy_branch_member_contracts',$this->joins,true))return DB::$rows['management-contract']??null;return DB::$rows[$this->table]??null;}return $this;}
 };}}
}
namespace {
 spl_autoload_register(function(string $class):void{$path=dirname(__DIR__).'/'.str_replace('\\','/',$class).'.php';if(is_file($path))require_once $path;});
 function base_path(string $path=''):string{return dirname(__DIR__).'/'.$path;}
 function storage_path(string $path):string{return sys_get_temp_dir().'/sornaz-panel-tests/'.$path;}
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
 $users->rows=[7=>['user_id'=>7,'password'=>'hash','type'=>'human'],1=>['user_id'=>1,'password'=>'admin-hash','type'=>'admin']];
 $GLOBALS['testAuth']=new \Core\auth\Auth($users);
 $GLOBALS['courseService']=new class extends \Modules\Academy\Services\AcademyCourseService{public array $received=[];public function saveCourse(int $actor,array $data,int $id=0):array{$this->received=compact('actor','data','id');return['id'=>14];}};
 require base_path('Modules/Analytics/Routes/routes.php');require base_path('Modules/Academy/Routes/web.php');require base_path('Modules/Analytics/Routes/api.php');
 $tokens=new \Modules\System\Services\MobileAuthTokenService($users);
 $catalog=new \Modules\Analytics\Services\MobilePanelCatalog();
 $panel=new \Modules\Analytics\Controllers\Api\MobilePanelController($tokens,new \Modules\Analytics\Services\MobilePanelAccess(),$catalog);
 session()->start();$_SERVER['HTTP_AUTHORIZATION']='Bearer invalid';
 check(field($panel->index(),'status')===401,'Invalid bearer accepted');
 $_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens->issue($users->rows[7]);$_SERVER['HTTP_ACCEPT_LANGUAGE']='en';$_SERVER['REQUEST_METHOD']='GET';$_SERVER['REQUEST_URI']='/api/sornaz/v1/panel/dashboard/list';$_SESSION=['_auth_user'=>1,'sentinel'=>'preserved'];
 $data=field($panel->index(),'data');$keys=array_column($data['sections'],'key');
 check(in_array('account',$keys,true)&&in_array('my-classrooms',$keys,true)&&in_array('chat',$keys,true),'Common destinations missing');
 check(!in_array('branches',$keys,true)&&!in_array('roles',$keys,true),'Ordinary user received management destinations');
 check($_SESSION===['_auth_user'=>1,'sentinel'=>'preserved'],'Bearer mutated browser session');
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
 \Core\database\DB::$rows=[];$_SERVER['HTTP_AUTHORIZATION']='Bearer '.$tokens->issue($users->rows[1]);$sections=field($panel->index(),'data')['sections'];
 check(in_array('roles',array_column($sections,'key'),true),'Admin role management missing');
 foreach($sections as$section)foreach($section['actions']as$action){check(!str_contains($action['path'],'admin-panel')&&!str_contains($action['path'],'_test'),'HTML or development action exposed');check(\Core\router\Router::dispatch($action['method'],preg_replace('/\{\w+\}/','1',$action['path']))!==null,'Unregistered action');}
 $_SERVER['REQUEST_METHOD']='POST';$_GET=[];$input=['name'=>'Piano','organizationUserId'=>12,'lesson_id'=>3,'teacher_capacity'=>1,'student_capacity'=>5,'user_id'=>999,'sessions'=>[['date'=>'2026-09-12']]];
 $_POST=['payload_b64'=>base64_encode(json_encode($input))];$_REQUEST=$_POST;$previous=$_POST;
 check(field($panel->execute('courses','create'),'status')===200,'Native create failed');
 check($GLOBALS['courseService']->received['actor']===1&&$GLOBALS['courseService']->received['data']===$input,'Payload or actor lost');
 check($_POST===$previous&&$_SESSION===['_auth_user'=>1,'sentinel'=>'preserved'],'Request state leaked');
 $_GET=['id'=>'../1'];check(field($panel->execute('courses','update'),'status')===422,'Unsafe parameter accepted');
 $_GET=['value'=>'../outside'];check(field($panel->execute('classroom-categories','delete'),'status')===422,'Unsafe category parameter accepted');
 $definitions=$catalog->sections();
 check($definitions['posts']['rows']==='posts'&&$definitions['comments']['rows']==='comments'&&$definitions['points']['rows']==='rules','List response contracts lost');
 $offlineKeys=array_column($definitions['finance']['actions']['offline']['fields'],'key');
 check(!array_diff(['method','payerName','reference','bankCardType','paymentDate','paymentTime'],$offlineKeys),'Payment form is missing required service fields');
 $mergeKeys=array_column($definitions['account']['actions']['merge']['fields'],'key');
 check(in_array('userId',$mergeKeys,true)&&in_array('memberId',$mergeKeys,true),'Merge form contract lost');
 $users->rows[1]['password']='rotated';check(field($panel->index(),'status')===401,'Rotated bearer accepted');session_destroy();
 echo 'Native panel: '.count($sections)." sections; bearer isolation, roles, real-controller payloads and routing passed.\n";
}
