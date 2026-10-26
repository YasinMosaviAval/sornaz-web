<?php
require __DIR__.'/../Modules/Notation/Controllers/NotationController.php';
function auth() { return new class { public function id() { return $GLOBALS['actor']; } }; }
$GLOBALS['actor']=7;
$_SERVER['REQUEST_URI']='/music-sheets/api';
$controller=new Modules\Notation\Controllers\NotationController();
$actorMethod=new ReflectionMethod($controller,'actor');
foreach ([null,'7'] as $header) {
    unset($_SERVER['HTTP_X_NOTATION_USER']);
    if ($header!==null) $_SERVER['HTTP_X_NOTATION_USER']=$header;
    if ($actorMethod->invoke($controller)!==7) throw new Exception('Server identity changed');
}
foreach (['8','07','', ['7']] as $header) {
    $_SERVER['HTTP_X_NOTATION_USER']=$header;
    try { $actorMethod->invoke($controller); throw new Exception('Stale or forged account accepted'); }
    catch (RuntimeException $error) { if ($error->getCode()!==403) throw $error; }
}
$GLOBALS['actor']=0;
$_SERVER['HTTP_X_NOTATION_USER']='7';
try { $actorMethod->invoke($controller); throw new Exception('Signed-out tab accepted'); }
catch (RuntimeException $error) { if ($error->getCode()!==403) throw $error; }
$_SERVER['HTTP_X_NOTATION_USER']='0';
if ($actorMethod->invoke($controller)!==0) throw new Exception('Guest reads broken');
echo "Notation web account: authoritative session, optional legacy header, stale tabs and malformed identity rejected.\n";
