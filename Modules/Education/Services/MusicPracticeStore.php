<?php
namespace Modules\Education\Services;

use PDO;
use RuntimeException;
use Modules\System\Services\AccessControl;

class MusicPracticeStore
{
    public function __construct(protected ?PDO $pdo = null) {}
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
        $fa=\Core\translation\EntityText::expression($table,$alias.'.id','title','fa');$en=\Core\translation\EntityText::expression($table,$alias.'.id','title','en');
        return "COALESCE(NULLIF($fa,''),$alias.title_fa) title_fa,COALESCE(NULLIF($en,''),$alias.title_en) title_en";
    }
}
