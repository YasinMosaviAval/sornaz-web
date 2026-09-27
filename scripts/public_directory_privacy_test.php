<?php
namespace Core\translation {
    // Translation fixtures contain no private data and avoid loading application state.
    class TranslationService {public static function manager(){return new class {public function get(...$args){return '';}};}}
}
namespace {
$map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require $map[$class];});
$pdo=new \PDO('sqlite::memory:');$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);
function db(){return $GLOBALS['pdo'];}
function app(){return new class {public function getLocale(){return 'en';}};}
function check($ok,$message){if(!$ok)throw new \RuntimeException($message);$GLOBALS['checks']++;}
$checks=0;
$pdo->exec("CREATE TABLE users(user_id INTEGER,username TEXT,type TEXT,gender TEXT,status TEXT,visibility TEXT,email TEXT,phone TEXT,birthday TEXT,register_time TEXT,register_method TEXT,last_login_at TEXT,avatar_file_id INTEGER,deleted_at TEXT);
CREATE TABLE user_roles(user_id INTEGER,role_id INTEGER,deleted_at TEXT);
CREATE TABLE access_system_roles(role_id INTEGER,name TEXT,deleted_at TEXT);
CREATE TABLE academy_branch_members(user_id INTEGER,deleted_at TEXT);
CREATE TABLE media_files(media_file_id INTEGER,user_id INTEGER,disk TEXT,visibility TEXT,collection TEXT,path TEXT,sort_order INTEGER,deleted_at TEXT);
CREATE TABLE user_instruments(user_id INTEGER,is_primary INTEGER,deleted_at TEXT);
CREATE TABLE user_lessons(user_id INTEGER,is_primary INTEGER,deleted_at TEXT);
CREATE TABLE z_user_settings(user_id INTEGER,`key` TEXT,value TEXT,deleted_at TEXT);
CREATE TABLE user_addresses(address_id INTEGER,user_id INTEGER,type TEXT,note TEXT,deleted_at TEXT);
CREATE TABLE user_contacts(user_contact_id INTEGER,user_id INTEGER,type TEXT,value TEXT,note TEXT,deleted_at TEXT);
INSERT INTO users VALUES(7,'public-user','human','female','approved','public','private@example.test','09120000000','2000-01-01','2020-01-01','email','2026-01-01',1,NULL);
INSERT INTO media_files VALUES(1,7,'private','private','avatar','secret/avatar.png',1,NULL);
INSERT INTO user_contacts VALUES(1,7,'email','secret-contact','private note',NULL);
INSERT INTO user_addresses VALUES(1,7,'home','private address note',NULL);");
$service=new \Modules\System\Services\UserService(new \Modules\System\Repositories\UserRepository,new \Modules\System\Services\UserReferralService);
$row=$service->publicDirectory()[0];
check($row['email']===''&&$row['phone']==='','Default contact exposure');
check($row['contacts']===[]&&$row['addresses']===[],'Default address exposure');
check($row['avatar']===null,'Private media exposure');
check(!isset($row['birthday'],$row['last_login_at'],$row['register_time']),'Private metadata exposure');
check($row['availabilities']===[]&&$row['availability_exceptions']===[],'Private schedule exposure');
$pdo->exec("INSERT INTO z_user_settings VALUES(7,'privacy_show_contact','1',NULL)");
$row=$service->publicDirectory()[0];check($row['email']==='private@example.test','Explicit contact consent ignored');
check(!array_key_exists('note',$row['contacts'][0])&&!array_key_exists('user_id',$row['contacts'][0]),'Internal contact fields exposed');
$pdo->exec("UPDATE z_user_settings SET value='0'");check($service->publicDirectory()[0]['contacts']===[],'Revoked consent ignored');
$pdo->exec("UPDATE users SET status='blocked'");check($service->publicDirectory()===[],'Blocked account published');
echo "Public directory privacy: $checks checks passed.\n";
}
