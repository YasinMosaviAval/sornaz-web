<?php


namespace Core\module;

class ModuleManager {
    protected array $modules = [];



    public function register(Module $module): void {
        $this->modules[] = $module;
    }



    public function modules(): array {
        return $this->modules;
    }



    public function boot(): void {
        foreach ($this->modules as $module) {
            $module->register();
        }
        foreach ($this->modules as $module) {
            $module->boot();
        }
    }






}