<?php
namespace Core\database {
 class DB {
  public static array $updated=[];
  public static function table(string $table):object{return new class($table){
   public function __construct(private string $table){}
   public function __call(string $name,array $args):mixed{
    if($name==='first')return $this->table==='users'?['user_id'=>2,'type'=>'human']:null;
    if($name==='update'){DB::$updated[$this->table]=$args[0];return 1;}
    return $this;
   }
  };}
 }
}
namespace {
 class TestContactSession {
  private array $values=[];
  public function get(string $key,mixed $default=null):mixed{return $this->values[$key]??$default;}
  public function put(string $key,mixed $value):void{$this->values[$key]=$value;}
  public function forget(string $key):void{unset($this->values[$key]);}
 }
 function session():TestContactSession{static $session;return $session??=new TestContactSession();}
 spl_autoload_register(function($c){$p=dirname(__DIR__).'/'.str_replace('\\','/',$c).'.php';if(is_file($p))require_once $p;});
 function check($v,$m){if(!$v)throw new RuntimeException($m);}
 $service=new \Modules\Analytics\Services\AdminAccountService();
 $service->saveSecurity(2,['password'=>'valid-password','passwordConfirmation'=>'valid-password']);
 $values=\Core\database\DB::$updated['users'];
 check(!array_key_exists('email',$values)&&!array_key_exists('phone',$values),'Password update must preserve contacts');
 check(password_verify('valid-password',$values['password']),'Password must be hashed');
 try{$service->saveSecurity(2,['email'=>'new@example.com']);throw new RuntimeException('Unverified email accepted');}
 catch(RuntimeException $e){check(str_contains($e->getMessage(),'تأیید'),'Unverified email must require OTP');}
 try{$service->saveSecurity(2,['phone'=>'09123456789']);throw new RuntimeException('Unverified phone accepted');}
 catch(RuntimeException $e){check(str_contains($e->getMessage(),'تأیید'),'Unverified phone must require OTP');}
 check(\Modules\Analytics\Services\ContactChangeService::normalize('email',' TEST@EXAMPLE.COM ')==='test@example.com','Email normalization failed');
 try{\Modules\Analytics\Services\ContactChangeService::normalize('phone','123');throw new RuntimeException('Short phone accepted');}
 catch(RuntimeException $e){check(str_contains($e->getMessage(),'تلفن'),'Short phone must be rejected');}
 $repository=new class(new PDO('sqlite::memory:')) extends \Modules\Social\Repositories\SocialRepository {
  public array $settings=[];
  public function transaction(callable $callback):mixed{return $callback();}
  public function query(string $sql,array $params=[]):array{
   if(str_starts_with($sql,'INSERT INTO social_account_settings')){$this->settings=json_decode($params[1],true);return [];}
   if(str_contains($sql,'social_account_settings'))return [['settings_json'=>json_encode($this->settings)]];
   return [['user_id'=>2]];
  }
 };
 $courses=(new ReflectionClass(\Modules\CourseMarket\Services\CourseService::class))->newInstanceWithoutConstructor();
 $social=(new ReflectionClass(\Modules\Social\Services\SocialService::class))->newInstanceWithoutConstructor();
 $learning=new \Modules\Social\Services\LearningService($repository,$courses,$social);
 $settings=$learning->updateSettings(2,['email'=>' public@example.com ']);
 check($settings['email']==='public@example.com','Public email is normalized');
 $learning->updateSettings(2,['website'=>'https://example.com']);
 check($repository->settings['email']==='public@example.com','Unrelated update preserves public email');
 try{$learning->updateSettings(2,['email'=>'invalid-email']);throw new RuntimeException('Invalid email accepted');}catch(RuntimeException $e){check($e->getCode()===422,'Invalid email must fail validation');}
 check($learning->updateSettings(2,['email'=>''])['email']==='','Public email can be removed');
 echo "Account profile: contact preservation and public email checks passed.\n";
}
