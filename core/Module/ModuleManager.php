<?php

/*
    namespace Core\Module;

    class ModuleManager {

        protected array $modules = [];



        public function scan(): void {
            $modulesPath = base_path('Modules');
            foreach(scandir($modulesPath) as $directory){
                if($directory=='.' || $directory=='..'){
                    continue;
                }
                $path = $modulesPath.'/'.$directory;
                if(!is_dir($path)){
                    continue;
                }
                $manifest = $path.'/module.php';
                if(!file_exists($manifest)){
                    continue;
                }
                $config = require $manifest;
                $this->modules[] = new Module($config, $path);
            }
        }



        public function register(): void {
            foreach($this->enabled() as $module){
                app()->register($module->provider);
            }
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

*/

namespace Core\Module;

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