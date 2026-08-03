<?php

namespace Core\localization;

use Core\translation\TranslationManager;

class TranslationService {

    public static function manager(): TranslationManager {
        return app()
            ->container()
            ->make(TranslationManager::class);
    }

}