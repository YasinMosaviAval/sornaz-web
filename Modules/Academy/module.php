<?php

namespace Modules\Academy;

// این use را حذف کن

class Module extends \Core\Module\Module
{
    public function name(): string
    {
        return 'Academy';
    }

    public function routes(): ?string
    {
        return __DIR__.'/routes/web.php';
    }

    public function migrations(): ?string
    {
        return __DIR__.'/database/migrations';
    }

    public function views(): ?string
    {
        return __DIR__.'/Views';
    }

    public function config(): ?string
    {
        return __DIR__.'/config.php';
    }

    public function translations(): ?string
    {
        return __DIR__.'/lang';
    }
}