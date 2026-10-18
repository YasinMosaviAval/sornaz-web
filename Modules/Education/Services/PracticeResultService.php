<?php
namespace Modules\Education\Services;

use PDO;
use RuntimeException;

/** Self-reported practice history, never proof of a supervised examination. */
final class PracticeResultService
{
    public function __construct(private ?PDO $connection = null) {}
    private function connection(): PDO { return $this->connection ??= db(); }
    private function ready(): void
    {
        try {
            $this->connection()->query('SELECT session_id FROM f_practice_sessions WHERE 1=0');
            $this->connection()->query('SELECT answer_id FROM f_practice_answers WHERE 1=0');
            $this->connection()->query('SELECT level_number FROM f_practice_legacy_progress WHERE 1=0');
        } catch (\PDOException $e) {
            throw new RuntimeException('ثبت نتایج تمرین هنوز روی سرور آماده نیست.', 503);
        }
    }
    public function store(int $actor, array $input): array
    {
        if ($actor <= 0) throw new RuntimeException('ورود به حساب الزامی است.', 401);
        $batch = IntervalResultPolicy::batch($input);
        $this->ready();
        $db = $this->connection();
        $owns = !$db->inTransaction();
        if ($owns) $db->beginTransaction();
        try {
            $session = $this->lockedSession($actor, $batch);
            if (!$batch['answers'] && (int) $session['answer_count'] === 0) throw new RuntimeException('تمرین بدون پاسخ قابل ثبت نیست.', 422);
            foreach ($batch['answers'] as $answer) $session = $this->append($session, $answer);
            if ($batch['closed'] && $session['status'] === 'in_progress') $session['status'] = 'abandoned';
            $query = $db->prepare('UPDATE f_practice_sessions SET answer_count=?,correct_count=?,current_streak=?,best_streak=?,status=?,updated_at=? WHERE session_id=? AND user_id=?');
            $query->execute([$session['answer_count'], $session['correct_count'], $session['current_streak'], $session['best_streak'], $session['status'], gmdate('Y-m-d H:i:s'), $session['session_id'], $actor]);
            if ($owns) $db->commit();
            $last = $batch['answers'] ? $batch['answers'][array_key_last($batch['answers'])]['sequence'] : (int) $session['answer_count'];
            return ['runId' => $batch['runId'], 'acceptedThrough' => $last, 'status' => $session['status']];
        } catch (\Throwable $error) {
            if ($owns && $db->inTransaction()) $db->rollBack();
            throw $error;
        }
    }
    private function lockedSession(int $actor, array $batch): array
    {
        $db = $this->connection();
        $insert = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' ? 'INSERT OR IGNORE' : 'INSERT IGNORE';
        $query = $db->prepare($insert . ' INTO f_practice_sessions(user_id,client_run_id,tool_key,music,curriculum_version,level_number,mode,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?)');
        $now = gmdate('Y-m-d H:i:s');
        $query->execute([$actor, $batch['runId'], 'interval_recognition', $batch['music'], 1, $batch['level'], $batch['mode'], $now, $now]);
        $lock = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $query = $db->prepare('SELECT * FROM f_practice_sessions WHERE user_id=? AND client_run_id=?' . $lock);
        $query->execute([$actor, $batch['runId']]);
        $session = $query->fetch(PDO::FETCH_ASSOC);
        if (!$session || $session['tool_key'] !== 'interval_recognition' || $session['music'] !== $batch['music'] || (int) $session['curriculum_version'] !== 1 || (int) $session['level_number'] !== $batch['level'] || $session['mode'] !== $batch['mode']) throw new RuntimeException('شناسهٔ تمرین با اطلاعات قبلی سازگار نیست.', 409);
        return $session;
    }
    private function append(array $session, array $answer): array
    {
        $hash = hash('sha256', json_encode($answer, JSON_UNESCAPED_SLASHES));
        $count = (int) $session['answer_count'];
        if ($answer['sequence'] <= $count) {
            $query = $this->connection()->prepare('SELECT payload_hash FROM f_practice_answers WHERE session_id=? AND sequence_number=?');
            $query->execute([$session['session_id'], $answer['sequence']]);
            if (!hash_equals((string) $query->fetchColumn(), $hash)) throw new RuntimeException('پاسخ ثبت‌شده قابل بازنویسی نیست.', 409);
            return $session;
        }
        if ($answer['sequence'] !== $count + 1 || $session['status'] !== 'in_progress') throw new RuntimeException('ترتیب پاسخ‌ها معتبر نیست یا تمرین بسته شده است.', 409);
        $correct = $answer['expectedQuarterSteps'] === $answer['selectedQuarterSteps'];
        $query = $this->connection()->prepare('INSERT INTO f_practice_answers(session_id,sequence_number,expected_quarter_steps,selected_quarter_steps,root_midi,direction,is_correct,elapsed_ms,answered_at_client,payload_hash,received_at) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
        $query->execute([$session['session_id'], $answer['sequence'], $answer['expectedQuarterSteps'], $answer['selectedQuarterSteps'], $answer['rootMidi'], $answer['direction'], (int) $correct, $answer['elapsedMs'], $answer['answeredAt'], $hash, gmdate('Y-m-d H:i:s')]);
        $session['answer_count'] = $count + 1;
        $session['correct_count'] = (int) $session['correct_count'] + (int) $correct;
        $session['current_streak'] = $correct ? (int) $session['current_streak'] + 1 : 0;
        $session['best_streak'] = max((int) $session['best_streak'], $session['current_streak']);
        if ($session['current_streak'] === 10) $session['status'] = 'passed';
        return $session;
    }
    public function progress(int $actor, string $music): array
    {
        if ($actor <= 0) throw new RuntimeException('ورود به حساب الزامی است.', 401);
        IntervalResultPolicy::music($music);
        $this->ready();
        $query = $this->connection()->prepare("SELECT client_run_id,level_number FROM f_practice_sessions WHERE user_id=? AND tool_key='interval_recognition' AND music=? AND curriculum_version=1 AND status='passed' ORDER BY session_id");
        $query->execute([$actor, $music]);
        $awards = $counts = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $level = (int) $row['level_number'];
            if (($counts[$level] ?? 0) >= 5) continue;
            $counts[$level] = ($counts[$level] ?? 0) + 1;
            $awards[] = ['runId' => $row['client_run_id'], 'level' => $level];
        }
        $query = $this->connection()->prepare("SELECT client_run_id runId,level_number level,mode,status,answer_count answerCount,correct_count correctCount,best_streak bestStreak,created_at createdAt,updated_at updatedAt FROM f_practice_sessions WHERE user_id=? AND tool_key='interval_recognition' AND music=? ORDER BY session_id DESC LIMIT 20");
        $query->execute([$actor, $music]);
        $recent = $query->fetchAll(PDO::FETCH_ASSOC);
        $query = $this->connection()->prepare("SELECT level_number FROM f_practice_legacy_progress WHERE user_id=? AND tool_key='interval_recognition' AND music=? AND curriculum_version=1 ORDER BY level_number");
        $query->execute([$actor, $music]);
        $legacy = array_map(fn ($level) => ['level' => (int) $level, 'stars' => 1], $query->fetchAll(PDO::FETCH_COLUMN));
        return ['music' => $music, 'version' => 1, 'awards' => $awards, 'legacyStars' => $legacy, 'recent' => $recent];
    }
    public function importLegacy(int $actor, array $input): array
    {
        if ($actor <= 0) throw new RuntimeException('ورود به حساب الزامی است.', 401);
        $music = IntervalResultPolicy::music($input['music'] ?? null);
        $levels = $input['levels'] ?? null;
        $limit = $music === 'western' ? 44 : 180;
        if (($input['version'] ?? null) !== 1 || !is_array($levels) || !array_is_list($levels) || count($levels) > $limit) throw new RuntimeException('پیشرفت قدیمی معتبر نیست.', 422);
        foreach ($levels as $level) {
            if (!is_int($level) || $level < 1 || $level > $limit) throw new RuntimeException('سطح معتبر نیست.', 422);
        }
        $this->ready();
        $db = $this->connection();
        $owns = !$db->inTransaction();
        if ($owns) $db->beginTransaction();
        try {
            $insert = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' ? 'INSERT OR IGNORE' : 'INSERT IGNORE';
            $query = $db->prepare($insert . ' INTO f_practice_legacy_progress(user_id,tool_key,music,curriculum_version,level_number,source,received_at) VALUES(?,?,?,?,?,?,?)');
            foreach (array_unique($levels) as $level) $query->execute([$actor, 'interval_recognition', $music, 1, $level, 'app_local_completion_v2', gmdate('Y-m-d H:i:s')]);
            if ($owns) $db->commit();
            return ['imported' => true];
        } catch (\Throwable $error) {
            if ($owns && $db->inTransaction()) $db->rollBack();
            throw $error;
        }
    }
    public function details(int $actor, string $runId, int $after = 0): array
    {
        if ($actor <= 0) throw new RuntimeException('ورود به حساب الزامی است.', 401);
        if (!preg_match('/^[a-f0-9]{32}$/D', $runId) || $after < 0) throw new RuntimeException('درخواست معتبر نیست.', 422);
        $this->ready();
        $query = $this->connection()->prepare("SELECT * FROM f_practice_sessions WHERE user_id=? AND client_run_id=? AND tool_key='interval_recognition'");
        $query->execute([$actor, $runId]);
        $session = $query->fetch(PDO::FETCH_ASSOC);
        if (!$session) throw new RuntimeException('نتیجه یافت نشد.', 404);
        $query = $this->connection()->prepare('SELECT sequence_number AS `sequence`,expected_quarter_steps expectedQuarterSteps,selected_quarter_steps selectedQuarterSteps,root_midi rootMidi,direction,is_correct correct,elapsed_ms elapsedMs,answered_at_client answeredAt FROM f_practice_answers WHERE session_id=? AND sequence_number>? ORDER BY sequence_number LIMIT 100');
        $query->execute([$session['session_id'], $after]);
        return ['runId' => $runId, 'music' => $session['music'], 'level' => (int) $session['level_number'], 'status' => $session['status'], 'answerCount' => (int) $session['answer_count'], 'answers' => $query->fetchAll(PDO::FETCH_ASSOC)];
    }
}
