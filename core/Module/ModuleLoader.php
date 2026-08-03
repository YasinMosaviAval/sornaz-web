<?php

namespace Core\module;

class ModuleLoader {

    public static function load(ModuleManager $manager): void {
        foreach (glob(base_path('Modules/*/module.php')) as $file) {
            $moduleName = basename(dirname($file));
            $class = "Modules\\{$moduleName}\\Module";
            if (class_exists($class)) {
                $manager->register(new $class());
            }
        }
    }




}