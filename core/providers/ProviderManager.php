<?php

namespace Core\Providers;

class ProviderManager {
    /**
     * @var ServiceProvider[]
     */
    protected array $providers = [];


    public function load(array $providers): void
    {
        foreach ($providers as $provider) {

            $this->providers[] = new $provider();

        }
    }


    public function register(): void
    {
        foreach ($this->providers as $provider) {

            $provider->register();

        }
    }


    public function boot(): void
    {
        foreach ($this->providers as $provider) {

            $provider->boot();

        }
    }
}