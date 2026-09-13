<?php
require __DIR__.'/../Modules/Notation/Services/NotationService.php';
$service=(new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$meta=['title'=>'Score','instrument'=>'Piano','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100];
foreach(['treble','bass','baritone-f','soprano','mezzo-soprano','alto','tenor'] as $clef){
    $score=['metadata'=>$meta+['clef'=>$clef],'score'=>['measures'=>[
        ['notes'=>[['pitch'=>'A0','duration'=>'w','dots'=>0,'rest'=>false,'tieNext'=>true]]],
        ['notes'=>[['pitch'=>'A0','duration'=>'q','dots'=>0,'rest'=>false,'tiePrevious'=>true],['pitch'=>'C8','duration'=>'256','dots'=>0,'rest'=>false]]]
    ]]];
    $result=$service->validate($score);
    $saved=json_decode($result['score'],true);
    if($saved['measures'][0]['notes'][0]['tieNext']!==true||$saved['measures'][1]['notes'][0]['tiePrevious']!==true)throw new Exception('Ties lost');
}
$score['metadata']['bpm']='100.5';
try{$service->validate($score);throw new Exception('Decimal tempo accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
echo "Notation validation: seven clefs, full keyboard, persisted ties and integer tempo passed.\n";
