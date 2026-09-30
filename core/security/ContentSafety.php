<?php

namespace Core\security;

use RuntimeException;

/** Context-specific policy: plain text stays plain; only rich content is purified. */
final class ContentSafety
{
    public static function text(mixed $value, int $max = 20000, bool $required = false): string
    {
        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
            throw new RuntimeException('متن ورودی معتبر نیست.', 422);
        }
        $value = trim($value);
        if (mb_strlen($value) > $max || ($required && $value === '')) {
            throw new RuntimeException('طول متن ورودی معتبر نیست.', 422);
        }
        return $value;
    }

    public static function url(string $value): string
    {
        $value = trim($value);
        if (preg_match('/[\x00-\x20\x7f\\\\]/', $value)) {
            return '';
        }
        if (str_starts_with($value, '/') && !str_starts_with($value, '//')) {
            return $value;
        }
        return preg_match('~^https?://~i', $value) && filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
    }

    public static function json(mixed $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public static function rich(string $html, string $profile = 'article'): string
    {
        static $purifiers = [];
        if (!isset($purifiers[$profile])) {
            require_once dirname(__DIR__, 2) . '/Modules/System/Lib/HTMLPurifier/library/HTMLPurifier.auto.php';
            $config = \HTMLPurifier_Config::createDefault();
            $allowed = 'p,br,strong,b,em,i,u,s,blockquote,ul,ol,li,a[href|title],span';
            if ($profile === 'article') {
                $allowed .= ',div,h1,h2,h3,h4,h5,h6,pre,code,hr,img[src|alt|width|height],table,thead,tbody,tr,th,td';
            }
            $config->set('HTML.Allowed', $allowed);
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
            $config->set('Attr.EnableID', false);
            $config->set('Cache.DefinitionImpl', null);
            $purifiers[$profile] = new \HTMLPurifier($config);
        }
        return $purifiers[$profile]->purify($html);
    }

    public static function comment(mixed $value): string
    {
        $html = self::rich(self::text($value, 20000, true), 'comment');
        $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        self::text($plain, 3000, true);
        return $html;
    }
}
