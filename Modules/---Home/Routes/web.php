<?php

use Core\Router\Router;
use Modules\Home\Controllers\Web\HomeController;

Router::get('/', [HomeController::class, 'index']);
