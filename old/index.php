<?php
define('APP_ENTRY', true);
require_once getcwd() . '/system/loader.php';

$router = new Router();
$router->dispatch();
