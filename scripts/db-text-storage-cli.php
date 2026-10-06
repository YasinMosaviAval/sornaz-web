<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/lib/TextStorageMigration.php';
try{
    $dsn=getenv('SORNAZ_TEXT_DSN');if(!$dsn){throw new LogicException('Set explicit SORNAZ_TEXT_DSN/USER/PASSWORD');}
    $p=new PDO($dsn,getenv('SORNAZ_TEXT_USER')?:'',getenv('SORNAZ_TEXT_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    if(!$p->query("SELECT GET_LOCK('sornaz_text_storage',0)")->fetchColumn()){throw new LogicException('Migration already running');}
    $m=new TextStorageMigration($p);
    if(!in_array('--apply',$argv,true)){echo json_encode($m->plan(),JSON_PRETTY_PRINT),"\n";exit;}
    if(!in_array('--maintenance',$argv,true)){throw new LogicException('Stop all writers; --maintenance required');}
    $directory=__DIR__.'/../storage/backups/text-storage/'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4));
    $result=$m->apply($directory);echo json_encode(['fields'=>count($result['fields']),'translations_inserted'=>$result['translations_inserted'],'remaining_text_columns'=>$result['remaining_text_columns'],'private_backup'=>'storage/backups/text-storage/'.basename($directory)],JSON_PRETTY_PRINT),"\n";
}catch(Throwable $error){fwrite(STDERR,'Text migration stopped ('.get_class($error).'). '.($error instanceof LogicException?$error->getMessage():'Private backup retained; inspect locally before resuming.')."\n");exit(1);}
