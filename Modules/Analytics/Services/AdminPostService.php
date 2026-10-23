<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Core\security\ContentSafety;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminPostService
{
    private const TYPES = ['post', 'product', 'music_theory', 'page'];
    private const STATUSES = ['draft', 'published', 'private', 'inherit', 'pending', 'trash', 'auto-draft', 'future', 'request-pending', 'request-confirmed'];
    private const VISIBILITIES = ['public', 'private', 'followers', 'premium'];
    private const TEXT_FIELDS = ['title', 'brief', 'description', 'content'];

    public function index(array $filters): array
    {
        SiteAdminAccess::requireCurrentUser();
        if (($filters['editorOptions'] ?? '') === '1') {
            return ['posts' => $this->query("SELECT p.post_id id, COALESCE((SELECT value FROM translations WHERE table_name='posts' AND table_id=p.post_id AND locale='fa' AND field='title' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1), p.slug) title FROM posts p WHERE p.deleted_at IS NULL AND p.type <> 'page' AND p.status <> 'trash' ORDER BY p.post_id DESC")];
        }
        $this->ensurePageType();
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = in_array((int) ($filters['perPage'] ?? 20), [10, 20, 30, 50, 100], true) ? (int) $filters['perPage'] : 20;
        $where = ['p.deleted_at IS NULL'];
        $bindings = [];
        foreach (['status', 'type', 'visibility'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "p.$field = ?";
                $bindings[] = $filters[$field];
            }
        }
        if (!empty($filters['excludeType'])) {
            $where[] = 'p.type <> ?';
            $bindings[] = $filters['excludeType'];
        }
        if (!empty($filters['search'])) {
            $term = '%' . trim((string) $filters['search']) . '%';
            $where[] = "(p.slug LIKE ? OR p.categories LIKE ? OR EXISTS (SELECT 1 FROM translations tx WHERE tx.table_name='posts' AND tx.table_id=p.post_id AND tx.locale='fa' AND tx.field IN ('title','brief','description','content') AND tx.deleted_at IS NULL AND tx.value LIKE ?))";
            array_push($bindings, $term, $term, $term);
        }
        $whereSql = implode(' AND ', $where);
        $count = $this->query("SELECT COUNT(*) FROM posts p WHERE $whereSql", $bindings, true);
        $offset = ($page - 1) * $perPage;
        $rows = $this->query("SELECT p.*,u.username,
            COALESCE((SELECT value FROM translations WHERE table_name='users' AND table_id=p.author_id AND locale='fa' AND field='full_name' AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),u.username,CONCAT('کاربر ',p.author_id)) author_name
            FROM posts p LEFT JOIN users u ON u.user_id=p.author_id
            WHERE $whereSql ORDER BY p.post_id DESC LIMIT $offset,$perPage", $bindings);
        $posts = array_map(fn (array $row) => $this->map($row), $rows);
        $posts = $this->withCategoryNames($posts);
        $counts = $this->statusCounts($filters);
        return ['posts' => $posts, 'total' => (int) $count, 'page' => $page, 'perPage' => $perPage, 'statusCounts' => $counts];
    }

    private function withCategoryNames(array $posts): array
    {
        $categoryIds = [];
        foreach ($posts as $post) {
            foreach (explode(',', (string) $post['categories']) as $value) {
                if (ctype_digit(trim($value))) $categoryIds[] = (int) trim($value);
            }
        }
        $categoryNames = [];
        if ($categoryIds) {
            $ids = array_values(array_unique($categoryIds));
            $locale = locale() === 'en' ? 'en' : 'fa';
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            foreach ($this->query("SELECT c.category_id, COALESCE((SELECT t.value FROM translations t WHERE t.table_name='categories' AND t.table_id=c.category_id AND t.field='title' AND t.locale=? AND t.deleted_at IS NULL ORDER BY t.translation_id DESC LIMIT 1), (SELECT t.value FROM translations t WHERE t.table_name='categories' AND t.table_id=c.category_id AND t.field='title' AND t.locale='fa' AND t.deleted_at IS NULL ORDER BY t.translation_id DESC LIMIT 1), c.name) title FROM categories c WHERE c.deleted_at IS NULL AND c.category_id IN ($placeholders)", [$locale, ...$ids]) as $category) {
                $categoryNames[(int) $category['category_id']] = $category['title'];
            }
        }
        foreach ($posts as &$post) {
            $post['category_names'] = array_values(array_filter(array_map(static fn ($id) => $categoryNames[(int) trim($id)] ?? null, explode(',', (string) $post['categories']))));
        }
        unset($post);
        return $posts;
    }

    private function statusCounts(array $filters): array
    {
        $countWhere = ['deleted_at IS NULL'];
        $countBindings = [];
        if (!empty($filters['type'])) {
            $countWhere[] = 'type=?';
            $countBindings[] = $filters['type'];
        }
        if (!empty($filters['excludeType'])) {
            $countWhere[] = 'type<>?';
            $countBindings[] = $filters['excludeType'];
        }
        $counts = [];
        foreach ($this->query('SELECT status,COUNT(*) total FROM posts WHERE ' . implode(' AND ', $countWhere) . ' GROUP BY status', $countBindings) as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    public function find(int $id): array
    {
        SiteAdminAccess::requireCurrentUser();
        $row = DB::table('posts')->where('post_id', $id)->whereNull('deleted_at')->first();
        if (!$row) {
            throw new RuntimeException('نوشته یافت نشد.');
        }
        return $this->map($row);
    }

    public function create(int $actor, array $data): int
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->ensurePageType();
        return transaction(function () use ($actor, $data) {
            [$values, $texts] = $this->validated($data, $actor);
            $id = (int) DB::table('posts')->insertGetId(['author_id' => $actor, 'created_by' => $actor] + $values);
            $this->setTexts($id, $texts, $actor);
            return $id;
        });
    }

    public function update(int $actor, int $id, array $data): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->ensurePageType();
        $this->find($id);
        transaction(function () use ($actor, $id, $data) {
            [$values, $texts] = $this->validated($data, $actor, $id);
            DB::table('posts')->where('post_id', $id)->update($values);
            $this->setTexts($id, $texts, $actor);
        });
    }

    public function trash(int $actor, int $id): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->find($id);
        DB::table('posts')->where('post_id', $id)->update(['status' => 'trash', 'updated_by' => $actor]);
    }

    public function status(int $actor, int $id, string $status): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        if (!in_array($status, self::STATUSES, true)) throw new RuntimeException('وضعیت نوشته معتبر نیست.');
        $this->find($id);
        $values = ['status' => $status, 'updated_by' => $actor, 'updated_at' => date('Y-m-d H:i:s')];
        if ($status === 'published') {
            $row = DB::table('posts')->where('post_id', $id)->first();
            if (empty($row['published_at'])) $values['published_at'] = date('Y-m-d H:i:s');
        }
        DB::table('posts')->where('post_id', $id)->whereNull('deleted_at')->update($values);
    }

    public function restore(int $actor, int $id): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->find($id);
        DB::table('posts')->where('post_id', $id)->update(['status' => 'draft', 'updated_by' => $actor]);
    }

    public function destroy(int $actor, int $id): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $post = $this->find($id);
        if ($post['status'] !== 'trash') {
            throw new RuntimeException('پیش از حذف دائمی، نوشته را به زباله‌دان منتقل کنید.');
        }
        transaction(function () use ($actor, $id) {
            $deletedAt = date('Y-m-d H:i:s');
            DB::table('translations')->where('table_name', 'posts')->where('table_id', $id)->whereNull('deleted_at')->update([
                'deleted_at' => $deletedAt, 'deleted_by' => $actor, 'updated_by' => $actor,
            ]);
            DB::table('posts')->where('post_id', $id)->whereNull('deleted_at')->update([
                'deleted_at' => $deletedAt, 'deleted_by' => $actor, 'updated_by' => $actor,
            ]);
        });
    }

    private function validated(array $data, int $actor, int $id = 0): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            throw new RuntimeException('عنوان نوشته الزامی است.');
        }
        $type = in_array($data['type'] ?? '', self::TYPES, true) ? $data['type'] : 'post';
        $status = in_array($data['status'] ?? '', self::STATUSES, true) ? $data['status'] : 'draft';
        $visibility = in_array($data['visibility'] ?? '', self::VISIBILITIES, true) ? $data['visibility'] : 'public';
        $slug = trim((string) ($data['slug'] ?? '')) ?: $this->slug($title);
        $duplicate = DB::table('posts')->where('slug', $slug)->whereNull('deleted_at')->first();
        if ($duplicate && (int) $duplicate['post_id'] !== $id) {
            throw new RuntimeException('این نامک قبلاً استفاده شده است.');
        }
        $publishedAt = trim((string) ($data['published_at'] ?? '')) ?: null;
        if ($publishedAt) {
            $publishedAt = str_replace('T', ' ', $publishedAt);
            if (strlen($publishedAt) === 16) {
                $publishedAt .= ':00';
            }
            if (!strtotime($publishedAt)) {
                throw new RuntimeException('تاریخ انتشار معتبر نیست.');
            }
        } elseif ($status === 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }
        $values = [
            'categories' => trim((string) ($data['categories'] ?? '')), 'cover' => trim((string) ($data['cover'] ?? '')),
            'cover_media_id' => ($data['cover_media_id'] ?? null) ?: null, 'slug' => $slug,
            'published_at' => $publishedAt, 'type' => $type, 'status' => $status, 'visibility' => $visibility,
            'visibility_user_id' => ($data['visibility_user_id'] ?? null) ?: null,
            'password' => trim((string) ($data['password'] ?? '')) ?: null,
            'guid' => trim((string) ($data['guid'] ?? '')) ?: null,
            'related_posts_id' => trim((string) ($data['related_posts_id'] ?? '')) ?: null,
            'updated_by' => $actor,
        ];
        return [$values, $this->validatedTexts($data, $title)];
    }

    private function validatedTexts(array $data, string $title): array
    {
        return ['title' => $title, 'brief' => ContentSafety::text($data['summary'] ?? '', 10000), 'description' => ContentSafety::text($data['description'] ?? '', 20000), 'content' => ContentSafety::rich(ContentSafety::text($data['content'] ?? '', 200000))];
    }

    private function map(array $row): array
    {
        $id = (int) $row['post_id'];
        $texts = [];
        foreach (DB::table('translations')->where('table_name', 'posts')->where('table_id', $id)->where('locale', 'fa')->whereIn('field', self::TEXT_FIELDS)->whereNull('deleted_at')->get() as $translation) {
            $texts[$translation['field']] = $translation['value'];
        }
        return [
            'id' => $id, 'title' => $texts['title'] ?? 'بدون عنوان', 'summary' => $texts['brief'] ?? '',
            'description' => $texts['description'] ?? '', 'content' => ContentSafety::rich($texts['content'] ?? ''),
            'author_id' => (int) ($row['author_id'] ?? 0), 'author_name' => $row['author_name'] ?? $row['username'] ?? ('کاربر ' . ($row['author_id'] ?? '')),
            'categories' => $row['categories'] ?? '', 'cover' => $row['cover'] ?? '', 'cover_media_id' => $row['cover_media_id'] ? (int) $row['cover_media_id'] : null,
            'slug' => $row['slug'] ?? '', 'views_count' => (int) ($row['views_count'] ?? 0), 'published_at' => $row['published_at'] ?? null,
            'type' => $row['type'], 'status' => $row['status'], 'visibility' => $row['visibility'], 'visibility_user_id' => $row['visibility_user_id'] ?? null,
            'password' => $row['password'] ?? '', 'comment_count' => (int) ($row['comment_count'] ?? 0), 'guid' => $row['guid'] ?? '',
            'related_posts_id' => $row['related_posts_id'] ?? '', 'created_at' => $row['created_at'] ?? null, 'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    private function setTexts(int $id, array $texts, int $actor): void
    {
        foreach ($texts as $field => $value) {
            $row = DB::table('translations')->where('table_name', 'posts')->where('table_id', $id)->where('locale', 'fa')->where('field', $field)->first();
            $values = ['value' => $value, 'version' => (int) ($row['version'] ?? 0) + 1, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
            if ($row) {
                DB::table('translations')->where('translation_id', (int) $row['translation_id'])->update($values);
            } else {
                DB::table('translations')->insert(['table_name' => 'posts', 'table_id' => $id, 'locale' => 'fa', 'field' => $field, 'created_by' => $actor] + $values);
            }
        }
    }

    private function slug(string $title): string
    {
        $slug = trim(preg_replace('/[^\pL\pN]+/u', '-', mb_strtolower($title)), '-');
        return $slug !== '' ? $slug : 'post-' . time();
    }

    private function query(string $sql, array $bindings = [], bool $scalar = false): mixed
    {
        $statement = db()->prepare($sql);
        $statement->execute($bindings);
        return $scalar ? $statement->fetchColumn() : $statement->fetchAll();
    }

    private function ensurePageType(): void
    {
        $column = $this->query("SHOW COLUMNS FROM posts LIKE 'type'")[0] ?? null;
        $type = strtolower((string) ($column['Type'] ?? ''));
        if (str_starts_with($type, 'enum(') && !str_contains($type, "'page'")) {
            throw new RuntimeException('نوع صفحه در جدول نوشته‌ها نصب نشده است؛ migration دیتابیس را اجرا کنید.');
        }
    }
}
