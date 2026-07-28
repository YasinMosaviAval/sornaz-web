<?php

namespace Core\Module;

class ModuleLoader {

    public static function load(ModuleManager $manager): void {
        foreach (glob(base_path('modules/*/module.php')) as $file) {
            $moduleName = basename(dirname($file));
            $class = "modules\\{$moduleName}\\module";
            if (class_exists($class)) {
                $manager->register(new $class());
            }
        }
    }




}