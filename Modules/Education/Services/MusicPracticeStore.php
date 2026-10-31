<?php
namespace Modules\Education\Services;

use PDO;
use RuntimeException;
use Modules\System\Services\AccessControl;

class MusicPracticeStore
{
    private array $legacyTitleColumns=[];
    private array $nodeScopeColumns=[];
    public function supportsSectionSubscriptions(): bool
    {
        return $this->hasNodeScope('music_practice_plans')&&$this->hasNodeScope('music_practice_orders');
    }
    public function hasNodeScope(string $table): bool
    {
        if(!in_array($table,['music_practice_plans','music_practice_orders'],true))throw new \LogicException('Unsupported subscription table');
        return $this->hasColumn($table,'node_id');
    }
    public function supportsLevelDeletion(): bool {return $this->hasColumn('music_practice_exams','deleted_at')&&$this->hasColumn('music_practice_exams','deleted_by');}
    public function supportsNodeDeletion(): bool {return $this->hasColumn('music_practice_nodes','deleted_at')&&$this->hasColumn('music_practice_nodes','deleted_by');}
    public function liveNodeSql(string $alias=''): string
    {
        if($alias!==''&&!preg_match('/^[a-z]+$/D',$alias))throw new \LogicException('Invalid alias');
        return $this->supportsNodeDeletion()?' AND '.($alias!==''?$alias.'.':'').'deleted_at IS NULL':'';
    }
    public function liveExamSql(string $alias=''): string
    {
        if($alias!==''&&!preg_match('/^[a-z]+$/D',$alias))throw new \LogicException('Invalid alias');
        return $this->supportsLevelDeletion()?' AND '.($alias!==''?$alias.'.':'').'deleted_at IS NULL':'';
    }
    private function hasColumn(string $table,string $column): bool
    {
        $key=$table.':'.$column;
        if(!array_key_exists($key,$this->nodeScopeColumns))$this->nodeScopeColumns[$key]=$this->connection()->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'
            ?(bool)$this->one('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?',['f_'.$table,$column])
            :in_array($column,array_column($this->rows('PRAGMA table_info(f_'.$table.')'),'name'),true);
        return $this->nodeScopeColumns[$key];
    }
    public function __construct(protected ?PDO $pdo = null, public readonly bool $onlineOnly = false) {}
    public function connection(): PDO { return $this->pdo ??= db(); }
    public function rows(string $sql, array $values = []): array
    {
        $q = $this->connection()->prepare($sql); $q->execute($values); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function one(string $sql, array $values = []): ?array { return $this->rows($sql, $values)[0] ?? null; }
    public function execute(string $sql, array $values = []): int
    {
        $q = $this->connection()->prepare($sql); $q->execute($values); return $q->rowCount();
    }
    public function ready(): void
    {
        try { $this->rows('SELECT n.id,e.teacher_bps,v.exam_id,g.user_id,r.client_id,a.sequence_number,p.exam_id,o.checkout_key,m.amount FROM f_music_practice_nodes n CROSS JOIN f_music_practice_exams e CROSS JOIN f_music_practice_versions v CROSS JOIN f_music_practice_grants g CROSS JOIN f_music_practice_runs r CROSS JOIN f_music_practice_answers a CROSS JOIN f_music_practice_plans p CROSS JOIN f_music_practice_orders o CROSS JOIN f_music_practice_revenue m WHERE 1=0'); }
        catch (\PDOException $e) { throw new RuntimeException('کتابخانهٔ آزمون‌ها هنوز روی سرور آماده نیست.', 503); }
    }
    public function allows(int $actor, string $action): bool
    {
        return $actor === 1 || ($actor > 0 && (new AccessControl())->allows($actor, 'music_practice.' . $action));
    }
    public function require(int $actor, string $action): void
    {
        if (!$this->allows($actor, $action)) throw new RuntimeException('مجوز این عملیات را ندارید.', 403);
    }
    public function transaction(callable $work): mixed
    {
        $db = $this->connection();
        $owns = !$db->inTransaction();
        if ($owns) $db->beginTransaction();
        try { $result = $work(); if ($owns) $db->commit(); return $result; }
        catch (\Throwable $e) {
            if ($owns && $db->inTransaction()) $db->rollBack();
            if($e instanceof \PDOException&&in_array((string)$e->getCode(),['23000','40001'],true))throw new RuntimeException('دادهٔ تکراری یا تغییر هم‌زمان شناسایی شد؛ دوباره دریافت کنید.',409);
            throw $e;
        }
    }
    public function lock(): string { return $this->connection()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : ''; }
    public function now(): string { return gmdate('Y-m-d H:i:s'); }
    public function saveTitles(string $table,int $id,string $fa,string $en,int $actor): void
    {
        foreach(['fa'=>$fa,'en'=>$en] as $language=>$value)\Core\translation\EntityText::save($table,$id,'title',$value,$actor,$this->connection(),$language);
    }
    public function titleColumns(string $table,string $alias): string
    {
        // Computed SELECT aliases only, for older readers during deployment.
        // Neither expression reads a legacy entity column.
        return \Core\translation\EntityText::expression($table,$alias.'.id','title','fa').' AS title_fa,'.\Core\translation\EntityText::expression($table,$alias.'.id','title','en').' AS title_en';
    }
    public function insertTitled(string $table,array $fields): int
    {
        if(!in_array($table,['music_practice_nodes','music_practice_exams','music_practice_plans'],true))throw new \LogicException('Unsupported title entity');
        foreach(array_keys($fields) as $column)if(!preg_match('/^[a-z_]+$/D',$column)||in_array($column,['title','title_fa','title_en'],true))throw new \LogicException('Translated titles must use f_translations');
        // Old NOT NULL columns receive no text during the rolling deployment.
        // All actual titles are written by saveTitles(), only to f_translations.
        foreach($this->legacyColumns($table) as $column)$fields[$column]='';
        $this->execute('INSERT INTO f_'.$table.' ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',array_fill(0,count($fields),'?')).')',array_values($fields));
        return (int)$this->connection()->lastInsertId();
    }
    private function legacyColumns(string $table): array
    {
        if(array_key_exists($table,$this->legacyTitleColumns))return $this->legacyTitleColumns[$table];
        $legacy=$table==='music_practice_plans'?['title']:['title_fa','title_en'];
        if($this->connection()->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'){
            $columns=array_column($this->rows('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME IN ('.implode(',',array_fill(0,count($legacy),'?')).')',['f_'.$table,...$legacy]),'COLUMN_NAME');
        }else{
            $columns=array_values(array_intersect(array_column($this->rows('PRAGMA table_info(f_'.$table.')'),'name'),$legacy));
        }
        return $this->legacyTitleColumns[$table]=$columns;
    }
    public function translatedTitles(string $table,array $rows): array
    {
        if(!$rows)return [];
        if(!in_array($table,['music_practice_nodes','music_practice_exams'],true))throw new \LogicException('Unsupported catalog title source');
        $translations=[];$first=[];
        foreach(array_chunk(array_column($rows,'id'),500) as $ids){
            $sql="SELECT table_id,locale,value FROM f_translations WHERE table_name=? AND field='title' AND deleted_at IS NULL AND table_id IN (".implode(',',array_fill(0,count($ids),'?')).') ORDER BY version DESC,translation_id DESC';
            foreach($this->rows($sql,[$table,...$ids]) as $item){
                $id=$item['table_id'];$language=$item['locale'];
                if(!isset($translations[$id]))$translations[$id]=[];
                if(!array_key_exists($language,$translations[$id]))$translations[$id][$language]=$item['value'];
                if(!array_key_exists($id,$first))$first[$id]=$item['value'];
            }
        }
        foreach($rows as &$row){
          unset($row['title_fa'],$row['title_en']);
          foreach(['fa','en'] as $language){
            $values=$translations[$row['id']]??[];
            $value=array_key_exists($language,$values)?$values[$language]:(array_key_exists('fa',$values)?$values['fa']:($first[$row['id']]??null));
            $row[$language==='fa'?'titleFa':'titleEn']=$value??'';
            $row['title_'.$language]=$value??''; // In-memory legacy read alias, never a storage fallback.
          }
        }
        unset($row);return $rows;
    }
}
