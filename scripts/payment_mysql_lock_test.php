<?php
// Optional CLI integration check. Uses two connections and temporary advisory
// locks only; it does not read or modify any application tables.
if (PHP_SAPI!=='cli') {http_response_code(404);exit;}
$dsn=getenv('SORNAZ_TEST_MYSQL_DSN');
if (!$dsn) {fwrite(STDERR,"Set SORNAZ_TEST_MYSQL_DSN, SORNAZ_TEST_MYSQL_USER and SORNAZ_TEST_MYSQL_PASSWORD for an isolated MySQL test server.\n");exit(2);}
require __DIR__.'/../Modules/System/Services/PaymentMutex.php';
try {
    $options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION];
    $first=new PDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',$options);
    $second=new PDO($dsn,getenv('SORNAZ_TEST_MYSQL_USER')?:'',getenv('SORNAZ_TEST_MYSQL_PASSWORD')?:'',$options);
    if($first->getAttribute(PDO::ATTR_DRIVER_NAME)!=='mysql')throw new RuntimeException('A MySQL test connection is required');
    $resource='fixture:'.bin2hex(random_bytes(16));
    Modules\System\Services\PaymentMutex::run($first,$resource,function()use($second,$resource){
        try {Modules\System\Services\PaymentMutex::run($second,$resource,fn()=>true);}
        catch(RuntimeException $error){if($error->getCode()===409)return;throw $error;}
        throw new RuntimeException('Concurrent connection acquired an occupied payment lock');
    });
    Modules\System\Services\PaymentMutex::run($second,$resource,fn()=>true);
    echo "MySQL payment mutex: exclusion and release passed.\n";
} catch(Throwable $error) {
    // Avoid printing connection strings or credentials from driver exceptions.
    fwrite(STDERR,"MySQL payment mutex check failed (".get_class($error)."). Inspect the isolated test configuration.\n");exit(1);
}
