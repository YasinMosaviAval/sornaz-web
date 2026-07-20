<?php

namespace Core\Support;

class AssetManager {
    protected static array $published = [];

    public static function publish(string $module, string $type, string $file): string {
        $source = base_path("Modules/{$module}/Resources/Assets/{$type}/{$file}");
        if (!file_exists($source)) {
            throw new \Exception("Asset not found : {$source}");
        }
        $targetDir = public_path("assets/{$module}/{$type}");
        if (!is_dir($targetDir)) {
            mkdir($targetDir,0777,true);
        }
        $target = "{$targetDir}/{$file}";
        if (!file_exists($target) || filemtime($target)!=filemtime($source)){
            copy($source,$target);
            touch($target,filemtime($source));
        }
        return "/assets/{$module}/{$type}/{$file}";
    }


}