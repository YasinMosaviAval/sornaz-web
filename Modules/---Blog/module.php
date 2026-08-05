<?php

namespace Modules\Blog;

use Core\Module\Module as CoreModule;
use Modules\Blog\Providers\BlogServiceProvider;

class Module extends CoreModule {
    public function name(): string {
        return 'Blog';
    }

    public function providers(): array {
        return [BlogServiceProvider::class];
    }
}