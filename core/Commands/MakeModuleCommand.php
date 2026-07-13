<?php

namespace Core\Commands;

use Core\Console\Command;
use Core\Console\Filesystem;
use Core\Console\Stub;

class MakeModuleCommand extends Command {
    protected Filesystem $filesystem;



    public function __construct() {
        $this->filesystem = new Filesystem();
    }



    public function handle(array $arguments): int {
        if(empty($arguments[2])){
            $this->error("Module name required.");
            return 1;
        }
        $module = ucfirst($arguments[2]);
        $variables = $this->buildVariables($module);
        $basePath = base_path("Modules/{$module}");
        $this->createDirectories($basePath);
        $this->createFiles($basePath, $variables);
        $this->info("Module {$module} created.");
        return 0;
    }



    protected function buildVariables(string $module): array {
        return [
            'module'=>$module,
            'table'=>strtolower($module).'s',
            'primaryKey'=>strtolower($module).'_id',
        ];
    }



    protected function files(): array {
        return [
            'Controller.stub' => 'Controllers/Web/{{module}}Controller.php',
            'Service.stub' => 'Services/{{module}}Service.php',
            'Repository.stub' => 'Repositories/{{module}}Repository.php',
            'Model.stub' => 'Models/{{module}}Model.php',
            'Provider.stub' => 'Providers/{{module}}ServiceProvider.php',
            'Policy.stub' => 'Policies/{{module}}Policy.php',
            'DTO.stub' => 'DTO/{{module}}DTO.php',
            'Request.stub' => 'Requests/{{module}}StoreRequest.php',
            'routes.stub' => 'routes.php',
            'config.stub' => 'config.php',
            'helpers.stub' => 'helpers.php',
            'README.stub' => 'README.md',
        ];
    }



    protected function createDirectories(string $base): void {
        $folders=[
            'Controllers',
            'Controllers/Web',
            'Controllers/Api',
            'DTO',
            'Events',
            'Listeners',
            'Models',
            'Policies',
            'Providers',
            'Repositories',
            'Requests',
            'Services',
            'Views',
        ];
        foreach($folders as $folder){
            $this->filesystem->ensureDirectory($base . '/' . $folder);
        }
    }



    protected function createFiles(string $base, array $variables): void {
        foreach($this->files() as $stub=>$destination){
            $destination=$this->replaceFilenameVariables(
                $destination,
                $variables
            );
            $this->generate(
                base_path('core/Stubs/module/'.$stub),
                $base.'/'.$destination,
                $variables
            );
        }
    }



    protected function generate(string $stub, string $destination, array $variables): void {
        $stub = new Stub($stub);
        $content = $stub->replace($variables)->render();
        $this->filesystem->put(
            $destination,
            $content
        );
    }



    protected function replaceFilenameVariables(string $path, array $variables): string {
        foreach($variables as $key=>$value){
            $path=str_replace(
                '{{'.$key.'}}',
                $value,
                $path
            );
        }
        return $path;
    }



}