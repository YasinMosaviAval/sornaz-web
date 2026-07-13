<?php

namespace Core\Application;

use Core\Container\Container;
use Core\Providers\ProviderManager;
use Core\Module\ModuleManager;

class Application {


    protected static ?Application $instance = null;
    protected Container $container;
    protected ProviderManager $providers;
    protected string $locale='fa';
    protected ModuleManager $modules;



    public function __construct() {
        self::$instance = $this;
        $this->container = new Container();
        $this->providers = new ProviderManager();
        $this->modules = new ModuleManager();
        $this->modules->scan();
    }


    public function modules(): ModuleManager {
        return $this->modules;
    }


    public static function getInstance(): Application {
        return self::$instance;
    }


    public function container(): Container {
        return $this->container;
    }


    public function run() {
        $this->bootstrap();
        require base_path('routes/web.php');
        (new Kernel())->handle();
    }


    public function providers(): ProviderManager {
        return $this->providers;
    }


    protected function bootstrap(): void {
        $providers = require base_path('config/providers.php');
        $this->providers->load($providers);
        $this->providers->register();
        $this->providers->boot();
    }



    public function getLocale(): string {
        return $this->locale;
    }



    public function setLocale(string $locale): void {
        $this->locale=$locale;
    }



}