<?php

foreach (glob(base_path('Modules/*/[Rr]outes/routes.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/[Rr]outes/web.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/[Rr]outes/api.php')) as $file) {
    require $file;
}

require base_path('routes/legacy.php');
