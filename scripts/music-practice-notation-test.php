<?php
/** Local policy fixture; no database or user media is written. */
require_once __DIR__.'/../Modules/Notation/Services/NotationService.php';
require_once __DIR__.'/../Modules/Education/Services/MusicPracticeNotation.php';
require_once __DIR__.'/../Modules/Education/Services/MusicPracticeDefinition.php';
use Modules\Education\Services\MusicPracticeNotation as N;
use Modules\Education\Services\MusicPracticeDefinition as D;
$service=(new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
if(($argv[1]??'')==='question'){echo json_encode(N::question(json_decode(stream_get_contents(STDIN),true,512,JSON_THROW_ON_ERROR),$service),JSON_THROW_ON_ERROR);exit;}
if(($argv[1]??'')==='grade'){ $v=json_decode(stream_get_contents(STDIN),true,512,JSON_THROW_ON_ERROR);echo json_encode(N::grade($v['reference'],N::recording($v['recording']),$v['assessment']),JSON_THROW_ON_ERROR);exit;}
$checks=0;
function check($test,$message){$GLOBALS['checks']++;if(!$test)throw new RuntimeException($message);}
function reject($work){try{$work();}catch(RuntimeException $e){check($e->getCode()===422,'Wrong validation error: '.$e->getMessage());return;}throw new RuntimeException('Invalid input accepted');}
$sheet=['metadata'=>['title'=>'Fixture','instrument'=>'Tar','clef'=>'treble','key'=>'C','scale_type'=>'major','time'=>'4/4','tempo_note'=>'q','bpm'=>120], 'score'=>['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','rest'=>false],['pitch'=>'D4','duration'=>'q','rest'=>false]]]]]];
$svg='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 40"><path d="M1 2 L3 4" fill="black"/></svg>';
$config=D::config(['kind'=>'notation','assessment'=>'pitch_rhythm','questions'=>[['sheet'=>$sheet,'preview'=>$svg,'reference'=>['notes'=>[[0,1,99]]]]]],$service);
$reference=$config['questions'][0]['reference'];
check($reference['notes']===[[0,500,60],[500,1000,62]],'Canonical pitch/time not generated');
check($reference['durationMs']===2000,'Implicit rests not counted');
$frames=[];for($at=0;$at<2000;$at+=50)$frames[]=[$at,$at<500?26163:($at<1000?29366:0),$at<1000?100:0];
$recording=['durationMs'=>2000,'frames'=>$frames];
$grade=N::grade($reference,N::recording($recording),'pitch_rhythm');check($grade['passed']&&$grade['pitch']===100&&$grade['rhythm']===100,'Correct melody rejected');
$answer=['sequence'=>1,'question'=>0,'elapsedMs'=>2000,'answeredAt'=>'2026-11-07T10:00:00.000Z','recording'=>$recording,'selected'=>0];
check(D::answer($answer,$config)['selected']===1,'Server trusted submitted pass flag');
$wrong=$recording;foreach($wrong['frames'] as &$frame)if($frame[1])$frame[1]=(int)round($frame[1]*pow(2,1/12));unset($frame);
check(!N::grade($reference,$wrong,'pitch_rhythm')['passed'],'Wrong pitch passed');
check(N::grade($reference,$wrong,'rhythm')['passed'],'Rhythm-only depends on pitch');
$silence=$recording;foreach($silence['frames'] as &$frame){$frame[1]=0;$frame[2]=0;}unset($frame);
check(!N::grade($reference,$silence,'rhythm')['passed'],'Silence passed');
$held=['notes'=>[[0,2000,60]],'durationMs'=>2000,'beatMs'=>500,'pitchTolerance'=>35];$cut=['durationMs'=>300,'frames'=>array_slice($frames,0,6)];check(!N::grade($held,N::recording($cut),'rhythm')['passed'],'A truncated sustained note passed');
$noise=$recording;foreach($noise['frames'] as &$frame)$frame[2]=100;unset($frame);check(!N::grade($reference,$noise,'pitch_rhythm')['passed'],'Noisy rests passed');
$bad=$recording;$bad['frames'][1][0]=1;reject(fn()=>N::recording($bad));
reject(fn()=>N::recording(['durationMs'=>40001,'frames'=>$frames]));
foreach(['<script>alert(1)</script>','<image href="https://invalid.test/x"/>','<g onclick="alert(1)"/>','<g xmlns="urn:evil"/>','<?evil test?>'] as $attack)reject(fn()=>N::question(['sheet'=>$sheet,'preview'=>str_replace('</svg>',$attack.'</svg>',$svg)],$service));
$badSheet=$sheet;$badSheet['score']['measures'][0]['notes'][0]['pitches']=[['pitch'=>'E4','accidental'=>'']];reject(fn()=>N::question(['sheet'=>$badSheet,'preview'=>$svg],$service));
$badSheet=$sheet;$badSheet['score']['measures'][0]['notes'][0]['voice']=2;reject(fn()=>N::question(['sheet'=>$badSheet,'preview'=>$svg],$service));
$quarter=$sheet;$quarter['metadata']['scale_type']='iran_shur';$quarter['metadata']['key']='G';$quarter['score']['measures'][0]['notes'][0]['pitch']='A4';$q=N::question(['sheet'=>$quarter,'preview'=>$svg],$service);check($q['reference']['notes'][0][2]===68.5,'Iranian key signature not applied');
$tied=$sheet;$tied['score']['measures']=[['notes'=>[['pitch'=>'C4','duration'=>'w','rest'=>false,'accidental'=>'#','tieNext'=>true]]],['notes'=>[['pitch'=>'C4','duration'=>'q','rest'=>false,'tiePrevious'=>true]]]];$q=N::question(['sheet'=>$tied,'preview'=>$svg],$service);check($q['reference']['notes']===[[0,2500,61]],'Accidental or length lost across a tied barline');
$badSheet=$sheet;$badSheet['score']['measures'][0]['notes'][0]['octaveShift']='8va';reject(fn()=>N::question(['sheet'=>$badSheet,'preview'=>$svg],$service));
echo "Music practice notation: $checks checks passed\n";
