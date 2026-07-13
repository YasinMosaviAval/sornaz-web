<?php

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