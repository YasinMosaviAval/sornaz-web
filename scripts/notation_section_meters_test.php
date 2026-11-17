<?php
require __DIR__.'/../Modules/CourseMarket/Repositories/CourseRepository.php';
require __DIR__.'/../Modules/Notation/Services/NotationService.php';
require __DIR__.'/../Modules/Education/Services/MusicPracticeNotation.php';
use Modules\Notation\Services\NotationService;
$service=(new ReflectionClass(NotationService::class))->newInstanceWithoutConstructor();
$note=['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false];
$leaf=['measures'=>[['notes'=>[$note]]]];
$free=['time'=>'free','measures'=>[['notes'=>[$note+['durationSeconds'=>0.25]]]]];
$source=['metadata'=>['title'=>'Mixed meters','instrument'=>'Tar','clef'=>'treble','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100],
    'score'=>['time'=>'4/4']+$leaf+['subsections'=>[$free],'sections'=>[['time'=>'3/4']+$leaf+['subsections'=>[['time'=>'5/8']+$leaf]]]],'notation_schema'=>3,'notation_section_meters'=>true];
$clean=json_decode($service->validate($source)['score'],true);
if($clean['time']!=='4/4'||$clean['subsections'][0]['time']!=='free'||$clean['sections'][0]['subsections'][0]['time']!=='5/8')throw new LogicException('Section meters lost');
$inverse=$source;$inverse['metadata']['time']='free';$inverseClean=json_decode($service->validate($inverse)['score'],true);
if($inverseClean['time']!=='4/4'||$inverseClean['sections'][0]['time']!=='3/4')throw new LogicException('Free default overrode numeric section');
$parts=$source;$parts['score']['parts']=[['metadata'=>$source['metadata'],'score'=>$source['score']]];
if(json_decode($service->validate($parts)['score'],true)['parts'][0]['score']['subsections'][0]['time']!=='free')throw new LogicException('Part subsection meter lost');
foreach([null,[],true,'bad','33/4'] as $time){$bad=$source;$bad['score']['sections'][0]['time']=$time;try{$service->validate($bad);throw new LogicException('Invalid meter accepted');}catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}}
$practice=$source;$practice['metadata']['time']='free';$practice['score']=['time'=>'3/4']+$leaf;
$validated=$service->validate($practice);$practice=['metadata'=>json_decode($validated['metadata'],true),'score'=>json_decode($validated['score'],true)];
$reference=Modules\Education\Services\MusicPracticeNotation::reference($practice);
if($reference['durationMs']!==1800)throw new LogicException('Practice ignored numeric section override');
$practice['score']=$free;
try{Modules\Education\Services\MusicPracticeNotation::reference($practice);throw new LogicException('Free practice accepted');}
catch(RuntimeException $e){if($e->getCode()!==422)throw $e;}
$db=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TABLE users (user_id INTEGER PRIMARY KEY,username TEXT,deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheets (id INTEGER PRIMARY KEY AUTOINCREMENT,owner_id INTEGER,title TEXT,metadata TEXT,score TEXT,visibility TEXT,version INTEGER DEFAULT 1,updated_at TEXT,deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheet_bookmarks (user_id INTEGER,sheet_id INTEGER)');
$db->exec("INSERT INTO users VALUES (7,'fixture-owner',NULL)");
// SQLite has no FOR UPDATE; only this test adapter removes that locking syntax.
$repo=new class($db) extends Modules\CourseMarket\Repositories\CourseRepository {
    public function query(string $sql,array $params=[]): array {return parent::query(str_replace(' FOR UPDATE','',$sql),$params);}
};
$stored=new NotationService($repo);$created=$stored->save(7,0,$source);$id=$created['id'];
foreach([[8,403,true,1],[7,409,false,1],[7,409,true,0]] as [$actor,$code,$capability,$version]){
    try{$stored->save($actor,$id,array_replace($source,['notation_section_meters'=>$capability,'version'=>$version]));throw new LogicException('Unsafe update accepted');}
    catch(RuntimeException $e){if($e->getCode()!==$code)throw $e;}
}
$updated=$stored->save(7,$id,$source+['version'=>1]);
if($updated['version']!==2||$updated['score']['subsections'][0]['time']!=='free')throw new LogicException('Update lost mixed meters');
try{$stored->save(7,$id,$source+['version'=>1]);throw new LogicException('Repeated stale update accepted');}
catch(RuntimeException $e){if($e->getCode()!==409)throw $e;}
if((int)$db->query('SELECT COUNT(*) FROM music_sheets')->fetchColumn()!==1)throw new LogicException('Update duplicated score');
echo "Section meters PHP/SQLite: nested validation, persistence, legacy-writer rejection, ownership, version conflicts and compatible updates passed; MySQL is not covered.\n";
