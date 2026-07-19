<?php

namespace Core\Http\Resources;

use Core\Http\Resources\Concerns\Conditionable;
use Core\http\Resources\ResourceCollection;

abstract class JsonResource extends Resource {
    use Conditionable;

    public static function make(mixed $resource): static {
        return new static($resource);
    }


    public static function collection(iterable $items): ResourceCollection {
        return new ResourceCollection($items, static::class);
    }


    public function resource(): mixed {
        return $this->resource;
    }


    public function __get(string $key): mixed {
        if (is_array($this->resource)) {
            return $this->resource[$key] ?? null;
        }
        if (is_object($this->resource)) {
            return $this->resource->$key ?? null;
        }
        return null;
    }


    public function __isset(string $key): bool {
        if (is_array($this->resource)) {
            return isset($this->resource[$key]);
        }
        if (is_object($this->resource)) {
            return isset($this->resource->$key);
        }
        return false;
    }


    public function toJson(int $flags = JSON_UNESCAPED_UNICODE): string {
        return json_encode($this->resolve(), $flags);
    }


    public function jsonSerialize(): array {
        return $this->resolve();
    }
}