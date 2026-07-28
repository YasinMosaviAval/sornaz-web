<?php

foreach (glob(base_path('modules/*/routes/routes.php')) as $file) {
    require $file;
}

foreach (glob(base_path('modules/*/routes/web.php')) as $file) {
    require $file;
}

foreach (glob(base_path('modules/*/routes/api.php')) as $file) {
    require $file;
}
