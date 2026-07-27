<?php

namespace Core\Translation;

class TranslationService {

    protected static ?TranslationManager $manager = null;


    public static function manager(): TranslationManager {
        if (!static::$manager) {
            static::$manager = new TranslationManager();
        }
        return static::$manager;
    }



}