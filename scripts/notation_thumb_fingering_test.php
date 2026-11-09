<?php
require __DIR__ . '/../Modules/Notation/Services/NotationService.php';
$service = (new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$source = ['metadata'=>['title'=>'Thumb fingering','instrument'=>'Tar','clef'=>'treble','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100], 'score'=>['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false,'finger'=>'+','pitches'=>[['pitch'=>'E4','finger'=>'+']]]]]]]];
$saved = json_decode($service->validate($source)['score'], true);
if ($saved['measures'][0]['notes'][0]['finger'] !== '+' || $saved['measures'][0]['notes'][0]['pitches'][0]['finger'] !== '+') throw new LogicException('Thumb fingering lost');
foreach (['6', 'thumb', '<script>'] as $value) {
    foreach (['note', 'chord'] as $target) {
        $invalid = $source;
        if ($target === 'note') $invalid['score']['measures'][0]['notes'][0]['finger'] = $value;
        else $invalid['score']['measures'][0]['notes'][0]['pitches'][0]['finger'] = $value;
        try { $service->validate($invalid); throw new LogicException('Invalid fingering accepted'); }
        catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
    }
}
echo "Thumb fingering: note/chord persistence and invalid-value rejection passed.\n";
