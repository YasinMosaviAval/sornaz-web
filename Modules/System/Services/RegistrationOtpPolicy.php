<?php
namespace Modules\System\Services;

/** Only the authenticated site owner can create accounts without an OTP. */
final class RegistrationOtpPolicy
{
    public static function web(): bool
    {
        return (int)auth()->id() === 1;
    }

    public static function api(): bool
    {
        $user = app()->container()->make(MobileAuthTokenService::class)->userFromRequest();
        return (int)($user['user_id'] ?? 0) === 1;
    }
}
