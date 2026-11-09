<?php
require __DIR__ . '/../Modules/Notation/Services/NotationService.php';
require __DIR__ . '/../Modules/Education/Services/MusicPracticeNotation.php';
$service = (new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$meta = ['title'=>'Measure unit', 'instrument'=>'Tar', 'clef'=>'treble', 'key'=>'C', 'time'=>'4/4', 'tempo_note'=>'q', 'bpm'=>100];
$source = ['metadata'=>$meta, 'score'=>['measures'=>[
    ['notes'=>[['pitch'=>'C4', 'duration'=>'q', 'dots'=>0, 'rest'=>false]]],
    ['notes'=>[['pitch'=>'D4', 'duration'=>'q', 'dots'=>0, 'rest'=>false]], 'tempo_unit'=>'8:1', 'tempo'=>['mode'=>'instant', 'bpm'=>90]],
    ['notes'=>[], 'tempo_unit'=>'h:2'],
]]];
$saved = json_decode($service->validate($source)['score'], true);
if (count($saved['measures']) !== 3 || $saved['measures'][1]['tempo_unit'] !== '8:1' || $saved['measures'][2]['tempo_unit'] !== 'h:2') throw new LogicException('Measure units were lost or trimmed');
if ($saved['measures'][1]['tempo']['bpm'] !== 90 || isset($saved['measures'][0]['tempo_unit'])) throw new LogicException('Other measure timing changed');
foreach (['q:3', '8:-1', 'q:1extra', 'invalid:0', '', 8, null, ['duration'=>'q', 'dots'=>0, 'rest'=>false]] as $unit) {
    $invalid = $source;
    $invalid['score']['measures'][1]['tempo_unit'] = $unit;
    try { $service->validate($invalid); throw new LogicException('Invalid measure unit accepted'); }
    catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
}
$parts = $source;
$parts['score']['parts'] = [['metadata'=>$meta, 'score'=>$source['score']]];
$parts['score']['sections'] = [$source['score']];
$nested = json_decode($service->validate($parts)['score'], true);
if ($nested['parts'][0]['score']['measures'][1]['tempo_unit'] !== '8:1' || $nested['sections'][0]['measures'][2]['tempo_unit'] !== 'h:2') throw new LogicException('Embedded measure units lost');
$invalid = ['metadata'=>$meta, 'score'=>['measures'=>[['notes'=>[], 'multiRest'=>2], ['notes'=>[], 'tempo_unit'=>'8:1']]]];
try { $service->validate($invalid); throw new LogicException('Unit change hidden inside multi-rest'); }
catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
$written = $source;
unset($written['score']['measures'][1]['tempo']);
$normalized = $service->validate($written);
try {
    Modules\Education\Services\MusicPracticeNotation::reference(['metadata'=>json_decode($normalized['metadata'], true), 'score'=>json_decode($normalized['score'], true)]);
    throw new LogicException('Written grading silently ignored a measure unit change');
} catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
echo "Measure tempo units: normalized persistence, empty measures, parts/sections, invalid inputs and multi-rest boundaries passed.\n";
