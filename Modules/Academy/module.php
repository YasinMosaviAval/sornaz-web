<?php

namespace Modules\Academy;

class Module extends \Core\Module\Module {
    public function name(): string { return 'Academy'; }
    public function views(): ?string { return __DIR__.'/Views'; }
    public function routes(): ?string { return __DIR__.'/routes/web.php'; }
    public function config(): ?string { return __DIR__.'/config.php'; }
    public function migrations(): ?string { return __DIR__.'/database/migrations'; }
    public function translations(): ?string { return __DIR__.'/lang'; }
}