<?php
namespace Modules\System\Services {
    class SiteAdminAccess { public static function allows($user): bool { return ($user['user_id'] ?? 0) === 1; } }
}
namespace Core\database {
    class DB { public static function table($table): object { return new class {
        public function __call($name,$args) { return $name === 'first' ? null : $this; }
    }; } }
}
namespace {
require __DIR__.'/../Modules/System/Services/AccessControl.php';
function db() { return new class { public function prepare($sql) { return new class {
    public function execute($args) {}
    public function fetchColumn() { return $GLOBALS['grant']; }
}; } }; }
function auth() { return new class {
    public function id() { return $GLOBALS['id']; }
    public function user() { return $GLOBALS['id'] ? ['user_id'=>$GLOBALS['id']] : null; }
    public function check() { return (bool)$GLOBALS['id']; }
}; }
function app() { return new class {
    public function container() { return $this; }
    public function make($class) { return $this; }
    public function token() { return 'fixture'; }
}; }
function e($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); }
function locale() { return 'en'; }
function direction() { return 'ltr'; }
function trans($key,$fallback='') { return $fallback; }
function component($name) {}
foreach ([['Page',2],['Analytics',2]] as [$module,$links]) {
 foreach ([[0,false,false],[1,false,true],[8,false,false],[8,true,true],[8,false,false]] as [$id,$grant,$expected]) {
    $GLOBALS['id']=$id;$GLOBALS['grant']=$grant;
    ob_start();include __DIR__.'/../Modules/'.$module.'/Resources/Views/sections/main-header.php';$html=ob_get_clean();
    if (substr_count($html,'href="/music-sheets"') !== ($expected?$links:0)) throw new \RuntimeException("$module header permission failure for $id");
 }
}
echo "Notation header: guest, founder, ordinary, grant and revocation passed for desktop/mobile headers.\n";
}
