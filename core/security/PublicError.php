<?php

namespace Core\security;

final class PublicError
{
    public static function message(\Throwable $error): string
    {
        if ($error instanceof \RuntimeException && !($error instanceof \PDOException)) {
            return $error->getMessage();
        }
        error_log(get_class($error) . ': ' . $error->getMessage());
        return 'خطای داخلی رخ داد. لطفاً کمی بعد دوباره تلاش کنید.';
    }

    public static function status(\Throwable $error): int
    {
        if (!($error instanceof \RuntimeException) || $error instanceof \PDOException) {
            return 500;
        }
        return in_array($error->getCode(), [400, 401, 403, 404, 409, 419, 422, 429, 503], true) ? $error->getCode() : 422;
    }
}
