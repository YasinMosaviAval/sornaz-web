<?php
namespace Modules\Education\Services;

use RuntimeException;

final class MusicPracticeDefinition
{
    public static function iconKey(mixed $value): ?string
    {
        if($value===null||$value==='')return null;
        if(!is_string($value)||!in_array($value,['music_note','library_music','piano','queue_music','headphones','mic','hearing','graphic_eq','speed','album','school','star','tune','folder'],true))throw new RuntimeException('آیکن انتخاب‌شده معتبر نیست.',422);
        return $value;
    }

    public static function integer(mixed $value, int $min, int $max): int
    {
        if (!is_int($value) || $value < $min || $value > $max) throw new RuntimeException('مقدار عددی معتبر نیست.', 422);
        return $value;
    }

    public static function text(mixed $value, int $max = 200): string
    {
        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8') || trim($value) === '' || mb_strlen($value) > $max || preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f]/', $value)) throw new RuntimeException('متن معتبر نیست.', 422);
        return trim($value);
    }

    public static function config(mixed $input, ?\Modules\Notation\Services\NotationService $notation = null): array
    {
        if(!is_array($input))throw new RuntimeException('تنظیمات آزمون معتبر نیست.',422);
        $kind = $input['kind'] ?? '';
        if($kind==='notation')return self::notationConfig($input,$notation);
        if ($kind === 'interval') {
            $steps = $input['quarterSteps'] ?? [];
            if (!is_array($steps) || !array_is_list($steps) || !$steps || count($steps) > 24) throw new RuntimeException('فاصله‌های تمرین معتبر نیستند.', 422);
            foreach ($steps as $step) self::integer($step, 1, 24);
            if (!in_array($input['direction'] ?? '', ['ascending', 'descending'], true) || !is_bool($input['variableRoot'] ?? null)) throw new RuntimeException('تنظیمات شنیداری معتبر نیست.', 422);
            return ['kind' => $kind, 'quarterSteps' => array_values(array_unique($steps)), 'direction' => $input['direction'], 'variableRoot' => $input['variableRoot']];
        }
        if (!in_array($kind, ['choice', 'rhythm'], true)) throw new RuntimeException('نوع آزمون معتبر نیست.', 422);
        $questions = $input['questions'] ?? [];
        if (!is_array($questions) || !array_is_list($questions) || !$questions || count($questions) > 200) throw new RuntimeException('پرسش‌ها معتبر نیستند.', 422);
        $clean = [];
        foreach ($questions as $question) {
            $clean[] = self::question($kind,$question);
        }
        return ['kind' => $kind, 'questions' => $clean];
    }
    private static function notationConfig(array $input,?\Modules\Notation\Services\NotationService $notation): array
    {
        $assessment=$input['assessment']??'pitch_rhythm';$questions=$input['questions']??null;
        if(!in_array($assessment,['pitch_rhythm','rhythm'],true)||!is_array($questions)||!array_is_list($questions)||!$questions||count($questions)>20||strlen(json_encode($input,JSON_THROW_ON_ERROR))>220000)throw new RuntimeException('تعریف سطح نوشتاری معتبر نیست.',422);
        $notation??=new \Modules\Notation\Services\NotationService(new \Modules\CourseMarket\Repositories\CourseRepository(db()));
        return ['kind'=>'notation','gradingVersion'=>1,'assessment'=>$assessment,'questions'=>array_map(fn($q)=>MusicPracticeNotation::question($q,$notation),$questions)];
    }
    private static function question(string $kind,mixed $question): array
    {
            if (!is_array($question)) throw new RuntimeException('پرسش معتبر نیست.', 422);
            $choices = $question['choices'] ?? [];
            if (!is_array($choices) || !array_is_list($choices) || count($choices) < 2 || count($choices) > 12) throw new RuntimeException('گزینه‌ها معتبر نیستند.', 422);
            $row = ['promptFa' => self::text($question['promptFa'] ?? '', 2000), 'promptEn' => self::text($question['promptEn'] ?? '', 2000), 'choices' => array_map(fn ($text) => self::text($text), $choices), 'correct' => self::integer($question['correct'] ?? null, 0, count($choices) - 1)];
            if ($kind === 'rhythm') {
                $beats = $question['beats'] ?? [];
                if (!is_array($beats) || !array_is_list($beats) || !$beats || count($beats) > 64) throw new RuntimeException('الگوی ریتم معتبر نیست.', 422);
                foreach ($beats as $beat) if (!is_numeric($beat) || $beat < .125 || $beat > 8) throw new RuntimeException('کشش ریتم معتبر نیست.', 422);
                $row += ['beats' => array_map('floatval', $beats), 'bpm' => self::integer($question['bpm'] ?? 80, 30, 240)];
            }
            return $row;
    }

    public static function answer(array $answer, array $config): array
    {
        $sequence = self::integer($answer['sequence'] ?? null, 1, 1000000);
        $elapsed = self::integer($answer['elapsedMs'] ?? null, 0, 86400000);
        $at = self::timestamp($answer['answeredAt']??null);
        $row = ['sequence' => $sequence, 'elapsedMs' => $elapsed, 'answeredAt' => $at];
        if ($config['kind'] === 'interval') {
            $expected = self::integer($answer['expected'] ?? null, 1, 24);
            $selected = self::integer($answer['selected'] ?? null, 1, 24);
            $root = self::integer($answer['rootMidi'] ?? null, 21, 108);
            $target = $root * 2 + ($config['direction'] === 'ascending' ? 1 : -1) * $expected;
            if (!in_array($expected, $config['quarterSteps'], true) || !in_array($selected, $config['quarterSteps'], true) || $target < 42 || $target > 216 || (!$config['variableRoot'] && ($root < 48 || $root > 72))) throw new RuntimeException('پاسخ خارج از تعریف آزمون است.', 422);
            $row += ['expected' => $expected, 'selected' => $selected, 'rootMidi' => $root];
        } elseif($config['kind']==='notation') {
            $row+=self::notationAnswer($answer,$config);
        } else {
            $question = self::integer($answer['question'] ?? null, 0, count($config['questions']) - 1);
            $selected = self::integer($answer['selected'] ?? null, 0, count($config['questions'][$question]['choices']) - 1);
            $row += ['question' => $question, 'expected' => $config['questions'][$question]['correct'], 'selected' => $selected];
        }
        return $row;
    }
    private static function notationAnswer(array $answer,array $config): array
    {
        $question=self::integer($answer['question']??null,0,count($config['questions'])-1);
        $recording=MusicPracticeNotation::recording($answer['recording']??null);
        $grade=MusicPracticeNotation::grade($config['questions'][$question]['reference'],$recording,$config['assessment']);
        return ['question'=>$question,'recording'=>$recording,'expected'=>1,'selected'=>$grade['passed']?1:0,'assessment'=>$grade];
    }
    private static function timestamp(mixed $value): string
    {
        $at=self::text($value,40);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?Z$/D', $at) || !checkdate((int)substr($at,5,2),(int)substr($at,8,2),(int)substr($at,0,4)) || substr($at,11,2) > '23' || substr($at,14,2) > '59' || substr($at,17,2) > '59') throw new RuntimeException('زمان پاسخ معتبر نیست.', 422);
        return $at;
    }
}
