<?php

namespace Core\Module;

class ModuleManager {

    protected array $modules = [];



    public function scan(): void {

    }



    public function register(): void {

    }



    public function all(): array {
        return $this->modules;
    }



    public function enabled(): array {
        return array_filter(
            $this->modules,
            fn($module)=>$module->enabled
        );
    }




}