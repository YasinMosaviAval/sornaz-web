<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/lib/DatabaseHardeningMigration.php';
try {
    $dsn=getenv('SORNAZ_MIGRATION_DSN');
    if (!$dsn) { throw new LogicException('Set SORNAZ_MIGRATION_DSN/USER/PASSWORD explicitly.'); }
    $applying=in_array('--apply',$argv,true);
    if ($applying&&!in_array('--maintenance',$argv,true)) { throw new LogicException('Stop writes first; then use --apply --maintenance.'); }
    $pdo=new PDO($dsn,getenv('SORNAZ_MIGRATION_USER')?:'',getenv('SORNAZ_MIGRATION_PASSWORD')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    if (!$pdo->query("SELECT GET_LOCK('sornaz_database_hardening',0)")->fetchColumn()) { throw new LogicException('Another migration is running.'); }
    $migration=new DatabaseHardeningMigration($pdo);
    $directory=__DIR__.'/../storage/backups/database-hardening/'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4));
    $result=$applying?$migration->apply($directory):$migration->inspect();
    echo json_encode($result,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),"\n";
    if($applying){echo 'Private backup and translation report: storage/backups/database-hardening/',basename($directory),"\n";}
    $pdo->query("SELECT RELEASE_LOCK('sornaz_database_hardening')");
    exit($result['errors']?1:0);
}catch(Throwable $error){
    fwrite(STDERR,'Migration stopped ('.get_class($error).'). '.($error instanceof LogicException?$error->getMessage():'Review --check and deployment guide; no automatic data cleanup.')."\n");exit(1);
}
