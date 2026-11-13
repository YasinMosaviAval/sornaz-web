<?php
require __DIR__ . '/../Modules/Notation/Services/NotationService.php';
$service=(new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$source=['metadata'=>['title'=>'Natural','instrument'=>'Piano','clef'=>'treble','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100],'score'=>['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','rest'=>false,'accidental'=>'n','showAccidental'=>true]]]]]];
$saved=json_decode($service->validate($source)['score'],true);
if (($saved['measures'][0]['notes'][0]['showAccidental']??false)!==true) throw new LogicException('Explicit accidental lost');
foreach (['yes',1,null] as $bad){$invalid=$source;$invalid['score']['measures'][0]['notes'][0]['showAccidental']=$bad;try{$service->validate($invalid);throw new LogicException('Invalid display accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}}
echo "Explicit accidental persistence and invalid-value rejection passed.\n";
