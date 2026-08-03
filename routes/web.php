<?php

foreach (glob(base_path('Modules/*/Routes/routes.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/Routes/web.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/Routes/api.php')) as $file) {
    require $file;
}
