<?php
require __DIR__.'/../Modules/Notation/Services/NotationService.php';
$service=(new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$leaf=['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]]]]];
$section=$leaf+['subsections'=>array_fill(0,15,$leaf)];
$score=$section+['sections'=>array_fill(0,15,$section)];
$meta=['title'=>'Nested sections','instrument'=>'Tar','clef'=>'treble','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100];
$source=['metadata'=>$meta,'score'=>$score];
$clean=json_decode($service->validate($source)['score'],true);
if(count($clean['sections'])!==15||count($clean['subsections'])!==15||count($clean['sections'][14]['subsections'])!==15)throw new LogicException('Hierarchy lost');
$detect = new ReflectionMethod($service, 'hasSubsections');
if (!$detect->invoke($service, $score) || !$detect->invoke($service, ['measures'=>$leaf['measures'], 'parts'=>[['score'=>$score]]]) || $detect->invoke($service, $leaf)) throw new LogicException('Legacy-client hierarchy detection failed');
$source['score']['parts']=[['metadata'=>$meta,'score'=>$score]];
$parts=json_decode($service->validate($source)['score'],true);
if(count($parts['parts'][0]['score']['sections'][14]['subsections'])!==15)throw new LogicException('Part hierarchy lost');
foreach(['overflow','recursive','parts','sections'] as $case){$bad=['metadata'=>$meta,'score'=>$score];if($case==='overflow')$bad['score']['subsections'][]=$leaf;else $bad['score']['subsections'][0][$case==='recursive'?'subsections':$case]=[$leaf];try{$service->validate($bad);throw new LogicException('Invalid hierarchy accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}}
echo "Subsections: 16x16 hierarchy, embedded parts, overflow and forbidden recursion passed.\n";
