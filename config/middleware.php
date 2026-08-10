<?php

use Core\auth\GuestMiddleware;
use Core\csrf\CsrfMiddleware;
use Core\middleware\AuthMiddleware;
use Modules\Academy\Middleware\AcademyAccountMiddleware;
use Modules\Academy\Middleware\AcademyPanelMiddleware;
use Modules\System\Middleware\SiteAdminMiddleware;

return [
    'auth' => AuthMiddleware::class,
    'guest' => GuestMiddleware::class,
    'csrf' => CsrfMiddleware::class,
    'academy' => AcademyAccountMiddleware::class,
    'academy-panel' => AcademyPanelMiddleware::class,
    'site-admin' => SiteAdminMiddleware::class,
];
