<?php

namespace Modules\Blog\Providers;

use Core\Providers\ServiceProvider;
use Modules\Blog\Contracts\BlogRepositoryInterface;
use Modules\Blog\Repositories\BlogRepository;
use Modules\Blog\Services\BlogService;
use Modules\Blog\Services\CategoryService;

class BlogServiceProvider extends ServiceProvider {

    public function register(): void {
        $this->app
            ->container()
            ->bind(
                BlogRepositoryInterface::class,
                BlogRepository::class
            );
        $this->app
            ->container()
            ->singleton(BlogService::class);
        $this->app
            ->container()
            ->singleton(CategoryService::class);
    }

    public function boot(): void { }
}