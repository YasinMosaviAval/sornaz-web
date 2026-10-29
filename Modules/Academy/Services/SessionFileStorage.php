<?php
namespace Modules\Academy\Services;

use RuntimeException;

/** No session file is ever stored in the document root or public media tree. */
final class SessionFileStorage
{
    public function store(int $sessionId, array $file, array $info): array
    {
        if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) throw new RuntimeException('فایل بارگذاری‌شده معتبر نیست.', 422);
        $root = $this->root(true);
        $directory = $root . '/' . $sessionId;
        if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) throw new RuntimeException('ذخیره فایل فعلاً در دسترس نیست.', 503);
        if ($this->normalize(realpath($directory) ?: '') !== $this->normalize($directory)) throw new RuntimeException('مسیر ذخیره فایل معتبر نیست.', 503);
        $name = bin2hex(random_bytes(24)) . '.' . $info['extension'];
        $path = $directory . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $path)) throw new RuntimeException('ذخیره فایل انجام نشد. دوباره تلاش کنید.', 503);
        @chmod($path, 0600);
        return ['relative' => $sessionId . '/' . $name, 'absolute' => $path, 'filename' => $name];
    }

    public function file(string $relative, int $sessionId): string
    {
        if (!preg_match('~^([1-9][0-9]*)/[a-f0-9]{48}\.[a-z0-9]{1,8}$~D', $relative, $match) || (int) $match[1] !== $sessionId) throw new RuntimeException('فایل در دسترس نیست.', 404);
        $root = $this->root(false);
        $path = $root . '/' . $relative;
        $resolved = realpath($path);
        if (!$resolved || is_link($path) || !is_file($resolved) || !$this->inside($resolved, $root . '/' . $sessionId)) throw new RuntimeException('فایل در دسترس نیست.', 404);
        return $resolved;
    }

    private function root(bool $create): string
    {
        $configured = function_exists('env') ? env('SORNAZ_SESSION_FILES_ROOT', '') : getenv('SORNAZ_SESSION_FILES_ROOT');
        $path = $configured ?: dirname(base_path()) . '/sornaz-private/session-files';
        if (!is_string($path) || !preg_match('~^(?:[A-Za-z]:/|/)~', str_replace('\\', '/', $path)) || str_contains($path, "\0")) throw new RuntimeException('محل خصوصی ذخیره فایل تنظیم نشده است.', 503);
        $path = rtrim(str_replace('\\', '/', $path), '/');
        $resolved = $this->resolve($path);
        foreach ([base_path(), (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')] as $public) {
            if ($public !== '' && $this->inside($resolved, realpath($public) ?: $public)) throw new RuntimeException('محل ذخیره فایل باید خارج از پوشه عمومی سایت باشد.', 503);
        }
        if ($create && !is_dir($path) && !@mkdir($path, 0700, true) && !is_dir($path)) throw new RuntimeException('محل خصوصی ذخیره فایل قابل نوشتن نیست.', 503);
        if (!is_dir($path)) throw new RuntimeException('فایل در دسترس نیست.', 404);
        $real = realpath($path);
        if (!$real || $this->normalize($real) !== $this->normalize($resolved)) throw new RuntimeException('مسیر خصوصی ذخیره فایل معتبر نیست.', 503);
        return str_replace('\\', '/', $real);
    }

    private function resolve(string $path): string
    {
        $parts = [];
        $parent = $path;
        while (!file_exists($parent)) {
            $next = dirname($parent);
            if ($next === $parent) throw new RuntimeException('مسیر خصوصی ذخیره فایل معتبر نیست.', 503);
            array_unshift($parts, basename($parent));
            $parent = $next;
        }
        $resolved = realpath($parent);
        if (!$resolved) throw new RuntimeException('مسیر خصوصی ذخیره فایل معتبر نیست.', 503);
        foreach ($parts as $part) {
            if (in_array($part, ['.', '..'], true)) throw new RuntimeException('مسیر خصوصی ذخیره فایل معتبر نیست.', 503);
            $resolved .= '/' . $part;
        }
        return $resolved;
    }

    private function inside(string $path, string $root): bool
    {
        $path = $this->normalize($path); $root = $this->normalize($root);
        return $path === $root || str_starts_with($path, $root . '/');
    }

    private function normalize(string $path): string
    {
        $path = rtrim(str_replace('\\', '/', $path), '/');
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;
    }
}
