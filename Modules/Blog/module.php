<?php

container()->bind(
    \Modules\Blog\Contracts\BlogRepositoryInterface::class,
    \Modules\Blog\Repositories\BlogRepository::class
);

return [
    'name' => 'Blog',
    'enabled' => true,
    'provider' => Modules\Blog\Providers\BlogServiceProvider::class,
];

