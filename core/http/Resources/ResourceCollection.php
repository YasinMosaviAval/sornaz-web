<?php

namespace Core\Http\Resources;

class ResourceCollection
{
    public function __construct(
        protected array $items,
        protected string $resourceClass
    ) {}

    public function resolve(): array
    {
        return array_map(
            fn($item) =>
                (new $this->resourceClass($item))->resolve(),
            $this->items
        );
    }
}