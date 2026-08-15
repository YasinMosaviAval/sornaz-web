<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminPostCategoryService
{
    private const TEXT_FIELDS = ['title', 'summary', 'description'];

    public function index(string $search = ''): array
    {
        $where = ['c.deleted_at IS NULL'];
        $bindings = [];
        if (trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $where[] = "(c.name LIKE ? OR c.slug LIKE ? OR c.`group` LIKE ? OR EXISTS (SELECT 1 FROM translations t WHERE t.table_name='categories' AND t.table_id=c.category_id AND t.deleted_at IS NULL AND t.value LIKE ?))";
            $bindings = [$term, $term, $term, $term];
        }
        $sql = "SELECT c.*,
            (SELECT COUNT(*) FROM posts p WHERE p.deleted_at IS NULL AND FIND_IN_SET(CAST(c.category_id AS CHAR), REPLACE(p.categories,' ','')) > 0) posts_count
            FROM categories c WHERE " . implode(' AND ', $where) . ' ORDER BY c.category_id DESC';
        $statement = db()->prepare($sql);
        $statement->execute($bindings);
        return array_map(fn(array $row) => $this->map($row), $statement->fetchAll());
    }

    public function create(int $actor, array $data): int
    {
        [$values, $texts] = $this->validated($data, 0);
        return transaction(function () use ($actor, $values, $texts) {
            $id = (int) DB::table('categories')->insertGetId(['created_by' => $actor] + $this->quoteReservedColumns($values));
            $this->setTexts($id, $texts, $actor);
            return $id;
        });
    }

    public function update(int $actor, int $id, array $data): void
    {
        $this->find($id);
        [$values, $texts] = $this->validated($data, $id);
        transaction(function () use ($actor, $id, $values, $texts) {
            DB::table('categories')->where('category_id', $id)->whereNull('deleted_at')->update(['updated_by' => $actor] + $this->quoteReservedColumns($values));
            $this->setTexts($id, $texts, $actor);
        });
    }

    public function delete(int $actor, int $id): void
    {
        $category = $this->find($id);
        if ((int) $category['posts_count'] > 0) throw new RuntimeException('این دسته‌بندی به نوشته‌ها متصل است و قابل حذف نیست.');
        $deletedAt = date('Y-m-d H:i:s');
        transaction(function () use ($actor, $id, $deletedAt) {
            DB::table('translations')->where('table_name', 'categories')->where('table_id', $id)->whereNull('deleted_at')->update(['deleted_at'=>$deletedAt,'deleted_by'=>$actor,'updated_by'=>$actor]);
            DB::table('categories')->where('category_id', $id)->whereNull('deleted_at')->update(['deleted_at'=>$deletedAt,'deleted_by'=>$actor,'updated_by'=>$actor]);
        });
    }

    private function find(int $id): array
    {
        $items = array_values(array_filter($this->index(), fn(array $item) => $item['id'] === $id));
        if (!$items) throw new RuntimeException('دسته‌بندی موردنظر یافت نشد.');
        return $items[0];
    }

    private function validated(array $data, int $id): array
    {
        $faTitle = trim((string)($data['title_fa'] ?? ''));
        $enTitle = trim((string)($data['title_en'] ?? ''));
        if ($faTitle === '') throw new RuntimeException('عنوان فارسی دسته‌بندی الزامی است.');
        $slug = trim((string)($data['slug'] ?? '')) ?: trim(preg_replace('/[^\pL\pN]+/u', '-', mb_strtolower($enTitle ?: $faTitle)), '-');
        if ($slug === '') $slug = 'category-' . time();
        $duplicate = DB::table('categories')->where('slug', $slug)->whereNull('deleted_at')->first();
        if ($duplicate && (int)$duplicate['category_id'] !== $id) throw new RuntimeException('این نامک قبلاً برای دسته‌بندی دیگری ثبت شده است.');
        $texts = [];
        foreach (['fa','en'] as $locale) foreach (self::TEXT_FIELDS as $field) $texts[$locale][$field] = trim((string)($data[$field.'_'.$locale] ?? ''));
        return [[
            'name'=>$faTitle, 'slug'=>$slug, 'group'=>trim((string)($data['group'] ?? 'posts')) ?: 'posts',
            'approved_at'=>date('Y-m-d H:i:s'),
        ], $texts];
    }

    private function map(array $row): array
    {
        $id = (int)$row['category_id'];
        $texts = ['fa'=>[], 'en'=>[]];
        foreach (DB::table('translations')->where('table_name','categories')->where('table_id',$id)->whereIn('field',self::TEXT_FIELDS)->whereNull('deleted_at')->get() as $translation) {
            $texts[$translation['locale']][$translation['field']] = $translation['value'];
        }
        return ['id'=>$id,'title_fa'=>$texts['fa']['title']??$row['name']??'','title_en'=>$texts['en']['title']??'','summary_fa'=>$texts['fa']['summary']??'','summary_en'=>$texts['en']['summary']??'','description_fa'=>$texts['fa']['description']??'','description_en'=>$texts['en']['description']??'','slug'=>$row['slug']??'','group'=>$row['group']??'posts','posts_count'=>(int)($row['posts_count']??0),'created_at'=>$row['created_at']??null];
    }

    private function setTexts(int $id, array $texts, int $actor): void
    {
        foreach ($texts as $locale => $fields) foreach ($fields as $field => $value) {
            $row = DB::table('translations')->where('table_name','categories')->where('table_id',$id)->where('locale',$locale)->where('field',$field)->first();
            $values = ['value'=>$value,'version'=>(int)($row['version']??0)+1,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];
            if ($row) DB::table('translations')->where('translation_id',(int)$row['translation_id'])->update($values);
            else DB::table('translations')->insert(['table_name'=>'categories','table_id'=>$id,'locale'=>$locale,'field'=>$field,'created_by'=>$actor]+$values);
        }
    }

    private function quoteReservedColumns(array $values): array
    {
        $values['`group`'] = $values['group'];
        unset($values['group']);
        return $values;
    }
}
