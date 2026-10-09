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
$score['metadata']=$meta+['clef'=>'treble','scale_type'=>'major','tempo_dots'=>2,'subtitle'=>str_repeat('a',1000)];
$result=$service->validate($score);
$savedMeta=json_decode($result['metadata'],true);
if($savedMeta['tempo_dots']!==2||$savedMeta['scale_type']!=='major'||strlen($savedMeta['subtitle'])!==1000)throw new Exception('Flutter metadata fields lost');
$score['metadata']['scale_type']='minor';
try{$service->validate($score);throw new Exception('Incompatible scale accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$score['metadata']['scale_type']='major';$score['metadata']['tempo_dots']=3;
try{$service->validate($score);throw new Exception('Invalid beat dots accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$piano=['metadata'=>$meta+['clef'=>'treble','staves'=>[['clef'=>'treble'],['clef'=>'bass']]],'score'=>['measures'=>[['notes'=>[
    ['pitch'=>'C4','duration'=>'w','dots'=>0,'rest'=>false,'staff'=>1],
    ['pitch'=>'C3','duration'=>'w','dots'=>0,'rest'=>false,'staff'=>2,'pitches'=>[['pitch'=>'E3','accidental'=>''],['pitch'=>'G3','accidental'=>'']]],
]]]]];
$saved=$service->validate($piano);
$savedMeta=json_decode($saved['metadata'],true);
$savedNotes=json_decode($saved['score'],true)['measures'][0]['notes'];
if(count($savedMeta['staves'])!==2||$savedMeta['staves'][1]['clef']!=='bass'||$savedNotes[1]['staff']!==2||count($savedNotes[1]['pitches'])!==2)throw new Exception('Piano staves or chord tones were lost');
$legacy=$service->validate(['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]]]]]]);
if(json_decode($legacy['score'],true)['measures'][0]['notes'][0]['staff']!==1)throw new Exception('Legacy note did not default to upper staff');
$piano['score']['measures'][0]['notes'][1]['staff']=3;
try{$service->validate($piano);throw new Exception('Invalid staff accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$preserved=['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[
    ['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]]],
    ['notes'=>[],'preserve'=>true],
]]];
$saved=$service->validate($preserved);
$savedMeasures=json_decode($saved['score'],true)['measures'];
if(count($savedMeasures)!==2||$savedMeasures[1]['preserve']!==true)throw new Exception('Intentional empty measure was lost');
$preserved['score']['measures'][1]['preserve']='yes';
try{$service->validate($preserved);throw new Exception('Invalid preserve flag accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$restScore=['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[['notes'=>[
    ['pitch'=>'B4','duration'=>'h','dots'=>0,'rest'=>true,'staff'=>2,'accidental'=>'','pitches'=>[]],
]]]]];
$restSaved=$service->validate($restScore);
if(json_decode($restSaved['score'],true)['measures'][0]['notes'][0]['staff']!==2)throw new Exception('Converted chord rest lost its staff');
$restScore['score']['measures'][0]['notes'][0]['pitches']=[['pitch'=>'E4','accidental'=>'']];
try{$service->validate($restScore);throw new Exception('Rest with chord tones accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
echo "Notation validation: legacy scores, seven clefs, independent piano staves, chords and tempo passed.\n";
