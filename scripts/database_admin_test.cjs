const assert = require('node:assert/strict');
const {spawnSync} = require('node:child_process');
const path = require('node:path');

// Readable CLI-only PHP fixtures, following the existing financial test harness.
const source = String.raw`<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root=getcwd();$map=require $root.'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($root,$map){if(isset($map[$class]))require_once $map[$class];else{$file=$root.'/'.str_replace(chr(92),'/',$class).'.php';if(is_file($file))require_once $file;}});
function base_path($path=''){return $GLOBALS['root'].($path===''?'':'/'.$path);}
function db(){$GLOBALS['dbReads']++;return $GLOBALS['pdo'];}
function auth(){return new class{function user(){return empty($GLOBALS['actor'])?null:['user_id'=>$GLOBALS['actor'],'type'=>'superadmin'];}function id(){return $GLOBALS['actor']??0;}function check(){return(bool)$this->id();}};}
function q($name){return chr(96).str_replace(chr(96),chr(96).chr(96),$name).chr(96);}
$checks=0;$dbReads=0;$actor=0;
function check($ok,$label){if(!$ok)throw new LogicException($label);$GLOBALS['checks']++;}
function denied($work,$status){try{$work();}catch(RuntimeException $e){check($e->getCode()===$status,'Correct access/input denial');return;}throw new LogicException('Unexpectedly accepted operation');}
function raw(PDO $pdo,string $sql,array $bindings=[]){$query=(new ReflectionMethod(PDO::class,'prepare'))->invoke($pdo,$sql);$query->execute($bindings);return $query;}
function dumpSql($service,array $filters):array{
 $response=$service->download($filters);$stream=(new ReflectionProperty($response,'stream'))->getValue($response);$file=stream_get_meta_data($stream)['uri'];
 check(!str_starts_with(str_replace(chr(92),'/',realpath($file)),str_replace(chr(92),'/',realpath($GLOBALS['root'])).'/'),'Private spool outside project');
 $kind=(new ReflectionProperty($response,'consistency'))->getValue($response);ob_start();$response->send();$sql=ob_get_clean();clearstatcache(true,$file);
 check(!is_file($file),'Private spool removed on close');check(str_ends_with($sql,"-- Sornaz export complete.\n"),'Completed SQL file');return[$sql,$kind];
}
function importSql(PDO $pdo,string $sql):void{
 if($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)==='sqlite'){$pdo->exec($sql);check(true,'SQLite restore');return;}
 $buffer='';$delimiter=';';$quote=null;$comment=false;$lineStart=true;$length=strlen($sql);
 for($i=0;$i<$length;$i++){
  if($lineStart&&$quote===null&&!$comment&&trim($buffer)===''&&preg_match('/\GDELIMITER ([^\r\n]+)\r?\n/A',$sql,$match,0,$i)){$delimiter=$match[1];$i+=strlen($match[0])-1;continue;}
  $c=$sql[$i];$next=$sql[$i+1]??'';
  if($quote===null&&!$comment&&($c==='#'||($c==='-'&&$next==='-'))){while($i<$length&&$sql[$i]!=="\n")$i++;$buffer.="\n";$lineStart=true;continue;}
  if($quote===null&&!$comment&&$c==='/'&&$next==='*'){$comment=true;$buffer.='/*';$i++;continue;}
  if($comment){$buffer.=$c;if($c==='*'&&$next==='/'){$buffer.='/';$i++;$comment=false;}continue;}
  if($quote!==null){$buffer.=$c;if($c===chr(92)&&$quote!==chr(96)&&$next!==''){$buffer.=$next;$i++;}elseif($c===$quote){if($next===$quote){$buffer.=$next;$i++;}else$quote=null;}}
  elseif(in_array($c,["'",'"',chr(96)],true)){$quote=$c;$buffer.=$c;}
  elseif(substr($sql,$i,strlen($delimiter))===$delimiter){if(trim($buffer)!=='')$pdo->exec($buffer);$buffer='';$i+=strlen($delimiter)-1;}
  else$buffer.=$c;
  $lineStart=$c==="\n";
 }
 check(trim($buffer)==='','SQL restore terminated');
}
// FIXTURE RUN START
$service=new Modules\Analytics\Services\AdminDatabaseService();
foreach([0=>401,2=>403,9=>403]as$actor=>$status){$GLOBALS['actor']=$actor;denied(fn()=>$service->tables(),$status);denied(fn()=>$service->rows(['table'=>'users']),$status);denied(fn()=>$service->download(['mode'=>'data']),$status);}
check($dbReads===0,'No database use before founder authorization');$actor=1;
$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];$mysql=getenv('DATABASE_ADMIN_MYSQL')==='1';$databases=[];
if($mysql){
 $dsn=getenv('SORNAZ_TEST_MYSQL_DSN')?:'mysql:host=127.0.0.1;port=3306;charset=utf8mb4';
 if(!preg_match('/host=(?:127\.0\.0\.1|localhost)(?:;|$)/',$dsn)||str_contains($dsn,'dbname='))throw new LogicException('Use a local server-only test DSN');
 $user=getenv('SORNAZ_TEST_MYSQL_USER')?:'root';$password=getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'';$server=new PDO($dsn,$user,$password,$options);$stem='sornaz_db_admin_fixture_'.bin2hex(random_bytes(6));
 foreach(['source','restore','schema','single']as$suffix){$name=$stem.'_'.$suffix;$server->exec('CREATE DATABASE '.q($name).' CHARACTER SET utf8mb4');$databases[]=$name;}
 register_shutdown_function(static function()use($server,&$databases){foreach($databases as$name){if(!preg_match('/^sornaz_db_admin_fixture_[a-f0-9]{12}_(?:source|restore|schema|single)$/D',$name))throw new LogicException('Unsafe cleanup');$server->exec('DROP DATABASE IF EXISTS '.q($name));}});
 $pdo=new Core\database\PrefixedPDO($dsn,$user,$password,$options);raw($pdo,'USE '.q($databases[0]));$target=new PDO($dsn,$user,$password,$options);$target->exec('USE '.q($databases[1]));$schemaTarget=new PDO($dsn,$user,$password,$options);$schemaTarget->exec('USE '.q($databases[2]));$singleTarget=new PDO($dsn,$user,$password,$options);$singleTarget->exec('USE '.q($databases[3]));
}else{$pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,$options);$target=new PDO('sqlite::memory:',null,null,$options);$schemaTarget=new PDO('sqlite::memory:',null,null,$options);$singleTarget=new PDO('sqlite::memory:',null,null,$options);}
$mapped=Core\database\TableNames::physical('users');check($mapped!=='users','Prefix collision fixture');$engine=$mysql?' ENGINE=InnoDB':'';$auto=$mysql?'BIGINT AUTO_INCREMENT PRIMARY KEY':'INTEGER PRIMARY KEY AUTOINCREMENT';
raw($pdo,"CREATE TABLE users(id $auto,secret TEXT)".$engine);raw($pdo,'CREATE TABLE '.q($mapped).'(id INTEGER PRIMARY KEY,secret TEXT)'.$engine);raw($pdo,"INSERT INTO users(id,secret) VALUES(1,'physical-fixture-secret')");raw($pdo,'INSERT INTO '.q($mapped)." VALUES(1,'mapped-fixture-secret')");
$numeric=$mysql?'DECIMAL(30,10)':'TEXT';raw($pdo,"CREATE TABLE records(id $auto,txt TEXT,bin BLOB,amount $numeric,large BIGINT,number DOUBLE,basis INTEGER DEFAULT 11,generated_value INTEGER GENERATED ALWAYS AS (basis+1) STORED)".$engine);
for($i=1;$i<=25;$i++)raw($pdo,'INSERT INTO records(id,txt,bin,amount,large,number) VALUES(?,?,?,?,?,?)',[$i,"فارسی 😀 quote' backslash\\ line\n\0","\x00\xff\x01",'12345678901234567890.1234567890','9223372036854775807',1.2345678901234567]);
raw($pdo,'CREATE UNIQUE INDEX idx_records ON records(id,large)');raw($pdo,'CREATE TABLE audit(message TEXT)'.$engine);raw($pdo,'CREATE VIEW z_records AS SELECT id,txt,generated_value FROM records');raw($pdo,'CREATE VIEW a_records AS SELECT id,txt FROM z_records');
raw($pdo,$mysql?"CREATE TRIGGER fixture_trigger AFTER INSERT ON records FOR EACH ROW BEGIN INSERT INTO audit(message) VALUES('copied trigger; safe'); END":"CREATE TRIGGER fixture_trigger AFTER INSERT ON records BEGIN INSERT INTO audit(message) VALUES('copied trigger; safe'); END");
raw($pdo,'INSERT INTO records(id,txt) VALUES(100,?)',['deleted high-water row']);raw($pdo,'DELETE FROM records WHERE id=100');$odd='strange'.chr(96).'table';raw($pdo,'CREATE TABLE '.q($odd).' ('.q('select').' TEXT)'.$engine);raw($pdo,'INSERT INTO '.q($odd).' VALUES(?)',['quoted identifier']);foreach(range(1,6)as$n)raw($pdo,'CREATE TABLE empty_'.$n.'(value TEXT)'.$engine);
$list=$service->tables();check($list['perPage']===10&&$list['total']===13&&count($list['tables'])===10,'Complete paginated inventory');check(count($service->tables(['page'=>2])['tables'])===3,'Second inventory page');check($service->rows(['table'=>'empty_1'])['stableOrder']===false,'Unkeyed ordering identified');check(count($service->tables(['search'=>'records'])['tables'])===3,'Literal name filter');
check($service->rows(['table'=>'users'])['rows'][0]['secret']==='physical-fixture-secret','Physical table bypasses mapper');check($service->rows(['table'=>$mapped])['rows'][0]['secret']==='mapped-fixture-secret','Mapped table accessible separately');
$page=$service->rows(['table'=>'records','page'=>2]);check(count($page['rows'])===10&&$page['rows'][0]['id']==11&&$page['total']===25,'Row pagination');check($page['rows'][0]['large']==='9223372036854775807','Lossless big integer');check($page['rows'][0]['bin']['value']===base64_encode("\x00\xff\x01"),'Lossless binary JSON');check($page['rows'][0]['generated_value']==12,'Generated values shown');check($service->rows(['table'=>'records','page'=>999])['page']===3,'Page clamp');check($service->rows(['table'=>$odd])['rows'][0]['select']==='quoted identifier','Quoted table/column names');
foreach([0,'1.5',[],false]as$v)denied(fn()=>$service->rows(['table'=>'records','page'=>$v]),422);foreach(['records; DROP TABLE users','information_schema.tables','other_database.users']as$v)denied(fn()=>$service->rows(['table'=>$v]),404);foreach([null,[],false]as$v)denied(fn()=>$service->rows(['table'=>$v]),422);denied(fn()=>$service->download(['mode'=>'restore']),422);denied(fn()=>$service->download(['mode'=>'data','table'=>'../users']),404);check((int)raw($pdo,'SELECT COUNT(*) FROM users')->fetchColumn()===1,'No injected SQL executed');
[$schemaSql]=dumpSql($service,['mode'=>'schema']);importSql($schemaTarget,$schemaSql);check((int)$schemaTarget->query('SELECT COUNT(*) FROM records')->fetchColumn()===0,'Schema-only has no rows');[$fullSql,$consistency]=dumpSql($service,['mode'=>'data']);importSql($target,$fullSql);check($consistency==='transactional-snapshot','Snapshot identified');
foreach(['users',$mapped,'records','audit','a_records','z_records',$odd]as$table){$name=q($table);check(raw($pdo,"SELECT * FROM $name")->fetchAll(PDO::FETCH_ASSOC)==$target->query("SELECT * FROM $name")->fetchAll(PDO::FETCH_ASSOC),'Schema/data/view restore');}
check((float)$target->query('SELECT number FROM records WHERE id=1')->fetchColumn()===(float)raw($pdo,'SELECT number FROM records WHERE id=1')->fetchColumn(),'Exact floating-point round-trip');check(!$pdo->inTransaction(),'Snapshot released');$target->exec("INSERT INTO records(txt) VALUES('after restore')");check((int)$target->lastInsertId()===101,'Auto increment state restored');check((int)$target->query('SELECT COUNT(*) FROM audit')->fetchColumn()===2,'Trigger restored after data without double firing');
[$singleSql]=dumpSql($service,['mode'=>'data','table'=>'users']);importSql($singleTarget,$singleSql);check($singleTarget->query('SELECT secret FROM users')->fetchColumn()==='physical-fixture-secret','Individual schema/data export');[$singleSchema]=dumpSql($service,['mode'=>'schema','table'=>$mapped]);check(!str_contains($singleSchema,'INSERT INTO '.q($mapped)),'Individual schema-only');check(!str_contains($singleSql,'CREATE TABLE '.q('records')),'Individual excludes unrelated tables');check((int)raw($pdo,'SELECT COUNT(*) FROM records')->fetchColumn()===25,'Source rows unchanged');check((int)raw($pdo,'SELECT COUNT(*) FROM audit')->fetchColumn()===1,'Source trigger records unchanged');
if($mysql){
 raw($pdo,'CREATE TABLE fidelity(id INTEGER PRIMARY KEY,latin VARCHAR(30) CHARACTER SET latin1,unicode_text VARCHAR(100) CHARACTER SET utf8mb4,binary_value VARBINARY(10),flags BIT(9),small_value FLOAT,precise_value DOUBLE,huge_value DOUBLE,tiny_value DOUBLE,location POINT,created TIMESTAMP NULL) ENGINE=InnoDB');raw($pdo,"SET time_zone='+03:30'");
 raw($pdo,"INSERT INTO fidelity VALUES(1,?,?,X'00ff00',b'100000001',1.2345678,1.2345678901234567,1.7e308,2.2250738585072014e-308,ST_GeomFromText('POINT(1 2)',4326),'2020-01-01 12:00:00')",['école','فارسی 😀']);
 $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES,true);raw($pdo,'SET NAMES latin1');[$fidelitySql]=dumpSql($service,['mode'=>'data','table'=>'fidelity']);
 check(raw($pdo,'SELECT @@time_zone')->fetchColumn()==='+03:30','Source timezone restored');check(raw($pdo,'SELECT @@character_set_results')->fetchColumn()==='latin1','Source charset restored');check((bool)$pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES),'Emulation restored');check((bool)$pdo->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY),'Buffering restored');$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);check(is_string(raw($pdo,'SELECT CAST(1 AS SIGNED)')->fetchColumn()),'Stringification restored');$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES,false);raw($pdo,'SET NAMES utf8mb4');
 $singleTarget->exec("SET time_zone='+05:00'");importSql($singleTarget,$fidelitySql);check($singleTarget->query('SELECT @@time_zone')->fetchColumn()==='+05:00','Target timezone restored');
 foreach(['HEX(latin)','HEX(unicode_text)','HEX(binary_value)','HEX(flags)','ST_AsText(location)','ST_SRID(location)','UNIX_TIMESTAMP(created)']as$expression)check(raw($pdo,"SELECT $expression FROM fidelity")->fetchColumn()==$singleTarget->query("SELECT $expression FROM fidelity")->fetchColumn(),'Restored fixture: '.$expression);
 $other=q($databases[3]);foreach(['small_value','precise_value','huge_value','tiny_value']as$column)check((int)raw($pdo,"SELECT a.$column=b.$column FROM fidelity a JOIN $other.fidelity b ON a.id=b.id")->fetchColumn()===1,'Exact numeric fixture: '.$column);
 $connection=new Modules\Analytics\Services\AdminDatabaseConnection($pdo);denied(fn()=>$connection->each('SELECT id FROM fidelity',static function(){throw new RuntimeException('Fixture cursor failure',422);}),422);check((int)raw($pdo,'SELECT COUNT(*) FROM fidelity')->fetchColumn()===1,'Failed cursor releases connection');$writer=new PDO($dsn,$user,$password,$options);$writer->exec('USE '.q($databases[0]));$connection->snapshot(function()use($connection,$writer){$count=$connection->scalar('SELECT COUNT(*) FROM audit');$writer->exec("INSERT INTO audit(message) VALUES('concurrent fixture write')");check($connection->scalar('SELECT COUNT(*) FROM audit')==$count,'Snapshot excludes concurrent writes');});check((int)raw($pdo,'SELECT COUNT(*) FROM audit')->fetchColumn()===2,'Concurrent write survives readonly snapshot');
 raw($pdo,'CREATE TABLE nontransactional(id INTEGER) ENGINE=MyISAM');raw($pdo,'INSERT INTO nontransactional VALUES(1)');[$bestEffort,$kind]=dumpSql($service,['mode'=>'data','table'=>'nontransactional']);check($kind==='best-effort'&&str_contains($bestEffort,'Non-transactional tables'),'Explicit nontransactional consistency');
 $marker=q($databases[0]).'.'.q('records');raw($pdo,"CREATE VIEW literal_view AS SELECT '".$marker."' marker FROM records");[$viewSql]=dumpSql($service,['mode'=>'schema','table'=>'literal_view']);importSql($target,$viewSql);check($target->query('SELECT marker FROM literal_view LIMIT 1')->fetchColumn()===$marker,'SQL string schema qualifiers preserved');
 raw($pdo,"SET sql_mode='NO_BACKSLASH_ESCAPES,ANSI_QUOTES'");raw($pdo,"CREATE TABLE mode_fixture(id INTEGER, value VARCHAR(40) DEFAULT 'a\\b') ENGINE=InnoDB");raw($pdo,'INSERT INTO mode_fixture(id) VALUES(1)');raw($pdo,'CREATE VIEW mode_view AS SELECT "value" FROM mode_fixture');
 [$modeSql]=dumpSql($service,['mode'=>'data','table'=>'mode_fixture']);importSql($singleTarget,$modeSql);[$modeView]=dumpSql($service,['mode'=>'schema','table'=>'mode_view']);importSql($singleTarget,$modeView);
 $singleTarget->exec('INSERT INTO mode_fixture(id) VALUES(2)');check(raw($pdo,'SELECT HEX(value) FROM mode_fixture')->fetchColumn()===$singleTarget->query('SELECT HEX(value) FROM mode_fixture WHERE id=2')->fetchColumn(),'Backslash-containing default restores under normalized SQL mode');check($singleTarget->query('SELECT COUNT(*) FROM mode_view')->fetchColumn()==2,'ANSI-quoted view restores');check(str_contains(raw($pdo,'SELECT @@sql_mode')->fetchColumn(),'NO_BACKSLASH_ESCAPES'),'Source SQL mode restored');
 foreach($databases as$name)$server->exec('DROP DATABASE '.q($name));$databases=[];check((int)$server->query("SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE ".$server->quote($stem.'_%'))->fetchColumn()===0,'All temporary databases removed');
}
echo 'Database administration: '.$checks.' founder-access, physical-name, pagination and SQL round-trip checks passed ('.($mysql?'MySQL':'SQLite').").\n";
`;

if (require.main === module) {
  const result = spawnSync('php', [], {cwd:path.resolve(__dirname,'..'),input:source,encoding:'utf8',timeout:120000,maxBuffer:1024*1024});
  process.stdout.write(result.stdout || ''); process.stderr.write(result.stderr || '');
  assert.equal(result.error, undefined); process.exitCode = result.status === 0 ? 0 : 1;
  if (!process.exitCode && process.env.DATABASE_ADMIN_MYSQL !== '1') require('./database_admin_http_test.cjs').run().catch(error=>{console.error(error);process.exitCode=1;});
}
module.exports = {source};
