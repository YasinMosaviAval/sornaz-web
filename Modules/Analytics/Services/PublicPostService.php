<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class PublicPostService
{
    public function all(string $locale): array
    {
        $locale = $this->locale($locale);
        $rows = DB::table('posts')->whereNull('deleted_at')->where('status', 'published')->where('visibility', 'public')->where('type', 'post')->orderBy('published_at', 'DESC')->get();
        return array_map(fn(array $row) => $this->map($row, $locale, false), $rows);
    }

    public function latest(string $locale, int $limit = 3): array
    {
        $locale = $this->locale($locale);
        $rows = DB::table('posts')
            ->whereNull('deleted_at')
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where('type', 'post')
            ->orderBy('created_at', 'DESC')
            ->limit(max(1, $limit))
            ->get();
        return array_map(fn(array $row) => $this->map($row, $locale, false), $rows);
    }

    public function find(int $id, string $locale): array
    {
        $row = DB::table('posts')->where('post_id', $id)->where('type', 'post')->where('visibility', 'public')->whereNull('deleted_at')->where('status', 'published')->first();
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
        $categoryIds = array_values(array_filter(array_map('trim', explode(',', (string)($row['categories'] ?? ''))), fn($value) => ctype_digit($value)));
        $categoryTitles = $this->categoryTitles($categoryIds, $locale);
        $images = $this->articleImages($id);
        $related = $this->relatedPosts($row['related_posts_id'] ?? '', $locale, $id);
        $result = [
            'id'=>$id, 'slug'=>$row['slug'] ?? '', 'title'=>$texts['title'] ?? '',
            'summary'=>$texts['brief'] ?? '', 'description'=>$texts['description'] ?? '',
            'category_ids'=>array_map('intval', $categoryIds),
            'categories'=>array_values(array_filter(array_map(fn($id) => $categoryTitles[(int)$id] ?? null, $categoryIds))),
            'cover'=>$images['main'] ?: ($row['cover'] ?? ''), 'thumbnail'=>$images['thumbnail'], 'content_images'=>$images['content'], 'author_name'=>$authorName,
            'related_posts'=>$related,
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

    private function categoryTitles(array $ids, string $locale): array
    {
        if (!$ids) return [];
        $result = [];
        foreach (DB::table('translations')->where('table_name', 'categories')->whereIn('table_id', $ids)->where('locale', $locale)->where('field', 'title')->whereNull('deleted_at')->get() as $row) {
            $result[(int)$row['table_id']] = (string)$row['value'];
        }
        if ($locale !== 'fa') {
            $missing = array_values(array_diff($ids, array_keys($result)));
            foreach (DB::table('translations')->where('table_name', 'categories')->whereIn('table_id', $missing)->where('locale', 'fa')->where('field', 'title')->whereNull('deleted_at')->get() as $row) {
                $result[(int)$row['table_id']] = (string)$row['value'];
            }
        }
        return $result;
    }

    private function articleImages(int $id): array
    {
        $prefix=sprintf('sornaz_%04d_', $id); $origin=base_path('assets/images/articles/origin'); $thumb=base_path('assets/images/articles/thumbnails');
        $url=function(string $file): string { $root=rtrim(str_replace('\\','/',base_path('assets')),'/'); $normalized=str_replace('\\','/',$file); return '/assets'.substr($normalized,strlen($root)); };
        $find=function(string $dir,string $suffix) use ($prefix,$url){foreach(['webp','png','jpg','jpeg'] as $ext){$files=glob($dir.'/'.$prefix.'*'.$suffix.'.'.$ext)?:[];if($files)return $url($files[0]);}return '';};
        $main=$find($origin,'_00_fa'); $thumbnail=$find($thumb,'_00_fa-300x169'); $content=[];
        foreach(glob($origin.'/'.$prefix.'*.webp')?:[] as $file){if(str_contains(basename($file),'_00_fa'))continue;$content[]=$url($file);}
        if(!$main)$main=$find($origin,''); if(!$thumbnail)$thumbnail=$main;
        return ['main'=>$main,'thumbnail'=>$thumbnail,'content'=>$content];
    }

    private function relatedPosts(string $value,string $locale,int $currentId): array
    {
        $ids=array_values(array_filter(array_map('intval',preg_split('/[,\s]+/',trim($value)))));$ids=array_values(array_filter($ids,fn($id)=>$id!==$currentId));if(!$ids)return[];$out=[];
        foreach($ids as $id){$post=DB::table('posts')->where('post_id',$id)->where('type','post')->where('status','published')->whereNull('deleted_at')->first();if(!$post)continue;$texts=$this->texts($id,$locale,['title','brief','description']);if($locale!=='fa')$texts+=$this->texts($id,'fa',['title','brief','description']);$categoryIds=array_values(array_filter(array_map('trim',explode(',',(string)($post['categories']??''))),fn($value)=>ctype_digit($value)));$categoryTitles=$this->categoryTitles($categoryIds,$locale);$img=$this->articleImages($id);$out[]=['id'=>$id,'title'=>$texts['title']??'مقاله','summary'=>$texts['brief']??($texts['description']??''),'categories'=>array_values(array_filter(array_map(fn($categoryId)=>$categoryTitles[(int)$categoryId]??null,$categoryIds))),'published_at'=>$post['published_at']??$post['created_at']??null,'thumbnail'=>$img['thumbnail']?:$img['main']];}return$out;
    }

    private function locale(string $locale): string
    {
        return in_array($locale, ['fa','en'], true) ? $locale : 'fa';
    }
}
