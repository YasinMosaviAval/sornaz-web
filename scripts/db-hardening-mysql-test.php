<?php
// Real MySQL, synthetic records, random databases. Only schemas are read from source.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/lib/DatabaseHardeningMigration.php';
$classmap=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($classmap){if(isset($classmap[$class])){require_once $classmap[$class];}});
function db():PDO{return $GLOBALS['hardeningRuntime'];}
function locale():string{return 'fa';}
function session():object{return new class{public function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}
$dsn=getenv('SORNAZ_TEST_MYSQL_DSN');
if(!$dsn){fwrite(STDERR,"Set isolated-test SORNAZ_TEST_MYSQL_DSN/USER/PASSWORD.\n");exit(2);}
$fixture='sornaz_hardening_test_'.bin2hex(random_bytes(6));$restore=$fixture.'_restore';$created=[];$checks=0;
$directory=__DIR__.'/../storage/backups/database-hardening-tests/'.basename($fixture);
function hardeningCheck(bool $ok,string $message):void{if(!$ok){throw new LogicException($message);}$GLOBALS['checks']++;}
function hardeningReject(callable $work):void{try{$work();}catch(PDOException){$GLOBALS['checks']++;return;}throw new LogicException('Constraint did not reject invalid data');}
try{
    $pdo=new PDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $names=$pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    $schemas=[];foreach($names as $name){if(!preg_match('/^[a-z0-9_]+$/',$name)){throw new LogicException('Unexpected table');}$schemas[]=$pdo->query("SHOW CREATE TABLE `$name`")->fetch(PDO::FETCH_NUM)[1];}
    if(!in_array('p_academy_term_invoice_payments',$names,true)){throw new LogicException('Requires pre-migration schemas');}
    $pdo->exec("CREATE DATABASE `$fixture` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created[]=$fixture;$pdo->exec("USE `$fixture`");
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');foreach($schemas as $sql){$pdo->exec($sql);}$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    $pdo->exec("INSERT INTO f_users(user_id,username) VALUES(7,'hardening-fixture'),(8,'other-tenant');
INSERT INTO p_academies(academy_id,user_id) VALUES(1,7),(2,8);
INSERT INTO p_academy_branches(branch_id,academy_id,user_id) VALUES(1,1,7),(2,2,8);
INSERT INTO p_academy_branch_members(member_id,academy_id,branch_id,user_id) VALUES(1,1,1,7),(2,2,2,8);
INSERT INTO p_academy_branch_courses(course_id,branch_id) VALUES(1,1),(2,2);
INSERT INTO p_academy_branch_course_terms(term_id,course_id) VALUES(1,1),(2,2);
INSERT INTO p_academy_branch_course_term_invoices(term_invoice_id,term_id,member_id) VALUES(1,1,1),(2,2,2);
INSERT INTO p_academy_branch_course_term_invoice_installments(term_invoice_installment_id,invoice_id) VALUES(1,1),(2,2);
INSERT INTO f_financial_system_currency(currency_id,code) VALUES(1,'IRT'),(2,'IRR');
INSERT INTO f_world_iran_provinces(province_id) VALUES(1);
INSERT INTO f_world_iran_counties(county_id,province_id) VALUES(1,1);
INSERT INTO f_user_profiles(user_id) VALUES(7);
INSERT INTO f_user_settings(user_id,`key`,value) VALUES(7,'fixture','active');
INSERT INTO f_financial_system_payments(payment_id,invoice_id,payer_id,amount,method,status,reference_code) VALUES(11,1,7,123.45,'cash','completed','legacy-reference');
INSERT INTO f_user_messages(related_entity_type,related_entity_id) VALUES('academy_term_invoice_payments',1);
INSERT INTO f_financial_system_ledger_entries(reference_type,reference_id) VALUES('academy_term_invoice_payments',1);
INSERT INTO f_user_points(user_id,points,reference_type,reference_id) VALUES(7,5,'academy_term_invoice_payments',1);
INSERT INTO p_translations(table_name,table_id,field,locale,version,value) VALUES('users',7,'name','fa',1,'first'),('users',7,'name','fa',1,'second'),('academy_term_invoice_payments',1,'note','fa',1,'payment-note');");
    $source=$pdo->prepare("INSERT INTO p_academy_term_invoice_payments(payment_id,invoice_id,installment_id,user_id,gateway,payment_method,amount,currency,callback_token,authority,reference_id,status,verified_at,created_at,created_by,updated_at,updated_by) VALUES(?,1,1,7,?,?,?,?,?,?,?,?,'2026-01-01 12:00:00','2026-01-01 12:00:00',7,'2026-01-01 12:00:00',7)");
    $source->execute([1,'zarinpal','online','100','IRT',str_repeat('a',64),'AUTH_A','REF_A','paid']);
    $source->execute([2,'zarinpal','online','18446744073709551615','IRR',str_repeat('b',64),'AUTH_B',null,'pending']);
    $source->execute([3,'offline','card_to_card','200','IRT',str_repeat('c',64),null,'REF_C','paid']);
    $migration=new DatabaseHardeningMigration($pdo);
    hardeningCheck(!$migration->inspect()['errors'],'Preflight unexpectedly blocked valid schemas');
    $pdo->exec('INSERT INTO f_user_profiles(user_id) VALUES(999)');
    hardeningCheck(in_array('orphan:f_user_profiles.user_id',$migration->inspect()['errors'],true),'Orphan preflight missed');$pdo->exec('DELETE FROM f_user_profiles WHERE user_id=999');
    $migration->apply($directory.'/before');
    $GLOBALS['hardeningRuntime']=new \Core\database\PrefixedPDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    db()->exec("USE `$fixture`");
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_financial_system_payments')->fetchColumn()===4,'Payment counts');
    hardeningCheck($pdo->query('SELECT reference_code FROM f_financial_system_payments WHERE payment_id=11')->fetchColumn()==='legacy-reference','Legacy receipt overwritten');
    hardeningCheck($pdo->query("SELECT amount FROM f_financial_system_payments WHERE authority='AUTH_B'")->fetchColumn()==='18446744073709551615.00','Unsigned amount truncated');
    hardeningCheck((int)$pdo->query("SELECT table_id FROM p_translations WHERE value='payment-note'")->fetchColumn()===12,'Translation payment reference not remapped');
    hardeningCheck((int)$pdo->query("SELECT related_entity_id FROM f_user_messages WHERE related_entity_type='financial_system_payments'")->fetchColumn()===12,'Notification payment reference not remapped');
    hardeningCheck((int)$pdo->query("SELECT reference_id FROM f_financial_system_ledger_entries WHERE reference_type='financial_system_payments'")->fetchColumn()===12,'Ledger payment reference not remapped');
    hardeningCheck((int)$pdo->query("SELECT reference_id FROM f_user_points WHERE reference_type='financial_system_payments'")->fetchColumn()===12,'Points payment reference not remapped');
    hardeningCheck((int)$pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='p_academy_term_invoice_payments'")->fetchColumn()===0,'Old table retained');
    hardeningCheck($migration->inspect()['translation_duplicate_groups']['p_translations']===1,'Manual translations modified');
    $account=new \Modules\Analytics\Services\AdminAccountService();$settingMethod=new ReflectionMethod($account,'setSetting');
    $settingMethod->invoke($account,7,'fixture','changed',7);
    hardeningCheck($pdo->query("SELECT value FROM f_user_settings WHERE user_id=7 AND `key`='fixture' AND deleted_at IS NULL")->fetchColumn()==='changed','Renamed settings key inaccessible');
    $pdo->exec("UPDATE f_user_settings SET deleted_at='2026-01-02' WHERE user_id=7 AND `key`='fixture'");$settingMethod->invoke($account,7,'fixture','new-active',7);
    hardeningCheck((int)$pdo->query("SELECT COUNT(*) FROM f_user_settings WHERE user_id=7 AND `key`='fixture'")->fetchColumn()===2,'Settings history was resurrected');
    $newId=\Modules\Academy\Services\AcademyPaymentStore::create(['invoice_id'=>2,'installment_id'=>2,'user_id'=>8,'currency'=>'IRT','amount'=>300,'status'=>'paid','verified_at'=>'2026-01-01 12:00:00']);
    $dashboard=new \Modules\Analytics\Services\AdminDashboardService();$own=$dashboard->data(7);$other=$dashboard->data(8);
    hardeningCheck(count($own['recentDeposits'])===2 && count($other['recentDeposits'])===1,'Dashboard deposits escaped branch scope or ignored tuition');
    try{$dashboard->data(7,['branchId'=>2]);throw new LogicException('Foreign branch admitted');}catch(RuntimeException $error){hardeningCheck($error->getCode()!==0||$error->getMessage()!=='Foreign branch admitted','Foreign branch admitted');}
    $pdo->exec("DELETE FROM f_financial_system_payments WHERE payment_id=$newId");
    hardeningReject(fn()=>$pdo->exec('INSERT INTO f_user_profiles(user_id) VALUES(7)'));
    hardeningReject(fn()=>$pdo->exec("INSERT INTO f_user_settings(user_id,`key`) VALUES(7,'fixture')"));
    hardeningReject(fn()=>$pdo->exec('INSERT INTO f_user_profiles(user_id) VALUES(999)'));
    hardeningReject(fn()=>$pdo->exec('INSERT INTO f_world_iran_counties(county_id,province_id) VALUES(2,999)'));
    hardeningReject(fn()=>$pdo->exec('DELETE FROM f_users WHERE user_id=7'));
    $pdo->exec("UPDATE f_user_profiles SET deleted_at='2026-01-02'; INSERT INTO f_user_profiles(user_id) VALUES(7)");
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_user_profiles WHERE user_id=7')->fetchColumn()===2,'Soft delete history lost');
    $migration->apply($directory.'/after');
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_financial_system_payments')->fetchColumn()===4,'Repeated migration duplicated payments');
    $pdo->exec("CREATE DATABASE `$restore` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created[]=$restore;$pdo->exec("USE `$restore`");
    $pdo->exec(file_get_contents($directory.'/after/before.sql'));
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_user_profiles WHERE user_id=7')->fetchColumn()===2,'Generated column backup restoration failed');
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_financial_system_payments')->fetchColumn()===4,'Backup payment restoration failed');
    $rollback=$fixture.'_rollback';$pdo->exec("CREATE DATABASE `$rollback` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created[]=$rollback;$pdo->exec("USE `$rollback`");
    $pdo->exec(file_get_contents($directory.'/before/before.sql'));
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM p_academy_term_invoice_payments')->fetchColumn()===3,'Pre-migration rollback lost tuition');
    hardeningCheck($pdo->query('SELECT amount FROM p_academy_term_invoice_payments WHERE payment_id=2')->fetchColumn()==='18446744073709551615','Rollback truncated amount');
    hardeningCheck((int)$pdo->query('SELECT COUNT(*) FROM f_financial_system_payments')->fetchColumn()===1,'Rollback lost legacy receipt');
    echo "MySQL hardening: $checks checks passed, full-width amounts, mixed receipts, constraints, repeated migration and backup restoration.\n";
}catch(Throwable $error){fwrite(STDERR,'Isolated hardening test failed ('.get_class($error).') at check '.$checks.'. '.($error instanceof LogicException?$error->getMessage():$error->getCode().' / '.($error->errorInfo[1]??''))."\n");file_put_contents($directory.'.local.json',json_encode(['message'=>$error->getMessage(),'line'=>$error->getLine(),'trace'=>$error->getTraceAsString()]));$failed=true;}
finally{if(isset($pdo)){foreach(array_reverse($created) as $database){$pdo->exec("DROP DATABASE `$database`");}}}
exit(isset($failed)?1:0);
