<?php

use Core\application\application;
use Core\csrf\Csrf;
use Core\database\Connection;
use Core\events\EventDispatcher;
use Core\session\Session;
use Modules\System\Contracts\UserRepositoryInterface;
use Modules\System\Repositories\UserRepository;
use Modules\Blog\Contracts\BlogRepositoryInterface;
use Modules\Blog\Repositories\BlogRepository;
use Core\localization\Contracts\TranslationRepositoryInterface;
use Core\localization\Repositories\TranslationRepository;

$app = new Application();

$app->container()->instance(Session::class, new Session());

$app->container()->instance(EventDispatcher::class, new EventDispatcher());

$app->container()->instance(Csrf::class, new Csrf());

$app->container()->instance(Connection::class, new Connection(require base_path('config/database.php')));

$app->container()->instance(PDO::class, app()->container()->make(Connection::class)->pdo());

$app->container()->bind(TranslationRepositoryInterface::class, TranslationRepository::class);

$app->container()->bind(UserRepositoryInterface::class, UserRepository::class);

$app->container()->bind(BlogRepositoryInterface::class, BlogRepository::class);

require base_path('config/gates.php');

return $app;