<?php
// Isolated database and HTTP fixtures; no live database or delivery services.
$map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require $map[$class];});
$pdo=new PDO('sqlite::memory:');$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
function db(){return $GLOBALS['pdo'];}
function base_path($path=''){return dirname(__DIR__).'/'.$path;}
function session(){return new class {public function start(){}public function get($key,$default=null){return $default;}};}
function app(){return new class {public function setLocale($locale){} };}
function transaction($fn){$pdo=db();$pdo->beginTransaction();try{$result=$fn();$pdo->commit();return $result;}catch(Throwable $e){$pdo->rollBack();throw $e;}}
function check($ok,$message){if(!$ok)throw new RuntimeException($message);$GLOBALS['checks']++;}
$checks=0;
$pdo->exec('CREATE TABLE user_messages(user_message_id INTEGER PRIMARY KEY,sender_id INTEGER,receiver_user_id INTEGER,type TEXT,status TEXT,is_read INTEGER,created_at TEXT,updated_at TEXT,created_by INTEGER,updated_by INTEGER);
CREATE TABLE translations(table_name TEXT,table_id INTEGER,field TEXT,locale TEXT,value TEXT,version INTEGER,created_at TEXT,updated_at TEXT,created_by INTEGER,updated_by INTEGER)');
$service=new Modules\System\Services\ContactMessageService;
$id=$service->submit(['name'=>'Visitor','email'=>'visitor@example.test','subject'=>'Question','message'=>'Hello <script>test</script>']);
check($id>0,'Missing contact ID');check((int)$pdo->query('SELECT receiver_user_id FROM user_messages')->fetchColumn()===1,'Wrong contact recipient');
check((int)$pdo->query('SELECT COUNT(*) FROM translations')->fetchColumn()===4,'Missing bilingual contact content');
foreach([[],['name'=>'Visitor','email'=>'invalid','message'=>'Test'],['name'=>['array'],'message'=>'Test']] as $input){try{$service->submit($input);throw new LogicException('Invalid contact accepted');}catch(InvalidArgumentException){$checks++;}}
check($service->submit(['message'=>'Phone account'],['user_id'=>7,'username'=>'Phone user','email'=>null])>0,'Phone account rejected');
check($service->submit(['message'=>'Legacy mobile client'],null,false)>0,'Mobile API compatibility lost');
$before=(int)$pdo->query('SELECT COUNT(*) FROM user_messages')->fetchColumn();
$pdo->exec("CREATE TRIGGER fail_contact_translation BEFORE INSERT ON translations BEGIN SELECT RAISE(ABORT,'fixture failure'); END;");
try{$service->submit(['message'=>'Must roll back'],['user_id'=>7,'username'=>'User']);throw new LogicException('Failed storage accepted');}catch(PDOException){}
check((int)$pdo->query('SELECT COUNT(*) FROM user_messages')->fetchColumn()===$before,'Partial contact record committed');
$_SERVER=['REQUEST_METHOD'=>'GET','REQUEST_URI'=>'/missing','SCRIPT_NAME'=>'/index.php'];
$kernel=new Core\application\Kernel;
ob_start();$kernel->handle();$body=ob_get_clean();check(http_response_code()===404&&$body!=='','Missing route status/body');
$_SERVER['REQUEST_URI']='/api/missing';ob_start();$kernel->handle();$body=json_decode(ob_get_clean(),true);check(http_response_code()===404&&$body['status']===404,'API 404 is not JSON');
Core\router\Router::get('/fixture',fn()=>'Rendered content');$_SERVER['REQUEST_URI']='/fixture';
ob_start();$kernel->handle();$body=ob_get_clean();check(http_response_code()===200&&$body==='Rendered content','String response lost its content');
echo "Core functionality: $checks checks passed.\n";
