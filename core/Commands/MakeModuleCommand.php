<?php

namespace Core\commands;

use Core\console\Command;
use Core\console\Filesystem;
use Core\console\Stub;

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
            /*
            |--------------------------------------------------------------------------
            | Controllers
            |--------------------------------------------------------------------------
            */
            'Controller.stub' => 'Controllers/Web/{{module}}Controller.php',
            'ApiController.stub' => 'Controllers/Api/{{module}}Controller.php',
            /*
            |--------------------------------------------------------------------------
            | Services
            |--------------------------------------------------------------------------
            */
            'Service.stub' => 'Services/{{module}}Service.php',
            /*
            |--------------------------------------------------------------------------
            | Events
            |--------------------------------------------------------------------------
            */
            'Event.stub' => 'Events/{{module}}Event.php',
            /*
            |--------------------------------------------------------------------------
            | Listeners
            |--------------------------------------------------------------------------
            */
            'Listener.stub' => 'Listeners/{{module}}Listener.php',
            /*
            |--------------------------------------------------------------------------
            | Repository
            |--------------------------------------------------------------------------
            */
            'Repository.stub' => 'Repositories/{{module}}Repository.php',
            /*
            |--------------------------------------------------------------------------
            | Model
            |--------------------------------------------------------------------------
            */
            'Model.stub' => 'Models/{{module}}Model.php',
            /*
            |--------------------------------------------------------------------------
            | DTO
            |--------------------------------------------------------------------------
            */
            'DTO.stub' => 'DTO/{{module}}DTO.php',
            /*
            |--------------------------------------------------------------------------
            | Requests
            |--------------------------------------------------------------------------
            */
            'StoreRequest.stub' => 'Requests/{{module}}StoreRequest.php',
            'UpdateRequest.stub' => 'Requests/{{module}}UpdateRequest.php',
            /*
            |--------------------------------------------------------------------------
            | Policy
            |--------------------------------------------------------------------------
            */
            'Policy.stub' => 'Policies/{{module}}Policy.php',
            /*
            |--------------------------------------------------------------------------
            | Provider
            |--------------------------------------------------------------------------
            */
            'Provider.stub' => 'Providers/{{module}}ServiceProvider.php',
            /*
            |--------------------------------------------------------------------------
            | Routes
            |--------------------------------------------------------------------------
            */
            'web.stub' => 'Routes/web.php',
            'api.stub' => 'Routes/api.php',
            'routes.stub' => 'Routes/routes.php',
            /*
            |--------------------------------------------------------------------------
            | View
            |--------------------------------------------------------------------------
            */
            'index.view.stub' => 'Resources/Views/index.php',
            'create.view.stub' => 'Resources/Views/create.php',
            'edit.view.stub' => 'Resources/Views/edit.php',
            'show.view.stub' => 'Resources/Views/show.php',
            /*
            |--------------------------------------------------------------------------
            | Layout
            |--------------------------------------------------------------------------
            */
            'layout.stub' => 'Resources/Views/layouts/main.php',
            /*
            |--------------------------------------------------------------------------
            | CSS
            |--------------------------------------------------------------------------
            */
            // 'style.stub'       => 'Resources/Assets/css/style.css',
            // 'index.css.stub'   => 'Resources/Assets/css/index.css',
            // 'create.css.stub'  => 'Resources/Assets/css/create.css',
            // 'edit.css.stub'    => 'Resources/Assets/css/edit.css',
            // 'show.css.stub'    => 'Resources/Assets/css/show.css',
            /*
            |--------------------------------------------------------------------------
            | Javascript
            |--------------------------------------------------------------------------
            */
            // 'script.stub'     => 'Resources/Assets/js/script.js',
            // 'index.js.stub'   => 'Resources/Assets/js/index.js',
            // 'create.js.stub'  => 'Resources/Assets/js/create.js',
            // 'edit.js.stub'    => 'Resources/Assets/js/edit.js',
            // 'show.js.stub'    => 'Resources/Assets/js/show.js',
            /*
            |--------------------------------------------------------------------------
            | Module Files
            |--------------------------------------------------------------------------
            */
            'config.stub' => 'config.php',
            'helpers.stub' => 'helpers.php',
            'module.stub' => 'module.php',
            'README.stub' => 'README.md',
        ];
    }



    protected function createDirectories(string $base): void {
        $folders = [
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

            'Resources',
            // 'Resources/Assets',
            // 'Resources/Assets/css',
            // 'Resources/Assets/js',
            // 'Resources/Assets/images',
            // 'Resources/Assets/fonts',

            'Resources/Views',
            'Resources/Views/layouts',
            'Resources/Views/partials',
            'Resources/Views/components',
            'Resources/Views/sections',

            'Routes',

            'Services',
        ];

        foreach($folders as $folder){
            $this->filesystem->ensureDirectory($base . '/' . $folder);
        }
    }



    protected function createFiles(string $base, array $variables): void {
        foreach($this->files() as $stub=>$destination){
            $destination=$this->replaceFilenameVariables($destination, $variables);
            $this->generate(base_path('core/stubs/module/'.$stub), $base.'/'.$destination, $variables);
        }
    }



    protected function generate(string $stub, string $destination, array $variables): void {
        $stub = new Stub($stub);
        $content = $stub->replace($variables)->render();
        $this->filesystem->put($destination, $content);
    }



    protected function replaceFilenameVariables(string $path, array $variables): string {
        foreach($variables as $key=>$value){
            $path=str_replace('{{'.$key.'}}', $value, $path);
        }
        return $path;
    }




}