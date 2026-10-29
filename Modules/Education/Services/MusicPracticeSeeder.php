<?php
namespace Modules\Education\Services;
final class MusicPracticeSeeder
{
    public function seed(MusicPracticeStore $store): void
    {
$store->transaction(function()use($store){
    $nodes=[['solfege',null,'تمرین سولفژ','Solfege practice'],['rhythm',null,'تمرین ریتم','Rhythm practice'],['scales','solfege','گام شناسی','Scale recognition'],['chord-theory','solfege','آکورد شناسی','Chord recognition'],['frequency','solfege','تشخیص فرکانس','Frequency recognition'],['intervals','solfege','تشخیص فاصله','Interval recognition'],['chords','solfege','تشخیص آکورد','Chord identification'],['harmony','solfege','تشخیص هارمونی','Harmony recognition'],['iranian','intervals','موسیقی ایرانی','Iranian music'],['western','intervals','موسیقی جهانی','Western music']];
    $ids=[];
    foreach($nodes as $position=>[$slug,$parent,$fa,$en]){
        $old=$store->one('SELECT * FROM f_music_practice_nodes WHERE slug=?',[$slug]);
        if($old){$ids[$slug]=(int)$old['id'];continue;}
        $p=$parent?$store->one('SELECT * FROM f_music_practice_nodes WHERE id=?',[$ids[$parent]]):null;
        $store->execute('INSERT INTO f_music_practice_nodes(parent_id,slug,title_fa,title_en,depth,position,published,created_by,updated_at) VALUES(?,?,?,?,?,?,1,1,?)',[$p['id']??null,$slug,$fa,$en,$p?(int)$p['depth']+1:1,$position,$store->now()]);$ids[$slug]=(int)$store->connection()->lastInsertId();
        $store->saveTitles('music_practice_nodes',$ids[$slug],$fa,$en,1);
    }
    $this->exams($store,$ids);
});
    }
    private function exams(MusicPracticeStore $store,array $ids): void
    {
    $data=json_decode(file_get_contents(base_path('Modules/Education/Resources/interval-curriculum.v1.json')),true,512,JSON_THROW_ON_ERROR);
    foreach($data['branches'] as $music=>$levels)foreach($levels as $number=>$config){
        $key=$music.':'.$number;if($store->one('SELECT id FROM f_music_practice_exams WHERE legacy_key=?',[$key]))continue;
        $store->execute("INSERT INTO f_music_practice_exams(node_id,owner_id,title_fa,title_en,position,published,audience,legacy_key,updated_at) VALUES(?,1,?,?,?,1,'public',?,?)",[$ids[$music],'سطح '.$number,'Level '.$number,(int)$number,$key,$store->now()]);
        $id=(int)$store->connection()->lastInsertId();$definition=['stageCount'=>5,'requiredWins'=>10,'config'=>['kind'=>'interval','quarterSteps'=>$config['quarterSteps'],'direction'=>$config['direction'],'variableRoot'=>$config['variableRoot']]];
        $store->saveTitles('music_practice_exams',$id,'سطح '.$number,'Level '.$number,1);
        $store->execute('INSERT INTO f_music_practice_versions(exam_id,version,definition,was_published,created_at) VALUES(?,1,?,1,?)',[$id,json_encode($definition,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$store->now()]);
    }
    }
}
