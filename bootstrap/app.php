<?php

use Core\Application\Application;
use Core\Csrf\Csrf;
use Core\Database\Connection;
use Core\Events\EventDispatcher;
use Core\Session\Session;
use Modules\System\Contracts\UserRepositoryInterface;
use Modules\System\Repositories\UserRepository;

$app = new Application();

$app->container()->instance(Session::class, new Session());

$app->container()->instance(EventDispatcher::class, new EventDispatcher());

$app->container()->instance(Csrf::class, new Csrf());

$app->container()->instance(Connection::class, new Connection(require base_path('config/database.php')));

$app->container()->bind(UserRepositoryInterface::class, UserRepository::class);

require base_path('config/gates.php');

return $app;