<?php
// Isolated SQLite fixture with fake email transport; no real message is sent.
$map = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function (string $class) use ($map): void { if (isset($map[$class])) require_once $map[$class]; });
require_once __DIR__ . '/../Modules/Analytics/Services/MobileContactChangeService.php';
$GLOBALS['pdo'] = new \Core\database\PrefixedPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function db() { return $GLOBALS['pdo']; }
function session() { static $session; return $session ??= new class { private array $values = []; public function get(string $key, mixed $default = null): mixed { return $key === 'suppress_database_notifications' ? true : ($this->values[$key] ?? $default); } public function put(string $key, mixed $value): void { $this->values[$key] = $value; } public function forget(string $key): void { unset($this->values[$key]); } }; }
function transaction(callable $work) { db()->beginTransaction(); try { $result = $work(); db()->commit(); return $result; } catch (Throwable $error) { db()->rollBack(); throw $error; } }
$pdo = db();
$pdo->exec("CREATE TABLE f_mobile_contact_challenges(user_id INTEGER,field TEXT,destination TEXT,code_hash TEXT,sent_at TEXT,expires_at TEXT,attempts INTEGER,verified_at TEXT,consumed_at TEXT,PRIMARY KEY(user_id,field));
CREATE TABLE users(user_id INTEGER PRIMARY KEY,email TEXT,phone TEXT,type TEXT,updated_by INTEGER,deleted_at TEXT);
CREATE TABLE academies(academy_id INTEGER PRIMARY KEY,user_id INTEGER,created_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branches(branch_id INTEGER PRIMARY KEY,user_id INTEGER,academy_id INTEGER,deleted_at TEXT);
INSERT INTO users VALUES(2,'old@example.com',NULL,'human',NULL,NULL),(3,'other@example.com',NULL,'human',NULL,NULL);");
$mail = new class extends \Modules\System\Services\MailService { public string $code = ''; public function sendRegistrationOtp(string $email, string $code, int $validMinutes): bool { $this->code = $code; return true; } };
$sms = new class extends \Modules\System\Services\SmsService {};
$service = new \Modules\Analytics\Services\MobileContactChangeService($mail, $sms, new \Modules\Analytics\Services\AdminAccountService());
$service->send(2, 'email', 'new@example.com');
if (!$mail->code || $pdo->query("SELECT code_hash FROM f_mobile_contact_challenges WHERE user_id=2")->fetchColumn() === $mail->code) throw new LogicException('OTP was missing or stored in plain text.');
try { $service->verify(3, 'email', 'new@example.com', $mail->code); throw new LogicException('Another user verified the challenge.'); } catch (RuntimeException $error) { if ($error->getMessage() === 'Another user verified the challenge.') throw $error; }
try { $service->verify(2, 'email', 'new@example.com', '000000'); throw new LogicException('Wrong OTP was accepted.'); } catch (RuntimeException $error) { if ($error->getMessage() === 'Wrong OTP was accepted.') throw $error; }
if ((int) $pdo->query("SELECT attempts FROM f_mobile_contact_challenges WHERE user_id=2")->fetchColumn() !== 1) throw new LogicException('Wrong OTP attempt was not counted.');
$service->verify(2, 'email', 'new@example.com', $mail->code);
try { $service->verify(2, 'email', 'new@example.com', '000000'); throw new LogicException('Wrong OTP kept verification active.'); } catch (RuntimeException $error) { if ($error->getMessage() === 'Wrong OTP kept verification active.') throw $error; }
try { $service->commit(2, 'email', 'new@example.com'); throw new LogicException('Invalidated verification was accepted.'); } catch (RuntimeException $error) { if ($error->getMessage() === 'Invalidated verification was accepted.') throw $error; }
$service->verify(2, 'email', 'new@example.com', $mail->code);
$service->commit(2, 'email', 'new@example.com');
if ($pdo->query('SELECT email FROM users WHERE user_id=2')->fetchColumn() !== 'new@example.com') throw new LogicException('Verified contact did not update the owner.');
try { $service->commit(2, 'email', 'new@example.com'); throw new LogicException('Challenge was reused.'); } catch (RuntimeException $error) { if ($error->getMessage() === 'Challenge was reused.') throw $error; }
if ($pdo->query('SELECT email FROM users WHERE user_id=3')->fetchColumn() !== 'other@example.com') throw new LogicException('Other account changed.');
echo "Mobile contact change: hashed code, ownership, attempt count and one-time commit passed.\n";
