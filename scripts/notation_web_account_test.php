<?php
require __DIR__.'/../Modules/Notation/Controllers/NotationController.php';
require __DIR__.'/../Modules/System/Services/AccessControl.php';
function db() { return new class { public function prepare($sql) { return new class {
    private array $args;
    public function execute($args) { $this->args=$args; }
    public function fetchColumn() { return $this->args === ['notation.access',7,7] && !empty($GLOBALS['granted']); }
}; } }; }
function auth() { return new class { public function id() { return $GLOBALS['actor']; } }; }
$GLOBALS['actor']=7;
$GLOBALS['granted']=true;
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
try { $actorMethod->invoke($controller); throw new Exception('Guest access accepted'); }
catch (RuntimeException $error) { if ($error->getCode()!==403) throw $error; }
unset($_SERVER['HTTP_X_NOTATION_USER']);
$GLOBALS['actor']=7;
$GLOBALS['granted']=false;
try { $actorMethod->invoke($controller); throw new Exception('Revoked permission accepted'); }
catch (RuntimeException $error) { if ($error->getCode()!==403) throw $error; }
$GLOBALS['actor']=1;
if ($actorMethod->invoke($controller)!==1) throw new Exception('Founder access rejected');
echo "Notation web account: authoritative session, optional legacy header, stale tabs and malformed identity rejected.\n";
