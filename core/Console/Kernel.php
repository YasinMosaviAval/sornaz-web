<?php

namespace Core\console;

use Core\commands\MakeModuleCommand;

class Kernel {

    protected array $commands = ['make:module'=> MakeModuleCommand::class,];



    public function __construct(protected $app) {
    }



    public function handle(array $argv): int {
        if(count($argv)<2){
            echo "No command.\n";
            return 1;
        }
        $command=$argv[1];
        if(!isset($this->commands[$command])){
            echo "Unknown command : {$command}\n";
            return 1;
        }
        $class=$this->commands[$command];
        $instance=new $class;
        return $instance->handle($argv);
    }




}