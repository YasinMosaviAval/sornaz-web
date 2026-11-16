<?php
require __DIR__ . '/../Modules/Notation/Services/NotationService.php';
require __DIR__ . '/../Modules/Education/Services/MusicPracticeNotation.php';
$service = (new ReflectionClass(Modules\Notation\Services\NotationService::class))->newInstanceWithoutConstructor();
$source = ['metadata'=>['title'=>'Free timing', 'instrument'=>'Tar', 'clef'=>'treble', 'key'=>'C', 'time'=>'free', 'tempo_note'=>'q', 'bpm'=>100],
    'score'=>['measures'=>[['notes'=>[
        ['pitch'=>'C4', 'duration'=>'q', 'dots'=>0, 'rest'=>false, 'durationSeconds'=>0.01],
        ['pitch'=>'D4', 'duration'=>'q', 'dots'=>0, 'rest'=>false, 'durationSeconds'=>5],
        ['pitch'=>'B4', 'duration'=>'q', 'dots'=>0, 'rest'=>true, 'durationSeconds'=>0.29],
    ]]]]];
$validated = $service->validate($source);
$score = json_decode($validated['score'], true);
if (json_decode($validated['metadata'], true)['time'] !== 'free') throw new LogicException('Free meter lost');
if (array_column($score['measures'][0]['notes'], 'durationSeconds') !== [0.01, 5, 0.29]) throw new LogicException('Seconds lost');
if (array_column($score['measures'][0]['notes'], 'at') !== [0, 241920, 121201920]) throw new LogicException('Inexact positions');
function rejected(array $data): void {
    global $service;
    try { $service->validate($data); throw new LogicException('Invalid free timing accepted'); }
    catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
}
foreach ([0, 0.001, 0.015, 5.01, '1', null, true, [], INF, NAN] as $value) {
    $bad=$source; $bad['score']['measures'][0]['notes'][0]['durationSeconds']=$value; rejected($bad);
}
$bad=$source; unset($bad['score']['measures'][0]['notes'][0]['durationSeconds']); rejected($bad);
$bad=$source; $bad['score']['measures'][0]['notes'][0]['tuplet']=['actual'=>3,'normal'=>2]; rejected($bad);
foreach (['time','timeSymbol','length','repeat','barline','multiRest','beamPattern','displayNumber'] as $field) {
    $bad=$source; $bad['score']['measures'][0][$field]=1; rejected($bad);
}
$bad=$source; $bad['metadata']['time']='4/4'; rejected($bad);
$bad=$source; $bad['score']['measures'][0]['notes'][1]['at']=0; rejected($bad);
$nested=$source; $nested['score']['sections']=[$source['score']];
$nested['score']['parts']=[['metadata'=>$source['metadata'],'score'=>$source['score']]];
$saved=json_decode($service->validate($nested)['score'],true);
if ($saved['sections'][0]['measures'][0]['notes'][1]['durationSeconds'] !== 5 || $saved['parts'][0]['metadata']['time'] !== 'free') throw new LogicException('Nested timing lost');
try {
    Modules\Education\Services\MusicPracticeNotation::reference(['metadata'=>json_decode($validated['metadata'], true), 'score'=>$score]);
    throw new LogicException('Written grading accepted unsupported seconds');
} catch (RuntimeException $error) { if ($error->getCode() !== 422) throw $error; }
echo "Free meter PHP: bounds, persistence, exact positions, overlap rejection and nested sections/parts passed.\n";
// Real SQL writes are confined to a disposable in-memory SQLite database.
require __DIR__ . '/../Modules/CourseMarket/Repositories/CourseRepository.php';
$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$db->exec('CREATE TABLE users (user_id INTEGER PRIMARY KEY, username TEXT, deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheets (id INTEGER PRIMARY KEY AUTOINCREMENT,owner_id INTEGER,title TEXT,metadata TEXT,score TEXT,visibility TEXT,version INTEGER DEFAULT 1,deleted_at TEXT)');
$db->exec('CREATE TABLE music_sheet_bookmarks (user_id INTEGER,sheet_id INTEGER)');
$db->exec("INSERT INTO users (user_id,username) VALUES (7,'fixture-owner')");
$storedService = new Modules\Notation\Services\NotationService(new Modules\CourseMarket\Repositories\CourseRepository($db));
$created = $storedService->save(7, 0, $source);
$reopened = $storedService->show(7, $created['id']);
if ($reopened['metadata']['time'] !== 'free' || $reopened['score']['measures'][0]['notes'][1]['durationSeconds'] !== 5 || !$reopened['editable']) throw new LogicException('SQL round trip lost seconds');
try { $storedService->show(8, $created['id']); throw new LogicException('Other account read private free score'); }
catch (RuntimeException $error) { if ($error->getCode() !== 404) throw $error; }
try { $storedService->save(0, 0, $source); throw new LogicException('Guest wrote free score'); }
catch (RuntimeException $error) { if ($error->getCode() !== 401) throw $error; }
if ((int) $db->query('SELECT COUNT(*) FROM music_sheets')->fetchColumn() !== 1) throw new LogicException('Rejected request wrote data');
echo "Free meter SQL: isolated SQLite save/read round trip, owner access, other-account rejection and guest write rejection passed; MySQL is not covered.\n";
