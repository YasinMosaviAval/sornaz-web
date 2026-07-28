<?php

namespace Modules\System\Listeners;

class CreateTranslationRecord {
    public function handle($event) {
        echo 'Translation Created<br>';
    }
}