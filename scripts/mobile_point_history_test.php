<?php
require __DIR__ . '/../Modules/Analytics/Services/UserPointService.php';
require __DIR__ . '/../core/database/ActivityText.php';
function locale(): string { return 'fa'; }
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE user_points(user_point_id INTEGER PRIMARY KEY,user_id INTEGER,type TEXT,points INTEGER,action TEXT,rule_id INTEGER,reference_type TEXT,reference_id INTEGER,created_at TEXT,updated_at TEXT,deleted_at TEXT)');
$insert = $pdo->prepare("INSERT INTO user_points VALUES(?,?,'general',5,'create:lessons',7,'lesson',2,'2026-10-01','2026-10-02',?)");
for ($i = 1; $i <= 20; $i++) $insert->execute([$i, 10, null]);
$insert->execute([21, 11, null]);
$insert->execute([22, 10, '2026-10-03']);
$method = new ReflectionMethod(\Modules\Analytics\Services\UserPointService::class, 'pointHistory');
$service = new \Modules\Analytics\Services\UserPointService();
$full = $method->invoke($service, $pdo, 10, true);
$recent = $method->invoke($service, $pdo, 10, false);
if (count($full) !== 20 || count($recent) !== 15 || $full[0]['user_point_id'] !== 20) throw new RuntimeException('History pagination, ownership or deleted-record scope failed');
if ($full[0]['rule_id'] !== 7 || $full[0]['updated_at'] !== '2026-10-02') throw new RuntimeException('Website history columns are missing');
if (str_contains($full[0]['action'], 'lessons')) throw new RuntimeException('Technical activity identifier exposed');
echo "Point history: complete own history, recent compatibility, deleted rows and human labels passed.\n";
