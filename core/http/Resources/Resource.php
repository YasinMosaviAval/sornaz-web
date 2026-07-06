<?php

namespace Core\Http\Resources;

abstract class Resource
{
    public function __construct(
        protected mixed $resource
    ) {}

    abstract public function toArray(): array;

    public function resolve(): array
    {
        return $this->toArray();
    }
}

