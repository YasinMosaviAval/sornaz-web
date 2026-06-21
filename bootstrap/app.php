<?php

use Core\Application\Application;

$app = new Application();

$app->container()
    ->instance(
        \Core\Session\Session::class,
        new \Core\Session\Session()
    );

$app->container()->instance(
    \Core\Csrf\Csrf::class,
    new \Core\Csrf\Csrf()
);

$app->container()->instance(
    \Core\Auth\Auth::class,
    new \Core\Auth\Auth()
);

return $app;