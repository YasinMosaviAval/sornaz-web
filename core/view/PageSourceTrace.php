<?php

namespace Core\view;

/** Founder-only source names; never exposes source content or absolute filesystem paths. */
final class PageSourceTrace
{
    private static array $sections = [];
    private static array $stack = [];

    public static function enabled(): bool
    {
        try {
            return function_exists('auth') && (int) auth()->id() === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function relative(string $file): ?string
    {
        $root = str_replace('\\', '/', realpath(dirname(__DIR__, 2)) ?: dirname(__DIR__, 2)) . '/';
        $resolved = realpath($file);
        if ($resolved === false) return null;
        $file = str_replace('\\', '/', $resolved);
        if (!str_starts_with($file, $root)) return null;
        $path = substr($file, strlen($root));
        if (!preg_match('~^(?:index\.php$|(?:core|Modules|routes|bootstrap)/.*\.php$)~D', $path)) return null;
        if (preg_match('~/(?:Lib|config|storage|vendor)/~i', '/' . $path)) return null;
        return $path;
    }

    public static function reset(): void
    {
        self::$sections = self::$stack = [];
    }

    public static function beginComponent(string $file): array
    {
        $name = pathinfo($file, PATHINFO_FILENAME);
        self::$stack[] = $name;
        self::remember($file);
        return get_included_files();
    }

    private static function remember(string $file): void
    {
        $path = self::relative($file);
        if ($path === null) return;
        foreach (self::$stack as $name) self::$sections[$name][$path] = true;
    }

    public static function endComponent(array $before): void
    {
        foreach (array_diff(get_included_files(), $before) as $file) self::remember($file);
        array_pop(self::$stack);
    }

    public static function manifest(): array
    {
        if (!self::enabled()) return [];
        $files = [];
        foreach (get_included_files() as $file) {
            $path = self::relative($file);
            if ($path !== null) $files[$path] = true;
        }
        $files = array_keys($files);
        sort($files);
        $sections = [];
        foreach (self::$sections as $name => $paths) $sections[$name] = array_keys($paths);
        return ['files' => $files, 'sections' => $sections];
    }

    public static function decorate(string $html): string
    {
        if (!self::enabled() || !preg_match('/<head\b[^>]*>/i', $html, $head, PREG_OFFSET_CAPTURE)) return $html;
        $json = json_encode(self::manifest(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        $version = filemtime(dirname(__DIR__, 2) . '/assets/theme/page-source-trace.js') ?: 1;
        $tag = '<script type="application/json" id="pageSourceManifest">' . $json . '</script>'
            . '<script src="/assets/theme/page-source-trace.js?v=' . $version . '"></script>';
        if (!headers_sent()) header('Cache-Control: private, no-store');
        $offset = $head[0][1] + strlen($head[0][0]);
        return substr_replace($html, $tag, $offset, 0);
    }

    public static function jsonMetadata(): array
    {
        if (($_SERVER['HTTP_X_SORNAZ_SOURCE_TRACE'] ?? '') !== '1' || !self::enabled()) return [];
        if (!headers_sent()) {
            header('X-Sornaz-Source-Trace: 1');
            header('Cache-Control: private, no-store');
        }
        return ['_pageSources' => self::manifest()];
    }
}
