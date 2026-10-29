<?php
namespace Modules\Analytics\Services;

use RuntimeException;

/** Only the Markdown library is writable; application code and links are excluded. */
final class AdminGuideDocumentService
{
    public const MAX_BYTES = 2097152;

    public function read(int $actor, string $relative): array
    {
        $this->founder($actor);
        $path = $this->path($relative);
        return $this->describe($relative, $this->content($path));
    }

    public function update(int $actor, string $relative, string $content, string $version): array
    {
        $this->founder($actor);
        if (strlen($content) > self::MAX_BYTES || str_contains($content, "\0") || !mb_check_encoding($content, 'UTF-8')) throw new RuntimeException('متن راهنما معتبر نیست یا بیش از ۲ مگابایت است.', 422);
        return $this->locked($relative, function () use ($relative, $content, $version) {
            $path = $this->path($relative);
            $old = $this->content($path);
            $this->version($old, $version);
            $content = str_replace(["\r\n", "\r"], "\n", $content);
            if (str_contains($old, "\r\n")) $content = str_replace("\n", "\r\n", $content);
            $temporary = @tempnam(dirname($path), '.sornaz-guide-');
            if (!$temporary || realpath(dirname($temporary)) !== realpath(dirname($path))) {
                if ($temporary) @unlink($temporary);
                throw new RuntimeException('پوشهٔ راهنما قابل نوشتن نیست.', 503);
            }
            try {
                if (@file_put_contents($temporary, $content) !== strlen($content)) throw new RuntimeException('ذخیرهٔ فایل راهنما انجام نشد.', 503);
                if (!@chmod($temporary, fileperms($path) & 0777) || !@rename($temporary, $path)) throw new RuntimeException('جایگزینی فایل راهنما انجام نشد.', 503);
            } finally {
                if (is_file($temporary)) @unlink($temporary);
            }
            return $this->describe($relative, $content);
        });
    }

    public function delete(int $actor, string $relative, string $version): void
    {
        $this->founder($actor);
        $this->locked($relative, function () use ($relative, $version) {
            $path = $this->path($relative);
            $this->version($this->content($path), $version);
            if (!@unlink($path)) throw new RuntimeException('حذف فایل راهنما انجام نشد.', 503);
        });
    }

    private function founder(int $actor): void
    {
        if ($actor !== 1 || (int) auth()->id() !== 1) throw new RuntimeException('این عملیات فقط برای مدیر سایت مجاز است.', 403);
    }

    private function path(string $relative): string
    {
        $parts = explode('/', $relative);
        if (strlen($relative) > 1024 || str_contains($relative, '\\') || str_contains($relative, ':') || preg_match('/[\x00-\x1f]/', $relative) || !in_array($parts[0], ['docs', 'Modules'], true) || strtolower(pathinfo($relative, PATHINFO_EXTENSION)) !== 'md') $this->missing();
        foreach ($parts as $part) if ($part === '' || $part === '.' || $part === '..' || ($parts[0] === 'Modules' && strcasecmp($part, 'Lib') === 0)) $this->missing();
        $base = realpath(base_path());
        if (!$base) $this->missing();
        $path = $base;
        foreach ($parts as $part) {
            $path .= DIRECTORY_SEPARATOR . $part;
            if (is_link($path)) $this->missing();
        }
        $real = realpath($path);
        if (!$real || !is_file($real) || $this->canonical($real) !== $this->canonical($path)) $this->missing();
        return $real;
    }

    private function content(string $path): string
    {
        clearstatcache(true, $path);
        if (filesize($path) > self::MAX_BYTES) throw new RuntimeException('فایل راهنما برای ویرایش آنلاین بیش از حد بزرگ است.', 422);
        $content = @file_get_contents($path);
        if ($content === false) throw new RuntimeException('خواندن فایل راهنما انجام نشد.', 503);
        if (!mb_check_encoding($content, 'UTF-8') || str_contains($content, "\0")) throw new RuntimeException('فایل راهنما باید متن UTF-8 باشد.', 422);
        return $content;
    }

    private function describe(string $relative, string $content): array
    {
        preg_match('/^#\s+(.+)$/m', $content, $heading);
        return ['path' => $relative, 'title' => trim($heading[1] ?? pathinfo($relative, PATHINFO_FILENAME)), 'content' => $content, 'version' => hash('sha256', $content)];
    }

    private function version(string $content, string $version): void
    {
        if (!preg_match('/^[a-f0-9]{64}$/D', $version) || !hash_equals(hash('sha256', $content), $version)) throw new RuntimeException('فایل تغییر کرده است. نسخهٔ تازه را باز کنید و دوباره تلاش کنید.', 409);
    }

    private function locked(string $relative, callable $work): mixed
    {
        $path = $this->path($relative);
        $directory = storage_path('guide-document-locks');
        if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) throw new RuntimeException('قفل فایل راهنما در دسترس نیست.', 503);
        $handle = @fopen($directory . '/' . hash('sha256', $this->canonical($path)) . '.lock', 'c');
        if (!$handle) throw new RuntimeException('قفل فایل راهنما در دسترس نیست.', 503);
        try {
            if (!flock($handle, LOCK_EX | LOCK_NB)) throw new RuntimeException('فایل در حال ویرایش است. کمی بعد تلاش کنید.', 409);
            return $work();
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function missing(): never
    {
        throw new RuntimeException('فایل راهنما در دسترس نیست.', 404);
    }

    private function canonical(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;
    }
}
