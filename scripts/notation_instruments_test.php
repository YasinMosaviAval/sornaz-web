<?php
namespace Core\translation {
    class TranslationService {
        public static function manager(): object { return new class {
            public function get(string $table, int $id, string $field, string $locale): string {
                if ($table!=='instruments'||$field!=='title') throw new \Exception('Wrong catalog');
                return $locale==='fa' ? 'ساز '.$id : ($id===42 ? 'Santur' : '');
            }
        }; }
    }
}
namespace {
require __DIR__.'/../Modules/CourseMarket/Repositories/CourseRepository.php';
require __DIR__.'/../Modules/Notation/Services/NotationService.php';
$repository=new class extends \Modules\CourseMarket\Repositories\CourseRepository {
    public function __construct() {}
    public function query(string $sql, array $params=[]): array {
        if (!str_contains($sql,'FROM instruments')||!str_contains($sql,'deleted_at IS NULL')) throw new \Exception('Catalog must exclude deleted instruments');
        return !$params ? [['instrument_id'=>7],['instrument_id'=>42]] : ($params[0]===42 ? [['instrument_id'=>42]] : []);
    }
};
$service=new \Modules\Notation\Services\NotationService($repository);
$items=$service->instruments();
if(count($items)!==2||$items[1]['en']!=='Santur'||$items[0]['en']!==$items[0]['fa'])throw new \Exception('Catalog translations or fallback lost');
$data=['metadata'=>['title'=>'Score','instrument'=>'42','key'=>'C','time'=>'4/4','tempo_note'=>'q','bpm'=>100,'clef'=>'treble'],'score'=>['measures'=>[['notes'=>[['pitch'=>'C4','duration'=>'q','dots'=>0,'rest'=>false]]]]]];
$saved=$service->validate($data);
if(json_decode($saved['metadata'],true)['instrument']!=='42')throw new \Exception('Instrument ID lost');
$data['metadata']['instrument']='999';
try{$service->validate($data);throw new \Exception('Unknown instrument accepted');}catch(\RuntimeException $e){if($e->getCode()!==422)throw $e;}
echo "Notation instruments: translated database catalog, stable IDs and invalid/deleted ID rejection passed.\n";
}
