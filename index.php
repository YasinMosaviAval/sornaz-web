<?php

require_once __DIR__ . '/vendor/autoload.php';

global $app;

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->run();

// require_once __DIR__ . '/public/index.php';

// require_once __DIR__ . '/old/index.php';