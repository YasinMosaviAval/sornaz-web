<?php
namespace Modules\Analytics\Services;

use RuntimeException;

final class ChatAttachmentPolicy
{
    private const TYPES = [
        'image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp', 'image/gif'=>'gif',
        'application/pdf'=>'pdf', 'text/plain'=>'txt',
        'audio/mpeg'=>'mp3', 'audio/mp4'=>'m4a', 'audio/x-m4a'=>'m4a',
        'audio/ogg'=>'ogg', 'application/ogg'=>'ogg', 'audio/webm'=>'webm',
        'audio/wav'=>'wav', 'audio/x-wav'=>'wav', 'audio/flac'=>'flac',
        'video/mp4'=>'mp4', 'video/webm'=>'webm', 'video/quicktime'=>'mov',
        'application/zip'=>'zip', 'application/x-zip-compressed'=>'zip',
        'application/x-7z-compressed'=>'7z',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'=>'pptx',
    ];

    public static function inspect(string $path, string $originalName): array
    {
        $size = is_file($path) ? filesize($path) : false;
        if (!$size || $size > 100 * 1024 * 1024) {
            throw new RuntimeException('حجم فایل باید بین ۱ بایت و ۱۰۰ مگابایت باشد.', 422);
        }
        $name = basename(str_replace('\\', '/', $originalName));
        $name = preg_replace('/[\x00-\x1f\x7f]/', '', $name);
        // Reject active content even when its MIME is disguised as an image or text.
        if ($name === '' || preg_match('/\.(?:php\d*|phtml|phar|html?|shtml|svg|js|mjs|exe|com|bat|cmd|ps1|sh|cgi|pl)(?:\.|$)/i', $name)) {
            throw new RuntimeException('این نوع فایل مجاز نیست.', 422);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!isset(self::TYPES[$mime])) throw new RuntimeException('این نوع فایل مجاز نیست.', 422);
        return ['name'=>$name, 'mime'=>$mime, 'extension'=>self::TYPES[$mime], 'size'=>$size];
    }
}
