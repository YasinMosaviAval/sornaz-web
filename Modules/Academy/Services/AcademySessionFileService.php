<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Core\http\DownloadResponse;
use Modules\Analytics\Services\ChatAttachmentPolicy;
use Modules\System\Services\PaymentMutex;
use RuntimeException;

final class AcademySessionFileService
{
    private const TYPE = 'academy_branch_course_term_sessions';
    private const COLLECTION = 'term-session';

    public function __construct(private SessionFileAccess $access = new SessionFileAccess(), private SessionFileStorage $storage = new SessionFileStorage()) {}

    public function index(int $actor, int $sessionId): array
    {
        $context = $this->access->authorize($actor, $sessionId);
        $rows = $this->files($sessionId)->orderBy('media_file_id', 'DESC')->get();
        return ['sessionId' => $sessionId, 'files' => array_map(fn ($row) => $this->serialize($row, $context, $actor), $rows), 'canUpload' => $context['canUpload'], 'canDownload' => true, 'maxBytes' => $this->maxBytes()];
    }

    public function upload(int $actor, int $sessionId, array $file): array
    {
        $this->access->authorize($actor, $sessionId, true);
        $info = $this->inspect($file);
        $checksum = hash_file('sha256', $file['tmp_name']);
        $written = null;
        try {
            return PaymentMutex::run(db(), 'session:' . $sessionId, function () use ($actor, $sessionId, $file, $info, $checksum, &$written) {
                return transaction(function () use ($actor, $sessionId, $file, $info, $checksum, &$written) {
                    $context = $this->access->authorize($actor, $sessionId, true);
                    $existing = $this->files($sessionId)->where('user_id', $actor)->where('checksum', $checksum)->first();
                    if ($existing) {
                        $this->storage->file($existing['path'], $sessionId);
                        return ['file' => $this->serialize($existing, $context, $actor), 'duplicate' => true];
                    }
                    return $this->persist($actor, $sessionId, $file, $info, $checksum, $context, $written);
                });
            });
        } catch (\Throwable $error) {
            if ($written !== null) @unlink($written);
            throw $error;
        }
    }

    private function persist(int $actor, int $sessionId, array $file, array $info, string $checksum, array $context, ?string &$written): array
    {
        $stored = $this->storage->store($sessionId, $file, $info);
        $written = $stored['absolute'];
        try {
            $now = date('Y-m-d H:i:s');
            $id = DB::table('media_files')->insertGetId(['user_id' => $actor, 'disk' => 'private', 'directory' => (string) $sessionId, 'filename' => $stored['filename'], 'extension' => $info['extension'], 'mime_type' => $info['mime'], 'type' => $this->fileType($info['mime']), 'collection' => self::COLLECTION, 'path' => $stored['relative'], 'original_filename' => $info['name'], 'fileable_type' => self::TYPE, 'fileable_id' => $sessionId, 'sort_order' => 0, 'size' => $info['size'], 'checksum' => $checksum, 'visibility' => 'private', 'created_at' => $now, 'created_by' => $actor, 'updated_at' => $now, 'updated_by' => $actor]);
            $row = $this->files($sessionId)->where('media_file_id', $id)->first();
            if (!$row) throw new RuntimeException('ذخیره اطلاعات فایل انجام نشد.', 503);
            return ['file' => $this->serialize($row, $context, $actor), 'duplicate' => false];
        } catch (\Throwable $error) {
            @unlink($stored['absolute']);
            throw $error;
        }
    }

    public function download(int $actor, int $sessionId, int $fileId): DownloadResponse
    {
        $this->access->authorize($actor, $sessionId);
        $row = $this->find($sessionId, $fileId);
        $path = $this->storage->file((string) $row['path'], $sessionId);
        return new DownloadResponse($path, (string) $row['original_filename'], (string) $row['mime_type']);
    }

    public function delete(int $actor, int $sessionId, int $fileId): void
    {
        $this->access->authorize($actor, $sessionId, true);
        PaymentMutex::run(db(), 'session:' . $sessionId, fn () => transaction(function () use ($actor, $sessionId, $fileId) {
            $context = $this->access->authorize($actor, $sessionId, true);
            $row = $this->find($sessionId, $fileId);
            if (!$context['canManage'] && (int) $row['user_id'] !== $actor) throw new RuntimeException('حذف این فایل برای شما مجاز نیست.', 403);
            DB::table('media_files')->where('media_file_id', $fileId)->update(['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $actor, 'updated_by' => $actor]);
        }));
    }

    private function files(int $sessionId): \Core\database\Builder
    {
        return DB::table('media_files')->where('fileable_type', self::TYPE)->where('fileable_id', $sessionId)->where('collection', self::COLLECTION)->where('disk', 'private')->where('visibility', 'private')->whereNull('deleted_at');
    }

    private function find(int $sessionId, int $fileId): array
    {
        $row = $fileId > 0 ? $this->files($sessionId)->where('media_file_id', $fileId)->first() : null;
        if (!$row) throw new RuntimeException('فایل در دسترس نیست.', 404);
        return $row;
    }

    private function serialize(array $row, array $context, int $actor): array
    {
        $id = (int) $row['media_file_id']; $session = (int) $context['session_id'];
        return ['id' => $id, 'name' => (string) $row['original_filename'], 'size' => (int) $row['size'], 'mime' => (string) $row['mime_type'], 'createdAt' => (string) ($row['created_at'] ?? ''), 'canDelete' => $context['canManage'] || ($context['canUpload'] && (int) $row['user_id'] === $actor), 'downloadUrl' => '/academy/sessions/' . $session . '/files/' . $id . '/download'];
    }

    private function inspect(array $file): array
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($error === UPLOAD_ERR_NO_FILE && $this->postTooLarge())) throw new RuntimeException('حجم فایل از حد مجاز سرور بیشتر است.', 413);
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) throw new RuntimeException('فایل بارگذاری‌شده معتبر نیست. دوباره تلاش کنید.', 422);
        $info = ChatAttachmentPolicy::inspect($file['tmp_name'], (string) ($file['name'] ?? ''));
        if (!mb_check_encoding($info['name'], 'UTF-8')) throw new RuntimeException('نام فایل معتبر نیست.', 422);
        $info['name'] = mb_substr($info['name'], 0, 200);
        return $info;
    }

    public function maxBytes(): int
    {
        $limits = [100 * 1024 * 1024, $this->iniBytes('upload_max_filesize')];
        $post = $this->iniBytes('post_max_size');
        if ($post) $limits[] = max(1, $post - 65536);
        return min(array_filter($limits, static fn ($value) => $value > 0));
    }

    private function postTooLarge(): bool
    {
        $post = $this->iniBytes('post_max_size');
        return $post > 0 && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $post;
    }

    private function iniBytes(string $name): int
    {
        $value = trim((string) ini_get($name));
        $number = (float) $value;
        return (int) ($number * match (strtolower(substr($value, -1))) { 'g' => 1073741824, 'm' => 1048576, 'k' => 1024, default => 1 });
    }

    private function fileType(string $mime): string
    {
        foreach (['image', 'audio', 'video'] as $type) if (str_starts_with($mime, $type . '/')) return $type;
        return str_contains($mime, 'zip') || str_contains($mime, '7z') ? 'archive' : 'document';
    }
}
