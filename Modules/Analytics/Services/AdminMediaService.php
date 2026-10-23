<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

class AdminMediaService
{
    private const MIME = [
        'image/jpeg' => ['jpg', 'image'], 'image/png' => ['png', 'image'], 'image/gif' => ['gif', 'image'], 'image/webp' => ['webp', 'image'],
        'video/mp4' => ['mp4', 'video'], 'video/webm' => ['webm', 'video'], 'video/quicktime' => ['mov', 'video'],
        'audio/mpeg' => ['mp3', 'audio'], 'audio/wav' => ['wav', 'audio'], 'audio/ogg' => ['ogg', 'audio'],
        'application/pdf' => ['pdf', 'document'], 'text/plain' => ['txt', 'document'],
        'application/zip' => ['zip', 'archive'], 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx', 'document'],
    ];

    public function index(array $filters): array
    {
        SiteAdminAccess::requireCurrentUser();
        $page = max(1, (int) ($filters['page'] ?? 1));
        $allowed = [10, 20, 30, 50, 100];
        $per = in_array((int) ($filters['perPage'] ?? 20), $allowed, true) ? (int) ($filters['perPage'] ?? 20) : 20;
        [$postScope, $scopeBindings] = $this->postImageScope();
        $scope = "m.deleted_at IS NULL AND m.type='image' AND (m.fileable_type IS NULL OR m.fileable_type IN ('posts','post')) AND (m.collection IS NULL OR m.collection IN ('posts','post-cover','post-content')) AND ($postScope)";
        $where = [$scope];
        $bind = $scopeBindings;
        if (!empty($filters['type'])) {
            $where[] = 'm.type=?';
            $bind[] = $filters['type'];
        }
        if (!empty($filters['month'])) {
            $where[] = "DATE_FORMAT(m.created_at,'%Y-%m')=?";
            $bind[] = $filters['month'];
        }
        if (!empty($filters['search'])) {
            $q = '%' . trim((string) $filters['search']) . '%';
            $where[] = "(m.original_filename LIKE ? OR m.filename LIKE ? OR EXISTS(SELECT 1 FROM translations t WHERE t.table_name='media_files' AND t.table_id=m.media_file_id AND t.deleted_at IS NULL AND t.value LIKE ?))";
            array_push($bind, $q, $q, $q);
        }
        $w = implode(' AND ', $where);
        $total = (int) $this->query("SELECT COUNT(*) FROM media_files m WHERE $w", $bind, true);
        $offset = ($page - 1) * $per;
        $rows = $this->query("SELECT m.*,u.username FROM media_files m LEFT JOIN users u ON u.user_id=m.user_id WHERE $w ORDER BY m.media_file_id DESC LIMIT $offset,$per", $bind);
        $months = $this->query("SELECT DATE_FORMAT(m.created_at,'%Y-%m') value,COUNT(*) total FROM media_files m WHERE $scope GROUP BY value ORDER BY value DESC", $scopeBindings);
        return ['items' => array_map(fn ($r) => $this->map($r), $rows), 'total' => $total, 'page' => $page, 'perPage' => $per, 'months' => $months];
    }

    private function postImageScope(): array
    {
        $ids = $paths = [];
        $siteHost = parse_url((string) (function_exists('env') ? env('APP_URL', '') : ''), PHP_URL_HOST) ?: ($_SERVER['HTTP_HOST'] ?? '');
        $addPath = static function (string $url) use (&$paths, $siteHost): void {
            $url = trim($url);
            if ($url === '' || preg_match('~^(?:data|blob|javascript):~i', $url)) return;
            $host = parse_url($url, PHP_URL_HOST);
            if ($host) $paths[$url] = true;
            if ($host && strcasecmp($host, explode(':', $siteHost)[0]) !== 0) {
                $paths[$url] = true;
                return;
            }
            $path = parse_url($url, PHP_URL_PATH);
            if (is_string($path) && $path !== '') $paths[ltrim(rawurldecode($path), '/')] = true;
        };
        foreach ($this->query("SELECT post_id,cover,cover_media_id FROM posts WHERE deleted_at IS NULL AND type <> 'page'") as $post) {
            if (!empty($post['cover_media_id'])) $ids[(int) $post['cover_media_id']] = true;
            $addPath((string) ($post['cover'] ?? ''));
        }
        $contents = $this->query("SELECT t.value FROM translations t JOIN posts p ON p.post_id=t.table_id WHERE t.table_name='posts' AND t.field='content' AND t.deleted_at IS NULL AND p.deleted_at IS NULL AND p.type <> 'page' AND t.translation_id=(SELECT MAX(tx.translation_id) FROM translations tx WHERE tx.table_name=t.table_name AND tx.table_id=t.table_id AND tx.field=t.field AND tx.locale=t.locale AND tx.deleted_at IS NULL)");
        foreach ($this->contentImageUrls($contents) as $url) $addPath($url);
        $clauses = $bindings = [];
        foreach (['m.media_file_id' => array_keys($ids), 'm.path' => array_keys($paths)] as $column => $values) {
            if (!$values) continue;
            $clauses[] = $column . ' IN (' . implode(',', array_fill(0, count($values), '?')) . ')';
            array_push($bindings, ...$values);
        }
        return [implode(' OR ', $clauses) ?: '1=0', $bindings];
    }

