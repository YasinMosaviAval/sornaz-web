<?php
// Synthetic MySQL records only; source database is read for schemas, never for rows.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/lib/InitialDatabase.php';
$dsn=getenv('SORNAZ_TEST_MYSQL_DSN');if(!$dsn){fwrite(STDERR,"Set isolated-test connection variables.\n");exit(2);}
$fixture='sornaz_initial_test_'.bin2hex(random_bytes(6));$restore=$fixture.'_restore';$created=[];$checks=0;
$directory=__DIR__.'/../storage/backups/initial-version-tests/'.$fixture;
function initialCheck(bool $ok,string $message):void{if(!$ok){throw new LogicException($message);}$GLOBALS['checks']++;}
final class InitialFixturePDO extends PDO
{
    public bool $failAfterPostDelete=false;
    public function exec(string $statement):int|false
    {
        $result=parent::exec($statement);
        if($this->failAfterPostDelete&&str_starts_with($statement,'DELETE FROM `f_posts`')){$this->failAfterPostDelete=false;throw new RuntimeException('Synthetic failure during reset');}
        return $result;
    }
}
try{
    $pdo=new InitialFixturePDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $schemas=[];foreach($pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN) as $name){if(!preg_match('/^[fp]_[a-z0-9_]+$/',$name)){throw new LogicException('Unexpected schema');}$schemas[]=$pdo->query("SHOW CREATE TABLE `$name`")->fetch(PDO::FETCH_NUM)[1];}
    $pdo->exec("CREATE DATABASE `$fixture` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created[]=$fixture;$pdo->exec("USE `$fixture`");$pdo->exec('SET FOREIGN_KEY_CHECKS=0');foreach($schemas as $sql){$pdo->exec($sql);}$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    $password=password_hash('Synthetic fixture secret',PASSWORD_DEFAULT);
    $s=$pdo->prepare("INSERT INTO f_users(user_id,username,password,status) VALUES(1,'fixture-admin',?,'active'),(2,'fixture-other',?,'active')");$s->execute([$password,$password]);
    $pdo->exec("INSERT INTO f_user_profiles(user_id) VALUES(1),(2);
INSERT INTO f_settings(setting_id,variable_name) VALUES(1,'fixture_public_label');
INSERT INTO f_translations(table_name,table_id,field,locale,value,version,created_by) VALUES('f_settings',1,'value','fa','public-copy',1,2);
INSERT INTO p_settings(setting_id,variable_name) VALUES(1,'home_fixture');
INSERT INTO p_translations(table_name,table_id,field,locale,value,version) VALUES('settings',1,'value','fa','public-page',1),('users',1,'full_name','fa','administrator',1),('users',2,'full_name','fa','private-person',1),('user_notifications',2,'content','fa','private-notification',1),('posts',1,'title','fa','public-article',1),('posts',2,'title','fa','other-article',1);
INSERT INTO f_posts(post_id,author_id,views_count,comment_count,created_by) VALUES(1,1,4,2,2),(2,2,8,3,2);
INSERT INTO f_comments(comment_id,post_id,user_id,author,created_by) VALUES(1,1,2,'fixture-comment-author',2),(2,2,2,'fixture-other-author',2);
INSERT INTO p_translations(table_name,table_id,field,locale,value,version) VALUES('comments',2,'content','fa','retained-comment',1);
INSERT INTO p_academies(academy_id,user_id) VALUES(2,2);
INSERT INTO p_academy_branches(branch_id,academy_id,user_id) VALUES(2,2,2);
INSERT INTO p_academy_branch_members(member_id,academy_id,branch_id,user_id) VALUES(2,2,2,2);
INSERT INTO p_academy_branch_course_terms(term_id) VALUES(2);
INSERT INTO p_academy_branch_course_term_sessions(term_session_id,term_id,makeup_for_session_id) VALUES(1,2,NULL),(2,2,1);
UPDATE p_academy_branch_course_term_sessions SET makeup_for_session_id=2 WHERE term_session_id=1;
INSERT INTO f_user_referrals(user_id,referred_by_user_id) VALUES(1,2);
INSERT INTO f_user_point_rules(user_point_rule_id,academy_id) VALUES(1,NULL),(2,2);
INSERT INTO f_user_messages(receiver_user_id,sender_id) VALUES(1,2);
INSERT INTO f_tracking_user_sessions(tracking_user_session_id,user_id) VALUES(1,2);
INSERT INTO f_tracking_ingestion_batches(tracking_user_session_id,batch_uuid) VALUES(1,'fixture-batch');");
    $builder=new InitialDatabase($pdo);$plan=$builder->plan();
    initialCheck($plan['f_users']['delete']===1,'Plan failed to select non-admin');
    initialCheck($plan['p_translations']['keep']===5,'Translation plan failed');
    $pdo->failAfterPostDelete=true;
    try{$builder->apply($directory.'/forced-failure');throw new LogicException('Expected transaction failure');}catch(RuntimeException $error){initialCheck($error->getMessage()==='Synthetic failure during reset','Unexpected reset failure');}
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_users')->fetchColumn()===2 && (int)$pdo->query('SELECT COUNT(*) FROM f_posts')->fetchColumn()===2,'Failed reset did not roll back');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM p_translations')->fetchColumn()===7,'Failed reset changed translations');
    $result=$builder->apply($directory.'/first');$builder->export($directory.'/first');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_users')->fetchColumn()===1,'Non-admin retained');
    initialCheck($pdo->query('SELECT password FROM f_users WHERE user_id=1')->fetchColumn()===$password,'Administrator password changed');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_user_profiles')->fetchColumn()===1,'Non-admin profile retained');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM p_translations')->fetchColumn()===5,'Personal translations retained or post/comment copy lost');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_comments WHERE user_id=2 AND created_by=2')->fetchColumn()===2,'Comments of removed user changed');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_user_referrals')->fetchColumn()===0,'Cross-user referral retained');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_tracking_ingestion_batches')->fetchColumn()===0,'Batch retained');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_user_messages')->fetchColumn()===0,'Cross-user message retained');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM p_academies')->fetchColumn()===0,'Academy retained');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM p_academy_branch_course_term_sessions')->fetchColumn()===0,'Self-reference blocked reset');
    initialCheck((int)$pdo->query('SELECT COUNT(*) FROM f_posts')->fetchColumn()===2,'Post of another user lost');
    initialCheck((int)$pdo->query('SELECT views_count+comment_count FROM f_posts WHERE post_id=1')->fetchColumn()===6,'Post counters changed');
    initialCheck((int)$pdo->query('SELECT created_by FROM f_posts WHERE post_id=1')->fetchColumn()===2,'Historical post actor changed');
    $again=$builder->apply($directory.'/second');initialCheck(array_sum(array_column($again,'delete'))===0,'Repeated reset deletes preserved data');
    $pdo->exec("CREATE DATABASE `$restore` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created[]=$restore;$pdo->exec("USE `$restore`");$pdo->exec(file_get_contents($directory.'/first/initial-database.sql'));
    (new InitialDatabase($pdo))->verify();initialCheck($pdo->query('SELECT password FROM f_users WHERE user_id=1')->fetchColumn()===$password,'Export restoration changed administrator');
    initialCheck((int)$pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_users'")->fetchColumn()===2,'Initial user sequence not reset');
    $pdo->exec('CREATE TABLE f_unclassified_fixture(id INT PRIMARY KEY) ENGINE=InnoDB');
    try{(new InitialDatabase($pdo))->plan();throw new RuntimeException('Unknown table accepted');}catch(LogicException){$checks++;}$pdo->exec('DROP TABLE f_unclassified_fixture');
    $pdo->exec("UPDATE f_users SET deleted_at='2026-10-05 00:00:00' WHERE user_id=1");
    try{(new InitialDatabase($pdo))->plan();throw new RuntimeException('Missing admin accepted');}catch(LogicException){$checks++;}
    echo "Initial database: $checks MySQL checks passed; admin identity, FK order, orphan translations, repeated reset and initial export restoration.\n";
}catch(Throwable $error){fwrite(STDERR,'Isolated initial-version test failed at check '.$checks.' ('.get_class($error).'). '.($error instanceof LogicException?$error->getMessage():($error->errorInfo[1]??$error->getCode()))."\n");if(is_dir(dirname($directory))){file_put_contents($directory.'.local.json',json_encode(['message'=>$error->getMessage(),'line'=>$error->getLine(),'trace'=>$error->getTraceAsString()]));}$failed=true;}
finally{if(isset($pdo)){foreach(array_reverse($created) as $database){$pdo->exec("DROP DATABASE `$database`");}}}
exit(isset($failed)?1:0);
