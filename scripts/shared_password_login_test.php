<?php
// Real repositories and authentication, isolated SQLite database; no application bootstrap.
$map = require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map) { if (isset($map[$class])) require $map[$class]; });
function db() { return $GLOBALS['pdo']; }
function session() { static $s; return $s ??= new Core\session\Session; }
function config($key, $default = null) { return 'isolated-login-fixture'; }
function env($key, $default = null) { return $GLOBALS['fixtureEnv'][$key] ?? $default; }
function check($ok, $message) { if (!$ok) throw new RuntimeException($message); ++$GLOBALS['checks']; }
$checks = 0;
$fixtureEnv = ['APP_ENV' => 'local', 'APP_URL' => 'https://fixture.test'];
$_SERVER['HTTPS'] = 'off';
check(!Core\session\Session::secureCookies(), 'Local HTTP inherited production secure cookie');
$fixtureEnv['APP_ENV'] = 'production';
check(Core\session\Session::secureCookies(), 'Production proxy HTTPS lost secure cookie');
$fixtureEnv['APP_ENV'] = 'local';
$_SERVER['HTTPS'] = 'on';
check(Core\session\Session::secureCookies(), 'Local HTTPS lost secure cookie');
$_SERVER['HTTPS'] = 'off';
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE users(user_id INTEGER PRIMARY KEY, username TEXT, email TEXT, phone TEXT, password TEXT, status TEXT, deleted_at TEXT);
CREATE TABLE auth_remember_tokens(token_hash TEXT PRIMARY KEY, user_id INTEGER, expires_at INTEGER);
CREATE TABLE tracking_user_sessions(session_id TEXT, auth_revoked_at TEXT)');
$hash = password_hash('shared-fixture-password', PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users VALUES(?,?,?,?,?,?,?)');
foreach ([1 => 'admin', 2 => 'academy_1', 3 => 'academy_2'] as $id => $name) {
    $insert->execute([$id, $name, "$name@example.test", "0900000000$id", $hash, 'approved', null]);
}
$dir = __DIR__.'/../storage/test-login-'.bin2hex(random_bytes(6));
mkdir($dir, 0700);
session_save_path($dir);
register_shutdown_function(static function () use ($dir) {
    if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
    foreach (glob($dir.'/sess_*') as $file) unlink($file);
    rmdir($dir);
});
session_start();
$users = new Modules\System\Repositories\UserRepository;
$service = new Modules\System\Services\UserService($users, new Modules\System\Services\UserReferralService);
$auth = new Core\auth\Auth($users);
$mobile = new Modules\System\Services\MobileAuthTokenService($users);
$tokens = [];
foreach ([1 => 'admin', 2 => 'academy_1', 3 => 'academy_2'] as $id => $name) {
    foreach ([$name, "$name@example.test", "0900000000$id"] as $identifier) {
        $user = $service->attempt($identifier, 'shared-fixture-password');
        check($user && (int)$user['user_id'] === $id, 'Login selected another account');
    }
    $auth->login($id);
    check($auth->id() === $id, 'Web session identity mismatch');
    $tokens[$id] = $mobile->issue($user);
}
foreach ($tokens as $id => $token) {
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
    check((int)$mobile->userFromRequest()['user_id'] === $id, 'Shared hash invalidated another bearer');
}
$mobile->revokeFromRequest();
$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer '.$tokens[1];
check((int)$mobile->userFromRequest()['user_id'] === 1, 'Logout revoked another account');
check($service->attempt('academy_1', 'wrong') === false, 'Wrong password accepted');
check($service->attempt('missing', 'shared-fixture-password') === false, 'Unknown identifier accepted');
$pdo->exec("UPDATE users SET status='blocked' WHERE user_id=2");
check($service->attempt('academy_1', 'shared-fixture-password') === false, 'Blocked account accepted');
$pdo->exec("UPDATE users SET deleted_at='2026-01-01' WHERE user_id=3");
check($service->attempt('academy_2', 'shared-fixture-password') === false, 'Deleted account accepted');
echo "Shared-password login: $checks checks passed.\n";
