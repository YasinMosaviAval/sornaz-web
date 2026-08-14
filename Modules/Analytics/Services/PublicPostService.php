<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class PublicPostService
{
    public function all(string $locale): array
    {
        $locale = $this->locale($locale);
        $rows = DB::table('posts')->whereNull('deleted_at')->where('status', 'published')->orderBy('published_at', 'DESC')->get();
        return array_map(fn(array $row) => $this->map($row, $locale, false), $rows);
    }

    public function find(int $id, string $locale): array
    {
        $row = DB::table('posts')->where('post_id', $id)->whereNull('deleted_at')->where('status', 'published')->first();
        if (!$row) throw new RuntimeException('مقاله یافت نشد.');
        return $this->map($row, $this->locale($locale), true);
    }

    private function map(array $row, string $locale, bool $withContent): array
    {
        $id = (int)$row['post_id'];
        $fields = $withContent ? ['title','brief','description','content'] : ['title','brief','description'];
        $texts = $this->texts($id, $locale, $fields);
        if ($locale !== 'fa') $texts += $this->texts($id, 'fa', $fields);
        $author = DB::table('users')->where('user_id', (int)($row['author_id'] ?? 0))->first();
        $authorName = '';
        if ($author) {
            $translated = $this->texts((int)$author['user_id'], $locale, ['full_name'], 'users');
            if ($locale !== 'fa') $translated += $this->texts((int)$author['user_id'], 'fa', ['full_name'], 'users');
            $authorName = $translated['full_name'] ?? $author['username'] ?? '';
        }
        $result = [
            'id'=>$id, 'slug'=>$row['slug'] ?? '', 'title'=>$texts['title'] ?? '',
            'summary'=>$texts['brief'] ?? '', 'description'=>$texts['description'] ?? '',
            'categories'=>array_values(array_filter(array_map('trim', explode(',', (string)($row['categories'] ?? ''))))),
            'cover'=>$row['cover'] ?? '', 'author_name'=>$authorName,
            'published_at'=>$row['published_at'] ?? $row['created_at'] ?? null,
            'updated_at'=>$row['updated_at'] ?? null, 'views'=>(int)($row['views_count'] ?? 0),
            'comment_count'=>(int)($row['comment_count'] ?? 0), 'type'=>$row['type'] ?? 'post',
        ];
        if ($withContent) $result['content'] = $texts['content'] ?? '';
        return $result;
    }

    private function texts(int $id, string $locale, array $fields, string $table = 'posts'): array
    {
        $result = [];
        foreach (DB::table('translations')->where('table_name', $table)->where('table_id', $id)->where('locale', $locale)->whereIn('field', $fields)->whereNull('deleted_at')->get() as $row) $result[$row['field']] = (string)$row['value'];
        return $result;
    }

    private function locale(string $locale): string
    {
        return in_array($locale, ['fa','en'], true) ? $locale : 'fa';
    }
}
