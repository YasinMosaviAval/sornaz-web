<?php
namespace Modules\System\Repositories { final class UserRepository {} }
namespace Modules\System\Services {
    final class MobileAuthTokenService {
        public function __construct($users) {}
        public function userFromRequest(): ?array {
            return match ($_SERVER['HTTP_AUTHORIZATION'] ?? '') {
                'Bearer fixture-10' => ['user_id' => 10],
                'Bearer fixture-20' => ['user_id' => 20],
                default => null,
            };
        }
    }
}
namespace {
    $root = dirname(__DIR__);
    $map = require $root . '/vendor/composer/autoload_classmap.php';
    spl_autoload_register(static function (string $class) use ($root, $map): void {
        $file = $map[$class] ?? $root . '/' . str_replace('\\', '/', $class) . '.php';
        if (is_file($file)) require_once $file;
    });
    $pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $dbReads = 0;
    function db(): PDO { $GLOBALS['dbReads']++; return $GLOBALS['pdo']; }
    $checks = 0;
    function verify(bool $condition, string $message): void { $GLOBALS['checks']++; if (!$condition) throw new RuntimeException($message); }
    function denied(callable $action, int $status): void {
        try { $action(); } catch (Throwable $error) { verify($error->getCode() === $status, 'Wrong rejection: ' . $error->getMessage()); return; }
        throw new RuntimeException('Expected rejected request');
    }
    function responseStatus(object $response): int { return (new ReflectionProperty($response, 'status'))->getValue($response); }
    function answer(int $sequence, bool $correct = true): array {
        return ['sequence' => $sequence, 'expectedQuarterSteps' => 14, 'selectedQuarterSteps' => $correct ? 14 : 24,
            'rootMidi' => 60, 'direction' => 'ascending', 'answeredAt' => '2026-10-17T12:00:00.000Z', 'elapsedMs' => 2000];
    }
    function batch(string $id, array $answers, bool $closed = false): array {
        return ['runId' => str_pad($id, 32, '0', STR_PAD_LEFT), 'music' => 'western', 'version' => 1, 'level' => 1, 'mode' => 'practice', 'answers' => $answers, 'closed' => $closed];
    }
    $service = new \Modules\Education\Services\PracticeResultService($pdo);
    denied(fn () => $service->progress(10, 'western'), 503);
    verify((int) $pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table'")->fetchColumn() === 0, 'Ordinary API reads must never create tables');
    $pdo->exec("CREATE TABLE f_practice_sessions (
      session_id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, client_run_id TEXT NOT NULL, tool_key TEXT NOT NULL,
      music TEXT NOT NULL,curriculum_version INTEGER NOT NULL,level_number INTEGER NOT NULL,mode TEXT NOT NULL,status TEXT DEFAULT 'in_progress',
      answer_count INTEGER DEFAULT 0,correct_count INTEGER DEFAULT 0,current_streak INTEGER DEFAULT 0,best_streak INTEGER DEFAULT 0,
      created_at TEXT,updated_at TEXT,UNIQUE(user_id,client_run_id));
      CREATE TABLE f_practice_answers (answer_id INTEGER PRIMARY KEY AUTOINCREMENT,session_id INTEGER,sequence_number INTEGER,
      expected_quarter_steps INTEGER,selected_quarter_steps INTEGER,root_midi INTEGER,direction TEXT,is_correct INTEGER,elapsed_ms INTEGER,
      answered_at_client TEXT,payload_hash TEXT,received_at TEXT,UNIQUE(session_id,sequence_number));
      CREATE TABLE f_practice_legacy_progress(user_id INTEGER,tool_key TEXT,music TEXT,curriculum_version INTEGER,level_number INTEGER,source TEXT,received_at TEXT,PRIMARY KEY(user_id,tool_key,music,curriculum_version,level_number));");
    $first = batch('a', array_map(fn ($i) => answer($i), range(1, 9)));
    verify($service->store(10, $first)['status'] === 'in_progress', 'Nine answers cannot award a star');
    verify($service->progress(10, 'western')['awards'] === [], 'Incomplete streak awarded a star');
    $service->store(10, batch('a', [answer(10, false)]));
    $last = batch('a', array_map(fn ($i) => answer($i), range(11, 20)), true);
    verify($service->store(10, $last)['status'] === 'passed', 'Ten consecutive correct answers must pass');
    $service->store(10, $last);
    verify((int) $pdo->query('SELECT COUNT(*) FROM f_practice_answers')->fetchColumn() === 20, 'Lost-response retries duplicated answers');
    verify(count($service->progress(10, 'western')['awards']) === 1, 'Retry duplicated a star');
    verify($service->progress(20, 'western')['awards'] === [], 'Another user can see awarded stars');
    denied(fn () => $service->details(20, $first['runId']), 404);
    denied(fn () => $service->details(1, $first['runId']), 404);
    denied(fn () => $service->store(10, batch('a', [answer(1, false)])), 409);
    denied(fn () => $service->store(10, batch('a', [answer(21)])), 409);
    $conflict = $first; $conflict['level'] = 3;
    denied(fn () => $service->store(10, $conflict), 409);
    $before = (int) $pdo->query('SELECT COUNT(*) FROM f_practice_sessions')->fetchColumn();
    denied(fn () => $service->store(10, batch('b', [answer(2)])), 409);
    verify((int) $pdo->query('SELECT COUNT(*) FROM f_practice_sessions')->fetchColumn() === $before, 'Invalid sequence left a session behind');
    foreach (['music' => 'other', 'version' => 2, 'level' => [], 'runId' => '../x', 'mode' => 'unknown', 'closed' => 1] as $key => $value) {
        $invalid = $first; $invalid[$key] = $value;
        denied(fn () => $service->store(10, $invalid), 422);
    }
    foreach (['expectedQuarterSteps' => 1, 'selectedQuarterSteps' => 13, 'rootMidi' => 5, 'direction' => 'descending', 'elapsedMs' => -1, 'answeredAt' => 'bad'] as $key => $value) {
        $invalid = batch('c', [array_replace(answer(1), [$key => $value])]);
        denied(fn () => $service->store(10, $invalid), 422);
    }
    denied(fn () => $service->store(10, batch('c', [answer(1), answer(1)])), 422);
    denied(fn () => $service->store(10, batch('c', [])), 422);
    denied(fn () => $service->store(0, $first), 401);
    for ($i = 1; $i <= 6; $i++) $service->store(10, batch(dechex(100 + $i), array_map(fn ($n) => answer($n), range(1, 10)), true));
    verify(count($service->progress(10, 'western')['awards']) === 5, 'Stars must cap at five per level');
    verify((int) $pdo->query("SELECT COUNT(*) FROM f_practice_sessions WHERE status='passed'")->fetchColumn() === 7, 'Five-star cap discarded additional test results');
    $long = batch('d', array_map(fn ($n) => answer($n, false), range(1, 100)));
    $service->store(10, $long);
    $service->store(10, batch('d', array_map(fn ($n) => answer($n, false), range(101, 110)), true));
    $detail = $service->details(10, $long['runId'], 100);
    verify($detail['status'] === 'abandoned' && count($detail['answers']) === 10 && $detail['answerCount'] === 110, 'Partial/failed practice or answer pagination was lost');
    $curriculum = json_decode(file_get_contents($root . '/Modules/Education/Resources/interval-curriculum.v1.json'), true);
    verify(count($curriculum['branches']['western']) === 44 && count($curriculum['branches']['iranian']) === 180, 'Server curriculum differs from the app');
    foreach ($curriculum['branches'] as $music => $levels) foreach ($levels as $number => $definition) {
        $sample = batch('ee', [array_replace(answer(1), ['direction' => $definition['direction'], 'expectedQuarterSteps' => $definition['quarterSteps'][0], 'selectedQuarterSteps' => $definition['quarterSteps'][0]])]);
        $sample['music'] = $music; $sample['level'] = (int) $number;
        verify(\Modules\Education\Services\IntervalResultPolicy::batch($sample)['level'] === (int) $number, 'A valid curriculum level was rejected');
    }
    $answerCount = (int) $pdo->query('SELECT COUNT(*) FROM f_practice_answers')->fetchColumn();
    $legacy = ['music' => 'western', 'version' => 1, 'levels' => [1, 3]];
    $service->importLegacy(10, $legacy); $service->importLegacy(10, $legacy);
    verify(count($service->progress(10, 'western')['legacyStars']) === 2, 'Legacy imports must merge without duplicate stars');
    verify($service->progress(20, 'western')['legacyStars'] === [], 'Legacy progress leaked across accounts');
    verify((int) $pdo->query('SELECT COUNT(*) FROM f_practice_answers')->fetchColumn() === $answerCount, 'Legacy import fabricated answer history');
    verify($pdo->query('SELECT source FROM f_practice_legacy_progress LIMIT 1')->fetchColumn() === 'app_local_completion_v2', 'Legacy data lost its provenance');
    denied(fn () => $service->importLegacy(10, ['music' => 'western', 'version' => 1, 'levels' => [45]]), 422);
    $iranian = batch('e', [array_replace(answer(1), ['expectedQuarterSteps' => 1, 'selectedQuarterSteps' => 1])]);
    $iranian['music'] = 'iranian';
    $service->store(10, $iranian);
    verify($service->details(10, $iranian['runId'])['answers'][0]['expectedQuarterSteps'] === 1, 'Quarter-tone data was rounded or lost');
    $pdo->exec("CREATE TRIGGER fixture_practice_failure BEFORE INSERT ON f_practice_answers WHEN NEW.sequence_number=2 BEGIN SELECT RAISE(ABORT,'fixture failure'); END");
    try { $service->store(10, batch('f', [answer(1), answer(2)])); } catch (PDOException $error) {}
    verify((int) $pdo->query("SELECT COUNT(*) FROM f_practice_sessions WHERE client_run_id='" . str_pad('f', 32, '0', STR_PAD_LEFT) . "'")->fetchColumn() === 0, 'Failed answer batch was not rolled back atomically');
    $pdo->exec('DROP TRIGGER fixture_practice_failure');
    $controller = new \Modules\Education\Controllers\Api\PracticeController();
    $_SESSION['auth_user_id'] = 10; $_SERVER['HTTP_AUTHORIZATION'] = ''; $_GET = [];
    $dbReads = 0;
    verify(responseStatus($controller->index()) === 401 && $dbReads === 0, 'Cookie-only request reached database');
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer fixture-20'; $_GET = ['runId' => $first['runId']];
    verify(responseStatus($controller->index()) === 404, 'API disclosed another user result');
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer fixture-10'; $_POST = ['payload' => json_encode(batch('ab', [answer(1)]))];
    verify(responseStatus($controller->store()) === 200, 'Authenticated API upload failed');
    $_POST = ['payload' => json_encode($legacy)];
    verify(responseStatus($controller->importLegacy()) === 200, 'Authenticated legacy import failed');
    $_POST = ['payload' => str_repeat('x', 262145)];
    verify(responseStatus($controller->store()) === 422, 'Oversized request was accepted');
    require $root . '/Modules/Education/Routes/api.php';
    verify(\Core\router\Router::dispatch('POST', '/api/sornaz/v1/practice/interval-results') !== null, 'Practice API route is not registered');
    $pdo->exec(file_get_contents($root . '/docs/database/migrations/2026_10_17_practice_results_rollback.sql'));
    verify((int) $pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name LIKE 'f_practice_%'")->fetchColumn() === 0, 'Rollback left practice tables behind');
    echo "Practice results: $checks streak, deduplication, scope, schema, quarter-tone, transaction and API checks passed (SQLite fixture).\n";
}
