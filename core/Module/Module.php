<?php

/*
    namespace Core\Module;

    class Module {

        public string $name;
        public string $path;
        public bool $enabled;
        public string $provider;
        public array $config = [];



        public function __construct(array $config, string $path){
            $this->config = $config;
            $this->path = $path;
            $this->name = $config['name'];
            $this->enabled = $config['enabled'] ?? true;
            $this->provider = $config['provider'];
        }

    }
*/


namespace Core\Module;

abstract class Module {
    abstract public function name(): string;



    public function boot(): void {
    }



    public function register(): void {
    }



    public function routes(): ?string {
        return null;
    }



    public function migrations(): ?string {
        return null;
    }



    public function views(): ?string {
        return null;
    }



    public function config(): ?string {
        return null;
    }



    public function translations(): ?string {
        return null;
    }






}