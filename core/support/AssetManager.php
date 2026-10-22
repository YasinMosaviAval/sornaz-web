<?php

namespace Core\support;

use Exception;

class AssetManager
{

    protected static array $published = [];

    public static function version(string $url): string
    {
        $path = base_path(ltrim($url, '/'));
        if (!is_file($path) || !is_readable($path)) return '1';
        // Upload tools can preserve both timestamps and file sizes.
        // Read content rather than caching a version by those attributes.
        $hash = hash_file('sha256', $path);
        return $hash !== false ? substr($hash, 0, 16) : '1';
    }

    public static function publish(string $module, string $type, string $file): string
    {
        $source = base_path("assets/{$module}/{$type}/{$file}");
        if (!file_exists($source)) {
            throw new Exception("Asset not found : {$source}");
        }
        $targetDir = asset_path("/{$module}/{$type}");
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $target = "{$targetDir}/{$file}";
        if (!file_exists($target) || filemtime($target) != filemtime($source)) {
            copy($source, $target);
            touch($target, filemtime($source));
        }
        return "/assets/{$module}/{$type}/{$file}";
    }
}
