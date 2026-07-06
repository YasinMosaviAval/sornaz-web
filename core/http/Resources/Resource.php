<?php

namespace Core\Http\Resources;

abstract class Resource {

    protected mixed $resource;

    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }

    abstract public function toArray(): array;

    public function resolve(): array
    {
        return $this->filter(
            $this->toArray()
        );
    }

    protected function filter(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {

            if ($value instanceof MissingValue) {
                continue;
            }

            if ($value instanceof MergeValue) {

                foreach ($value->data() as $mergeKey => $mergeValue) {

                    if ($mergeValue instanceof MissingValue) {
                        continue;
                    }

                    $result[$mergeKey] = $mergeValue;
                }

                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}