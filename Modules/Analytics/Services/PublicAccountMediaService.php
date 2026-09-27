<?php
namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

final class PublicAccountMediaService
{
    public function library(string $year, string $month, string $filename, ?array $viewer = null): array
    {
        if (!preg_match('/^\d{4}$/D', $year) || !preg_match('/^(?:0[1-9]|1[0-2])$/D', $month)
            || !preg_match('/^[a-f0-9]{32}\.[a-z0-9]+$/D', $filename)) throw new RuntimeException('File not found', 404);
        $relative = "assets/media/library/$year/$month/$filename";
        $row = DB::table('media_files')->where('path', $relative)->whereNull('deleted_at')->first();
        if (!$row || DB::table('academy_documents')->where('media_file_id', (int)$row['media_file_id'])->first()) throw new RuntimeException('File not found', 404);
        $public = $row['disk'] === 'public' && $row['visibility'] === 'public';
        if (!$public && (!$viewer || ((int)$row['user_id'] !== (int)$viewer['user_id'] && !\Modules\System\Services\SiteAdminAccess::allows($viewer)))) {
            throw new RuntimeException('File not found', 404);
        }
        $root = realpath(base_path('assets/media/library'));
        $path = realpath(base_path($relative));
        if (!$root || !$path || !str_starts_with($path, $root.DIRECTORY_SEPARATOR) || !is_file($path)) throw new RuntimeException('File not found', 404);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $inline = in_array($mime, ['image/jpeg','image/png','image/webp','image/gif','video/mp4','video/webm','video/quicktime','audio/mpeg','audio/wav','audio/ogg'], true);
        return ['path'=>$path, 'name'=>$filename, 'mime'=>$inline?$mime:'application/octet-stream', 'inline'=>$inline];
    }

    public function find(string $user, string $year, string $month, string $filename): array
    {
        if (!ctype_digit($user) || !preg_match('/^\d{4}$/D', $year)
            || !preg_match('/^(?:0[1-9]|1[0-2])$/D', $month)
            || !preg_match('/^[a-f0-9]{32}\.(?:jpg|jpeg|png|webp|gif|mp4|webm|mov)$/D', $filename)) {
            throw new RuntimeException('File not found', 404);
        }
        $relative = "storage/account-media/$user/$year/$month/$filename";
        $row = DB::table('media_files')->where('path', $relative)
            ->where('disk', 'public')->where('visibility', 'public')
            ->whereIn('collection', ['avatar','cover','logo','intro_video','gallery','teacher_gallery'])
            ->whereNull('deleted_at')->first();
        // Documents must never become public merely through a changed media flag.
        if (!$row || DB::table('academy_documents')->where('media_file_id', (int)$row['media_file_id'])->first()) {
            throw new RuntimeException('File not found', 404);
        }
        $root = realpath(base_path('storage/account-media'));
        $path = realpath(base_path($relative));
        if (!$root || !$path || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path)) {
            throw new RuntimeException('File not found', 404);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!in_array($mime, ['image/jpeg','image/png','image/webp','image/gif','video/mp4','video/webm','video/quicktime'], true)) {
            throw new RuntimeException('File not found', 404);
        }
        return ['path'=>$path, 'name'=>$filename, 'mime'=>$mime];
    }
}
