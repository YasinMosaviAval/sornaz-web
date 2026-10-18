<?php
namespace Modules\Education\Services;

use RuntimeException;

final class IntervalResultPolicy
{
    private static ?array $curriculum = null;
    public static function music(mixed $music): string
    {
        if (!in_array($music, ['western', 'iranian'], true)) throw new RuntimeException('شاخهٔ موسیقی معتبر نیست.', 422);
        return $music;
    }

    public static function batch(array $input): array
    {
        $music = self::music($input['music'] ?? null);
        $version = $input['version'] ?? null;
        $level = $input['level'] ?? null;
        self::$curriculum ??= json_decode(file_get_contents(dirname(__DIR__) . '/Resources/interval-curriculum.v1.json'), true, 512, JSON_THROW_ON_ERROR);
        if ($version !== 1 || !is_int($level)) throw new RuntimeException('نسخه یا سطح تمرین معتبر نیست.', 422);
        $definition = self::$curriculum['branches'][$music][$level] ?? null;
        if (!$definition) throw new RuntimeException('نسخه یا سطح تمرین معتبر نیست.', 422);
        $run = $input['runId'] ?? '';
        if (!is_string($run) || !preg_match('/^[a-f0-9]{32}$/D', $run)) throw new RuntimeException('شناسهٔ تمرین معتبر نیست.', 422);
        $mode = $input['mode'] ?? 'practice';
        if (!in_array($mode, ['practice', 'exam'], true)) throw new RuntimeException('نوع تمرین معتبر نیست.', 422);
        $answers = $input['answers'] ?? null;
        if (!is_array($answers) || !array_is_list($answers) || count($answers) > 100 || !is_bool($input['closed'] ?? false)) throw new RuntimeException('فهرست پاسخ‌ها معتبر نیست.', 422);
        $normalized = array_map(fn ($answer) => self::answer($answer, $definition), $answers);
        foreach ($normalized as $index => $answer) {
            if ($index > 0 && $answer['sequence'] !== $normalized[$index - 1]['sequence'] + 1) throw new RuntimeException('ترتیب پاسخ‌ها معتبر نیست.', 422);
        }
        return ['runId' => $run, 'music' => $music, 'version' => 1, 'level' => $level, 'mode' => $mode,
            'closed' => $input['closed'] ?? false,
            'answers' => $normalized];
    }

    private static function answer(mixed $answer, array $definition): array
    {
        if (!is_array($answer)) throw new RuntimeException('پاسخ معتبر نیست.', 422);
        foreach (['sequence', 'expectedQuarterSteps', 'selectedQuarterSteps', 'rootMidi', 'elapsedMs'] as $key) {
            if (!isset($answer[$key]) || !is_int($answer[$key])) throw new RuntimeException('مقدار پاسخ معتبر نیست.', 422);
        }
        if ($answer['sequence'] < 1 || $answer['sequence'] > 10000000 || $answer['elapsedMs'] < 0 || $answer['elapsedMs'] > 86400000) throw new RuntimeException('ترتیب یا زمان پاسخ معتبر نیست.', 422);
        foreach (['expectedQuarterSteps', 'selectedQuarterSteps'] as $key) {
            if (!in_array($answer[$key], $definition['quarterSteps'], true)) throw new RuntimeException('فاصله خارج از سطح تمرین است.', 422);
        }
        $root = $answer['rootMidi'];
        $direction = $answer['direction'] ?? null;
        $target = $root * 2 + ($direction === 'ascending' ? 1 : -1) * $answer['expectedQuarterSteps'];
        if ($direction !== $definition['direction'] || $root < 21 || $root > 108 || $target < 42 || $target > 216 || (!$definition['variableRoot'] && ($root < 48 || $root > 72))) throw new RuntimeException('نت‌های سوال معتبر نیستند.', 422);
        $at = $answer['answeredAt'] ?? null;
        if (!is_string($at) || strlen($at) > 40 || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?Z$/D', $at)) throw new RuntimeException('تاریخ پاسخ معتبر نیست.', 422);
        if (!checkdate((int) substr($at, 5, 2), (int) substr($at, 8, 2), (int) substr($at, 0, 4)) || (int) substr($at, 11, 2) > 23 || (int) substr($at, 14, 2) > 59 || (int) substr($at, 17, 2) > 59) throw new RuntimeException('تاریخ پاسخ معتبر نیست.', 422);
        return ['sequence' => $answer['sequence'], 'expectedQuarterSteps' => $answer['expectedQuarterSteps'],
            'selectedQuarterSteps' => $answer['selectedQuarterSteps'], 'rootMidi' => $root, 'direction' => $direction,
            'answeredAt' => $at, 'elapsedMs' => $answer['elapsedMs']];
    }
}
