<?php

namespace Core\Providers;

abstract class ServiceProvider
{
    /**
     * ثبت سرویس‌ها داخل Container
     */
    public function register(): void {
        file_put_contents(
            storage_path('logs/provider.log'),
            "register\n",
            FILE_APPEND
        );
    }

    /**
     * عملیات بعد از ثبت تمام Providerها
     */
    public function boot(): void {
        file_put_contents(
            storage_path('logs/provider.log'),
            "boot\n",
            FILE_APPEND
        );
    }
}