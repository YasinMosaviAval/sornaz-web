<?php

use Core\Application\Application;

$app = new Application();

$app->container()
    ->instance(
        \Core\Session\Session::class,
        new \Core\Session\Session()
    );

return $app;