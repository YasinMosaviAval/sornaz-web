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
$timed=['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[['time'=>'7/8','length'=>846720,'notes'=>[
    ['pitch'=>'C4','duration'=>'8','dots'=>0,'rest'=>false,'staff'=>1,'voice'=>1,'at'=>0,'tuplet'=>['actual'=>3,'normal'=>2]],
    ['pitch'=>'E4','duration'=>'q','dots'=>0,'rest'=>false,'staff'=>1,'voice'=>2,'at'=>0],
]]]]];
$timedSaved=json_decode($service->validate($timed)['score'],true)['measures'][0];
if($timedSaved['time']!=='7/8'||$timedSaved['length']!==846720||$timedSaved['notes'][0]['at']!==0||$timedSaved['notes'][0]['tuplet']['actual']!==3||$timedSaved['notes'][1]['voice']!==2)throw new Exception('Independent timing fields lost');
$timed['score']['measures'][]=['notes'=>[['pitch'=>'F4','duration'=>'h','dots'=>0,'rest'=>false,'staff'=>1],['pitch'=>'G4','duration'=>'q','dots'=>0,'rest'=>false,'staff'=>1]]];
$service->validate($timed);
$timed['score']['measures'][1]['notes'][0]['duration']='w';
try{$service->validate($timed);throw new Exception('Time change was not carried into next measure');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$timed['score']['measures'][1]['notes'][0]['duration']='h';
$timed['score']['measures'][0]['notes'][1]['voice']=1;
try{$service->validate($timed);throw new Exception('Overlapping notes accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$repeat=['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[
    ['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]],'repeat'=>['start'=>true],'barline'=>'double'],
    ['notes'=>[['pitch'=>'D4','duration'=>'q','dots'=>0,'rest'=>false]],'repeat'=>['end'=>2,'endings'=>[1]]],
    ['notes'=>[['pitch'=>'E4','duration'=>'q','dots'=>0,'rest'=>false]],'repeat'=>['endings'=>[2],'marker'=>'fine','jump'=>'dcAlFine']],
]]];
$repeatSaved=json_decode($service->validate($repeat)['score'],true)['measures'];
if($repeatSaved[0]['repeat']['start']!==true||$repeatSaved[1]['repeat']['end']!==2||$repeatSaved[2]['repeat']['jump']!=='dcAlFine')throw new Exception('Repeat structure lost');
$repeat['score']['measures'][]=['notes'=>[],'repeat'=>['marker'=>'coda']];
if(count(json_decode($service->validate($repeat)['score'],true)['measures'])!==4)throw new Exception('Trailing repeat marker was removed');
$repeat['score']['measures'][1]['repeat']['end']=99;
try{$service->validate($repeat);throw new Exception('Invalid repeat count accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$repeat['score']['measures'][1]['repeat']['end']=2;$repeat['score']['measures'][2]['repeat']['jump']='dsAlFine';
try{$service->validate($repeat);throw new Exception('D.S. without Segno accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$repeatMeasure=['metadata'=>$meta+['clef'=>'treble'],'score'=>['measures'=>[
    ['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]]],
    ['notes'=>[],'repeat'=>['measure'=>1]],
]]];
$service->validate($repeatMeasure);
$repeatMeasure['score']['measures'][0]['time']='4/4';$repeatMeasure['score']['measures'][0]['timeSymbol']='common';
if(json_decode($service->validate($repeatMeasure)['score'],true)['measures'][0]['timeSymbol']!=='common')throw new Exception('Common time symbol was lost');
$repeatMeasure['score']['measures'][0]['timeSymbol']='cut';
try{$service->validate($repeatMeasure);throw new Exception('Invalid cut time accepted with 4/4');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$repeatMeasure['score']['measures'][0]['timeSymbol']='common';
$repeatMeasure['score']['measures'][1]['notes']=[['pitch'=>'D4','duration'=>'q','dots'=>0,'rest'=>false]];
try{$service->validate($repeatMeasure);throw new Exception('Measure repeat hid existing notes');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
echo "Notation validation: legacy scores, staves, chords, timing, tuplets and repeats passed.\n";
