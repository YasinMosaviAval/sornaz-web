<?php

namespace Core\Container;

use ReflectionClass;
use ReflectionParameter;

class Container {
    protected array $bindings = [];
    protected array $instances = [];



    public function bind(string $abstract, string $concrete): void {
        $this->bindings[$abstract] = $concrete;
    }



    public function make(string $class): object {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }
        if (isset($this->bindings[$class])) {
            $class = $this->bindings[$class];
        }
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return new $class;
        }
        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($parameter);
        }
        return $reflection->newInstanceArgs($dependencies);
    }



    protected function resolveParameter(ReflectionParameter $parameter) {
        $type = $parameter->getType();
        if (!$type) {
            return null;
        }
        return $this->make($type->getName());
    }



    public function instance(string $abstract, object $instance): void {
        $this->instances[$abstract] = $instance;
    }



}
