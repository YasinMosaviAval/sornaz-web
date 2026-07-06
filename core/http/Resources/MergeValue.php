<?php

namespace Core\Http\Resources;

class MergeValue
{
    protected array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}