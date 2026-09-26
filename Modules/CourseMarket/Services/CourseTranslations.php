<?php
namespace Modules\CourseMarket\Services;

final class CourseTranslations
{
    public static function english(): array
    {
        static $messages;
        return $messages ??= json_decode(file_get_contents(__DIR__.'/../Resources/lang/en.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function text(string $message): string
    {
        return locale() === 'en' ? (self::english()[$message] ?? $message) : $message;
    }
}
