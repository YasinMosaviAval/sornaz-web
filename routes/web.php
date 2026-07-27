<?php

foreach (glob(base_path('Modules/*/routes/routes.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/routes/web.php')) as $file) {
    require $file;
}

foreach (glob(base_path('Modules/*/routes/api.php')) as $file) {
    require $file;
}
