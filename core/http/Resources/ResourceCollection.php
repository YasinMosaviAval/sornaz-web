<?php

namespace Core\http\Resources;

use Countable;
use IteratorAggregate;
use ArrayIterator;

class ResourceCollection implements Countable, IteratorAggregate {

    protected iterable $items;
    protected string $resource;


    public function __construct(iterable $items, string $resource) {
        $this->items = $items;
        $this->resource = $resource;
    }



    public function resolve(): array {
        /*
        |--------------------------------------------------------------------------
        | Paginated Result
        |--------------------------------------------------------------------------
        */
        if (is_array($this->items) && isset($this->items['data'])) {
            $rows = [];
            foreach ($this->items['data'] as $item) {
                $rows[] = (new $this->resource($item))->resolve();
            }
            return [
                'data' => $rows,
                'total' => $this->items['total'],
                'page' => $this->items['page'],
                'per_page' => $this->items['per_page'],
                'last_page' => $this->items['last_page'],
            ];
        }
        /*
        |--------------------------------------------------------------------------
        | Normal Collection
        |--------------------------------------------------------------------------
        */
        $result = [];
        foreach ($this->items as $item) {
            $result[] = (new $this->resource($item))->resolve();
        }
        return $result;
    }



    public function toArray(): array {
        return $this->resolve();
    }


    public function jsonSerialize(): array {
        return $this->resolve();
    }


    public function count(): int {
        return count($this->resolve());
    }


    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->resolve());
    }


    public function toJson(int $flags = JSON_UNESCAPED_UNICODE): string {
        return json_encode($this->resolve(), $flags);
    }



}