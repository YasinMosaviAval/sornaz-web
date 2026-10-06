<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/lib/InitialDatabase.php';
try{
    $dsn=getenv('SORNAZ_INITIAL_DSN');if(!$dsn){throw new LogicException('Set SORNAZ_INITIAL_DSN/USER/PASSWORD explicitly.');}
    $apply=in_array('--apply',$argv,true);
    if($apply&&!in_array('--maintenance',$argv,true)){throw new LogicException('Stop all writers first; use --apply --maintenance.');}
    $pdo=new PDO($dsn,getenv('SORNAZ_INITIAL_USER')?:'',getenv('SORNAZ_INITIAL_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    if(!$pdo->query("SELECT GET_LOCK('sornaz_initial_version',0)")->fetchColumn()){throw new LogicException('Another initial-version job is running');}
    $builder=new InitialDatabase($pdo);$plan=$builder->plan();
    if(!$apply){echo json_encode($plan,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),"\n";exit;}
    $directory=__DIR__.'/../storage/backups/initial-version/'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4));
    $result=$builder->apply($directory);$builder->export($directory);
    echo json_encode(['tables'=>count($result),'removed_rows'=>array_sum(array_column($result,'delete')),'administrator_preserved'=>true,'private_artifacts'=>'storage/backups/initial-version/'.basename($directory)],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),"\n";
    $pdo->query("SELECT RELEASE_LOCK('sornaz_initial_version')");
}catch(Throwable $error){fwrite(STDERR,'Initial-version build stopped ('.get_class($error).'). '.($error instanceof LogicException?$error->getMessage():'Review the plan; full backup is retained; no automatic retry.')."\n");exit(1);}
