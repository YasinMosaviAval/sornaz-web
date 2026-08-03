<?php

namespace Core\console;

class Filesystem {

    public function ensureDirectory(string $path): void {
        if (!is_dir($path)) {
            mkdir($path,0777,true);
        }
    }



    public function put(string $path,string $contents): void {
        file_put_contents($path,$contents);
    }



    public function get(string $path): string {
        return file_get_contents($path);
    }



    public function exists(string $path): bool {
        return file_exists($path);
    }



}