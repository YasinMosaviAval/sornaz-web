<?php

use Core\router\Router;
use Modules\Page\Controllers\Web\LegacyRedirectController;

// These routes must be registered after all current application routes.
Router::get('/category/{legacyCategory}/page/{page}', [LegacyRedirectController::class, 'categoryPage']);
Router::get('/category/{legacyCategory}', [LegacyRedirectController::class, 'category']);
Router::get('/author/{legacyAuthor}/page/{page}', [LegacyRedirectController::class, 'authorPage']);
Router::get('/author/{legacyAuthor}', [LegacyRedirectController::class, 'author']);
Router::get('/{legacySection}/page/{page}', [LegacyRedirectController::class, 'paginated']);
Router::get('/{legacySlug}', [LegacyRedirectController::class, 'single']);
