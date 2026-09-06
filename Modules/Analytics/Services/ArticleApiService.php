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

        $ids = isset($query['ids']) ? array_map('intval', explode(',', (string)$query['ids'])) : null;
        $items = array_values(array_filter($this->available($locale), function (array $post) use ($categoryId, $search, $ids): bool {
            if ($ids !== null && !in_array((int)$post['id'], $ids, true)) return false;
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
            $this->localizedPost((int)$post['id'], $locale)
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
        $posts = $this->available($locale);

        return array_values(array_filter(array_map(function (array $row) use ($locale, $posts): ?array {
            $id = (int)$row['category_id'];
            $translation = DB::table('translations')
                ->where('table_name', 'categories')
                ->where('table_id', $id)
                ->where('field', 'title')
                ->where('locale', $locale)
                ->whereNull('deleted_at')
                ->first();
            if (!$translation && $locale === 'en') return null;
            $count = count(array_filter($posts, fn(array $post) => in_array($id, $post['category_ids'] ?? [], true)));
            return [
                'id' => $id,
                'name' => (string)($translation['value'] ?? $row['name'] ?? ''),
                'slug' => (string)($row['slug'] ?? ''),
                'count' => $count,
            ];
        }, $rows)));
    }

    public function related(int $postId, array $query, string $locale): array
    {
        $this->localizedPost($postId, $locale);
        $limit = min($this->positiveInt($query['per_page'] ?? 2, 2), 10);
        $categoryId = max(0, (int)($query['category_id'] ?? $query['categories'] ?? 0));
        $items = array_filter($this->available($locale), function (array $post) use ($postId, $categoryId): bool {
            if ((int)$post['id'] === $postId) return false;
            return $categoryId < 1 || in_array($categoryId, $post['category_ids'] ?? [], true);
        });
        return array_map(
            fn(array $post) => $this->wordpressCompatiblePost($this->posts->find((int)$post['id'], $locale)),
            array_slice(array_values($items), 0, $limit)
        );
    }

    public function comments(int $postId, array $query, string $locale, ?int $viewerId = null): array
    {
        $this->posts->find($postId, $locale);
        $page = $this->positiveInt($query['page'] ?? 1, 1);
        $perPage = min($this->positiveInt($query['per_page'] ?? 10, 10), 50);
        $pending = [];
        foreach (explode(',', (string)($query['receipts'] ?? '')) as $receipt) {
            $parts = explode('.', $receipt, 2);
            if (count($parts) === 2 && ctype_digit($parts[0]) && $this->validReceipt($postId, (int)$parts[0], $parts[1])) $pending[] = (int)$parts[0];
        }
        $items = $this->comments->forPost($postId, $locale, $viewerId, $pending);
        usort($items, fn($a,$b) => ($a['status']==='pending'?0:1) <=> ($b['status']==='pending'?0:1));
        $items = array_slice($items, ($page - 1) * $perPage, $perPage);

        return array_map(fn(array $comment): array => [
            'id' => (int)$comment['id'],
            'post' => $postId,
            'parent' => (int)($comment['parent'] ?? 0),
            'depth' => (int)($comment['depth'] ?? 0),
            'status' => $comment['status'],
            'locale' => $locale,
            'rating' => $comment['status'] === 'approved' ? (new PublicRatingService())->summary('comment', (int)$comment['id'], $viewerId) : null,
            'author_name' => (string)($comment['author'] ?? ''),
            'author_avatar_urls' => ['96' => ''],
            'date' => $this->isoDate($comment['created_at'] ?? null),
            'content' => ['rendered' => (string)($comment['content'] ?? '')],
        ], $items);
    }

    public function storeComment(int $postId, array $payload, string $locale, ?int $viewerId = null): int
    {
        $this->posts->find($postId, $locale);
        $content = $this->safeCommentHtml((string)($payload['content'] ?? ''));
        $plain = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $author = trim(strip_tags((string)($payload['author_name'] ?? $payload['author'] ?? '')));
        if ($plain === '' || mb_strlen($plain) > 3000 || strlen($content) > 20000) {
            throw new \RuntimeException('متن نظر باید بین ۱ تا ۳۰۰۰ نویسه باشد.');
        }
        if (mb_strlen($author) > 80) throw new \RuntimeException('نام نویسنده بیش از حد طولانی است.');

        return $this->comments->store($postId, [
            'content' => $content,
            'author' => $author,
            'author_email' => (string)($payload['author_email'] ?? ''),
            'parent' => max(0, (int)($payload['parent'] ?? 0)),
        ], $viewerId, $locale);
    }

    public function show(int $id, string $locale): array
    {
        return $this->wordpressCompatiblePost($this->localizedPost($id, $locale));
    }

    public function manifest(string $locale): array
    {
        $items = [];
        foreach ($this->available($locale) as $row) {
            $post = $this->show((int)$row['id'], $locale);
            $items[] = ['id'=>$post['id'], 'modified'=>$post['modified'], 'revision'=>$post['revision']];
        }
        return ['items'=>$items, 'categories'=>$this->categories($locale), 'locale'=>$locale];
    }

    public function prepareReceipts(): void
    {
        try { db()->query('SELECT comment_id FROM article_comment_receipts LIMIT 0'); }
        catch (\PDOException $e) {
            if (($e->errorInfo[0] ?? '') !== '42S02') throw $e;
            db()->exec('CREATE TABLE IF NOT EXISTS article_comment_receipts (comment_id BIGINT UNSIGNED PRIMARY KEY, post_id BIGINT UNSIGNED NOT NULL, token_hash CHAR(64) NOT NULL, created_at DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        }
    }

    public function receipt(int $postId, int $commentId): string
    {
        $this->prepareReceipts();
        $secret = bin2hex(random_bytes(32));
        DB::table('article_comment_receipts')->insert(['comment_id'=>$commentId,'post_id'=>$postId,'token_hash'=>hash('sha256',$secret),'created_at'=>date('Y-m-d H:i:s')]);
        return $commentId.'.'.$secret;
    }

    private function validReceipt(int $postId, int $commentId, string $secret): bool
    {
        if (!preg_match('/^[a-f0-9]{64}$/D', $secret)) return false;
        $this->prepareReceipts();
        $row=DB::table('article_comment_receipts')->where('comment_id',$commentId)->where('post_id',$postId)->first();
        return $row && hash_equals((string)$row['token_hash'],hash('sha256',$secret));
    }

    private function available(string $locale): array
    {
        $posts = $this->posts->all($locale);
        if ($locale !== 'en') return $posts;
        $translations = DB::table('translations')->where('table_name','posts')->where('field','title')->where('locale','en')->whereNull('deleted_at')->get();
        $ids = array_column(array_filter($translations, fn($t)=>trim((string)$t['value'])!==''), 'table_id');
        return array_values(array_filter($posts, fn($p)=>in_array($p['id'], $ids)));
    }

    private function localizedPost(int $id, string $locale): array
    {
        $post = $this->posts->find($id, $locale);
        $post['locale'] = $locale;
        if ($locale !== 'en') return $post;
        $texts = [];
        foreach (DB::table('translations')->where('table_name','posts')->where('table_id',$id)->where('locale','en')->whereNull('deleted_at')->get() as $row) $texts[$row['field']] = (string)$row['value'];
        if (trim($texts['title'] ?? '') === '') throw new \RuntimeException('مقاله یافت نشد.', 404);
        foreach (['title'=>'title','summary'=>'brief','description'=>'description','content'=>'content'] as $key=>$field) $post[$key] = $texts[$field] ?? '';
        $post['categories'] = [];
        foreach ($post['category_ids'] as $categoryId) {
            $translation = DB::table('translations')->where('table_name','categories')->where('table_id',$categoryId)->where('field','title')->where('locale','en')->whereNull('deleted_at')->first();
            if ($translation) $post['categories'][] = (string)$translation['value'];
        }
        $row = DB::table('posts')->where('post_id',$id)->first();
        $author = DB::table('users')->where('user_id',(int)($row['author_id']??0))->first();
        $name = DB::table('translations')->where('table_name','users')->where('table_id',(int)($row['author_id']??0))->where('field','full_name')->where('locale','en')->whereNull('deleted_at')->first();
        $post['author_name'] = (string)($name['value'] ?? $author['username'] ?? '');
        return $post;
    }

    private function safeCommentHtml(string $html): string
    {
        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors(); libxml_use_internal_errors($previous);
        $render = function($node) use (&$render): string {
            if ($node instanceof \DOMText) return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            if (!($node instanceof \DOMElement)) return '';
            if (in_array(strtolower($node->tagName), ['script','style','iframe','object'], true)) return '';
            $children = ''; foreach ($node->childNodes as $child) $children .= $render($child);
            if ($node->tagName === 'br') return '<br>';
            if ($node->tagName === 'p') return '<p>'.$children.'</p>';
            if ($node->tagName === 'a') {
                $url = trim($node->getAttribute('href'));
                if (preg_match('~^https?://~i', $url)) return '<a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" rel="nofollow noopener">'.$children.'</a>';
            }
            return $children;
        };
        $out=''; foreach ($doc->childNodes as $node) $out.=$render($node); return trim($out);
    }

    private function wordpressCompatiblePost(array $post): array
    {
        $image = $this->absoluteUrl((string)($post['cover'] ?: $post['thumbnail'] ?? ''));
        return [
            'id' => (int)$post['id'],
            'date' => $this->isoDate($post['published_at'] ?? null),
            'modified' => $this->isoDate($post['updated_at'] ?? null),
            'slug' => (string)($post['slug'] ?? ''),
            'locale' => (string)($post['locale'] ?? 'fa'),
            'category_names' => $post['categories'] ?? [],
            'views' => (int)($post['views'] ?? 0),
            'revision' => hash('sha256', json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
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
