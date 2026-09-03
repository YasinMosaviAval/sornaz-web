<?php

namespace Modules\Analytics\Services;

use Core\database\DB;

class ArticleApiService
{
    public function __construct(
        private PublicPostService $posts,
        private PublicCommentService $comments
    ) {}

    public function articles(array $query, string $locale): array
    {
        $page = $this->positiveInt($query['page'] ?? 1, 1);
        $perPage = min($this->positiveInt($query['per_page'] ?? 10, 10), 50);
        $categoryId = max(0, (int)($query['category_id'] ?? $query['categories'] ?? 0));
        $search = mb_strtolower(trim((string)($query['search'] ?? '')));

        $items = array_values(array_filter($this->posts->all($locale), function (array $post) use ($categoryId, $search): bool {
            if ($categoryId > 0 && !in_array($categoryId, $post['category_ids'] ?? [], true)) return false;
            if ($search === '') return true;
            $haystack = mb_strtolower(implode(' ', [
                (string)($post['title'] ?? ''),
                (string)($post['summary'] ?? ''),
                (string)($post['description'] ?? ''),
            ]));
            return str_contains($haystack, $search);
        }));

        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);
        return array_map(fn(array $post) => $this->wordpressCompatiblePost(
            $this->posts->find((int)$post['id'], $locale)
        ), $slice);
    }

    public function categories(string $locale): array
    {
        $locale = in_array($locale, ['fa', 'en'], true) ? $locale : 'fa';
        $rows = DB::table('categories')
            ->where('`group`', 'posts')
            ->whereNull('deleted_at')
            ->orderBy('category_id', 'ASC')
            ->get();
        $posts = $this->posts->all($locale);

        return array_map(function (array $row) use ($locale, $posts): array {
            $id = (int)$row['category_id'];
            $translation = DB::table('translations')
                ->where('table_name', 'categories')
                ->where('table_id', $id)
                ->where('field', 'title')
                ->where('locale', $locale)
                ->whereNull('deleted_at')
                ->first();
            if (!$translation && $locale !== 'fa') {
                $translation = DB::table('translations')
                    ->where('table_name', 'categories')
                    ->where('table_id', $id)
                    ->where('field', 'title')
                    ->where('locale', 'fa')
                    ->whereNull('deleted_at')
                    ->first();
            }
            $count = count(array_filter($posts, fn(array $post) => in_array($id, $post['category_ids'] ?? [], true)));
            return [
                'id' => $id,
                'name' => (string)($translation['value'] ?? $row['name'] ?? ''),
                'slug' => (string)($row['slug'] ?? ''),
                'count' => $count,
            ];
        }, $rows);
    }

    public function related(int $postId, array $query, string $locale): array
    {
        $this->posts->find($postId, $locale);
        $limit = min($this->positiveInt($query['per_page'] ?? 2, 2), 10);
        $categoryId = max(0, (int)($query['category_id'] ?? $query['categories'] ?? 0));
        $items = array_filter($this->posts->all($locale), function (array $post) use ($postId, $categoryId): bool {
            if ((int)$post['id'] === $postId) return false;
            return $categoryId < 1 || in_array($categoryId, $post['category_ids'] ?? [], true);
        });
        return array_map(
            fn(array $post) => $this->wordpressCompatiblePost($this->posts->find((int)$post['id'], $locale)),
            array_slice(array_values($items), 0, $limit)
        );
    }

    public function comments(int $postId, array $query, string $locale): array
    {
        $this->posts->find($postId, $locale);
        $page = $this->positiveInt($query['page'] ?? 1, 1);
        $perPage = min($this->positiveInt($query['per_page'] ?? 10, 10), 50);
        $items = $this->comments->forPost($postId, $locale);
        $items = array_slice($items, ($page - 1) * $perPage, $perPage);

        return array_map(fn(array $comment): array => [
            'id' => (int)$comment['id'],
            'post' => $postId,
            'parent' => (int)($comment['parent'] ?? 0),
            'author_name' => (string)($comment['author'] ?? ''),
            'author_avatar_urls' => ['96' => ''],
            'date' => $this->isoDate($comment['created_at'] ?? null),
            'content' => ['rendered' => (string)($comment['content'] ?? '')],
        ], $items);
    }

    public function storeComment(int $postId, array $payload, string $locale): int
    {
        $this->posts->find($postId, $locale);
        $content = trim(strip_tags((string)($payload['content'] ?? '')));
        $author = trim(strip_tags((string)($payload['author_name'] ?? $payload['author'] ?? '')));
        if ($content === '' || mb_strlen($content) > 3000) {
            throw new \RuntimeException('متن نظر باید بین ۱ تا ۳۰۰۰ نویسه باشد.');
        }
        if (mb_strlen($author) > 80) throw new \RuntimeException('نام نویسنده بیش از حد طولانی است.');

        return $this->comments->store($postId, [
            'content' => $content,
            'author' => $author,
            'parent' => max(0, (int)($payload['parent'] ?? 0)),
        ], auth()->check() ? (int)auth()->id() : null, $locale);
    }

    private function wordpressCompatiblePost(array $post): array
    {
        $image = $this->absoluteUrl((string)($post['cover'] ?: $post['thumbnail'] ?? ''));
        return [
            'id' => (int)$post['id'],
            'date' => $this->isoDate($post['published_at'] ?? null),
            'modified' => $this->isoDate($post['updated_at'] ?? null),
            'slug' => (string)($post['slug'] ?? ''),
            'status' => 'publish',
            'type' => 'post',
            'title' => ['rendered' => (string)($post['title'] ?? '')],
            'content' => ['rendered' => (string)($post['content'] ?? '')],
            'excerpt' => ['rendered' => (string)($post['summary'] ?: $post['description'] ?? '')],
            'featured_media' => $image === '' ? 0 : (int)$post['id'],
            'categories' => array_values(array_map('intval', $post['category_ids'] ?? [])),
            '_embedded' => [
                'author' => [['id' => 0, 'name' => (string)($post['author_name'] ?? '')]],
                'wp:featuredmedia' => $image === '' ? [] : [['source_url' => $image]],
            ],
        ];
    }

    private function positiveInt(mixed $value, int $default): int
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        return $value !== false && $value > 0 ? $value : $default;
    }

    private function isoDate(mixed $value): string
    {
        if (!$value) return '';
        $timestamp = strtotime((string)$value);
        return $timestamp === false ? (string)$value : date('c', $timestamp);
    }

    private function absoluteUrl(string $path): string
    {
        if ($path === '' || preg_match('~^https?://~i', $path)) return $path;
        $base = rtrim((string)env('APP_URL', ''), '/');
        if ($base === '') {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'sornaz.com');
        }
        return $base . '/' . ltrim($path, '/');
    }
}
