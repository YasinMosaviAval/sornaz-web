<?php

namespace Core\Localization;

use Core\Translation\TranslationManager;

class TranslationService {

    public static function manager(): TranslationManager {
        return app()
            ->container()
            ->make(TranslationManager::class);
    }

}