    private function contentImageUrls(array $contents): array
    {
        $urls = [];
        $previous = libxml_use_internal_errors(true);
        try {
            foreach ($contents as $content) {
                $dom = new \DOMDocument();
                if (!$dom->loadHTML('<?xml encoding="UTF-8">' . (string) $content['value'], LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) continue;
                foreach ($dom->getElementsByTagName('img') as $image) $urls[] = $image->getAttribute('src');
                foreach (['img', 'source'] as $tag) foreach ($dom->getElementsByTagName($tag) as $image) {
                    foreach (explode(',', $image->getAttribute('srcset')) as $candidate) {
                        $urls[] = preg_split('/\s+/', trim($candidate))[0] ?? '';
                    }
                }
            }
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        return $urls;
    }

    public function upload(int $actor, array $file, array $data): int
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $mime = $this->validatedMime($file);
        $tmp = (string) $file['tmp_name'];
        [$ext,$type] = self::MIME[$mime];
        $directory = 'assets/media/library/' . date('Y/m');
        $absolute = base_path($directory);
        if (!is_dir($absolute) && !mkdir($absolute, 0775, true) && !is_dir($absolute)) {
            throw new RuntimeException('پوشه رسانه قابل ایجاد نیست.');
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $relative = $directory . '/' . $filename;
        $target = base_path($relative);
        if (!move_uploaded_file($tmp, $target)) {
            throw new RuntimeException('ذخیره فایل روی سرور ناموفق بود.');
        }
        $now = date('Y-m-d H:i:s');
        $width = $height = null;
        if ($type === 'image' && ($size = @getimagesize($target))) {
            $width = $size[0];
            $height = $size[1];
        }
        try {
            $id = (int) DB::table('media_files')->insertGetId(['user_id' => $actor, 'disk' => 'public', 'directory' => $directory, 'filename' => $filename, 'extension' => $ext, 'mime_type' => $mime, 'type' => $type, 'collection' => null, 'path' => $relative, 'thumbnail_path' => null, 'original_filename' => (string) ($file['name'] ?? $filename), 'fileable_type' => null, 'fileable_id' => null, 'sort_order' => 0, 'size' => (int) filesize($target), 'duration' => null, 'width' => $width, 'height' => $height, 'checksum' => hash_file('sha256', $target), 'visibility' => 'public', 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            if (!$id) {
                throw new RuntimeException('ثبت رسانه در دیتابیس ناموفق بود.');
            }
            $title = trim((string) ($data['title'] ?? '')) ?: pathinfo((string) ($file['name'] ?? $filename), PATHINFO_FILENAME);
            $this->saveTranslations($id, $actor, ['title' => $title, 'alt' => $data['alt'] ?? '', 'caption' => $data['caption'] ?? '', 'description' => $data['description'] ?? '']);
            return $id;
        } catch (\Throwable$e) {
            @unlink($target);
            throw $e;
        }
    }

    public function uploadPostCover(int $actor, array $file): array
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->validatedMime($file, true);
        $id = $this->upload($actor, $file, []);
        return $this->uploadedMedia($id);
    }

    public function uploadedMedia(int $id): array
    {
        SiteAdminAccess::requireCurrentUser();
        $row = $this->find($id);
        return ['id' => $id, 'url' => '/' . ltrim((string) $row['path'], '/')];
    }

    private function validatedMime(array $file, bool $postCover = false): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('بارگذاری فایل کامل نشد.');
        }
        if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > 50 * 1024 * 1024) {
            throw new RuntimeException('حجم فایل باید کمتر از ۵۰ مگابایت باشد.');
        }
        $tmp = (string) $file['tmp_name'];
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!isset(self::MIME[$mime])) {
            throw new RuntimeException('نوع این فایل مجاز نیست.');
        }
        if ($postCover) {
            $allowed = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'webp' => 'image/webp'];
            $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
            $image = @getimagesize($tmp);
            if (($allowed[$extension] ?? null) !== $mime || !$image || ($image['mime'] ?? '') !== $mime) {
                throw new RuntimeException(locale() === 'en' ? 'Select a valid PNG, JPG or WebP image.' : 'یک تصویر معتبر PNG، JPG یا WebP انتخاب کنید.');
            }
        }
        return $mime;
    }

    public function update(int $actor, int $id, array $data): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $row = $this->find($id);
        $visibility = in_array($data['visibility'] ?? '', ['public', 'private', 'academy_only'], true) ? $data['visibility'] : $row['visibility'];
        DB::table('media_files')->where('media_file_id', $id)->update(['visibility' => $visibility, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $actor]);
        $this->saveTranslations($id, $actor, $data);
    }

    public function delete(int $actor, int $id): void
    {
        SiteAdminAccess::requireCurrentUser($actor);
        $this->find($id);
        $now = date('Y-m-d H:i:s');
        DB::table('media_files')->where('media_file_id', $id)->update(['deleted_at' => $now, 'deleted_by' => $actor]);
        DB::table('translations')->where('table_name', 'media_files')->where('table_id', $id)->whereNull('deleted_at')->update(['deleted_at' => $now, 'deleted_by' => $actor]);
    }

    private function find(int $id): array
    {
        $r = DB::table('media_files')->where('media_file_id', $id)->whereNull('deleted_at')->first();
        if (!$r) {
            throw new RuntimeException('رسانه یافت نشد.');
        }
        return $r;
    }

    private function saveTranslations(int $id, int $actor, array $data): void
    {
        foreach (['title', 'alt', 'caption', 'description'] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            $value = trim((string) $data[$field]);
            $row = DB::table('translations')->where('table_name', 'media_files')->where('table_id', $id)->where('locale', 'fa')->where('field', $field)->first();
            $now = date('Y-m-d H:i:s');
            $values = ['value' => $value, 'version' => 1, 'updated_at' => $now, 'updated_by' => $actor, 'deleted_at' => null, 'deleted_by' => null];
            if ($row) {
                DB::table('translations')->where('translation_id', (int) $row['translation_id'])->update($values);
            } else {
                DB::table('translations')->insert(['table_name' => 'media_files', 'table_id' => $id, 'locale' => 'fa', 'field' => $field, 'created_at' => $now, 'created_by' => $actor] + $values);
            }
        }
    }

    private function map(array $r): array
    {
        $tr = [];
        foreach (DB::table('translations')->where('table_name', 'media_files')->where('table_id', (int) $r['media_file_id'])->where('locale', 'fa')->whereNull('deleted_at')->get() as $t) {
            $tr[$t['field']] = $t['value'];
        }
        $path = (string) $r['path'];
        $url = preg_match('~^https?://~', $path) ? $path : '/' . ltrim($path, '/');
        return ['id' => (int) $r['media_file_id'], 'title' => $tr['title'] ?? pathinfo((string) $r['original_filename'], PATHINFO_FILENAME), 'alt' => $tr['alt'] ?? '', 'caption' => $tr['caption'] ?? '', 'description' => $tr['description'] ?? '', 'url' => $url, 'filename' => $r['original_filename'] ?: $r['filename'], 'type' => $r['type'], 'mime' => $r['mime_type'], 'size' => (int) $r['size'], 'width' => $r['width'] ? (int) $r['width'] : null, 'height' => $r['height'] ? (int) $r['height'] : null, 'visibility' => $r['visibility'], 'author' => $r['username'] ?? '', 'createdAt' => $r['created_at']];
    }

    private function query(string $sql, array $bind = [], bool $scalar = false): mixed
    {
        $s = db()->prepare($sql);
        $s->execute($bind);
        return $scalar ? $s->fetchColumn() : $s->fetchAll();
    }
}
