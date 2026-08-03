<?php

namespace Core\module;

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