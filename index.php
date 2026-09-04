<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php-error.log');


require_once __DIR__ . '/vendor/autoload.php';

global $app;

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->run();

// require_once __DIR__ . '/public/index.php';

// require_once __DIR__ . '/old/index.php';
