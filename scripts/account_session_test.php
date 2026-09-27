<?php
$map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require $map[$class];});
function session(){static $s;return $s??=new \Core\session\Session;}
function config($key,$default=null){return 'test-secret';}
function env($key,$default=null){return $default;}
function db(){return $GLOBALS['pdo'];}
function check($ok,$message){if(!$ok)throw new RuntimeException($message);$GLOBALS['checks']++;}
$checks=0;
$pdo=new PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE tracking_user_sessions(session_id TEXT,auth_revoked_at TEXT); CREATE TABLE auth_remember_tokens(token_hash TEXT PRIMARY KEY,user_id INTEGER,expires_at INTEGER)');
$sessionDir=__DIR__.'/../storage/test-sessions-'.bin2hex(random_bytes(6));
mkdir($sessionDir,0700);
session_save_path($sessionDir);
register_shutdown_function(static function()use($sessionDir){if(session_status()===PHP_SESSION_ACTIVE)session_destroy();foreach(glob($sessionDir.'/sess_*') as $file)unlink($file);rmdir($sessionDir);});
session_start();
$users=new class implements \Modules\System\Contracts\UserRepositoryInterface {
    public array $user=['user_id'=>7,'password'=>'hash-one','status'=>'approved'];
    public function all():array{return [$this->user];}
    public function find(int $id):?array{return $id===7?$this->user:null;}
    public function create(array $d):bool{return false;}
    public function update(int $id,array $d):bool{return false;}
    public function delete(int $id):bool{return false;}
};
$auth=new \Core\auth\Auth($users);
$old=session_id();$auth->login(7,true);
check($old!==session_id(),'Login session fixation');check($auth->id()===7,'Login failed');
$remember=session()->get('_auth_remember_token');$store=new \Modules\System\Services\AccountSecurityStore;
check($store->valid($remember,7),'Remember token missing');
$old=session_id();$auth->logout();check($old!==session_id(),'Logout did not rotate');check(!$store->valid($remember,7),'Logout kept remember token');check($auth->id()===null,'Logout kept login');
$auth->login(7);$users->user['password']='hash-two';check($auth->id()===null,'Password change kept web session');
$auth->login(7);$users->user['status']='blocked';check($auth->id()===null,'Blocked user kept session');
$users->user['status']='approved';session()->put('_auth_user',7);check($auth->id()===null,'Unbound legacy session accepted');
session_destroy();echo "Account sessions: $checks checks passed.\n";
