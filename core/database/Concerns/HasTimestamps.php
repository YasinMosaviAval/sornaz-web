<?php

namespace Core\Database\Concerns;

trait HasTimestamps {
    protected bool $timestamps = true;
    protected string $createdAtColumn = 'created_at';
    protected string $updatedAtColumn = 'updated_at';


    protected function freshTimestamp(): string {
        return date('Y-m-d H:i:s');
    }


    protected function updateTimestampsOnCreate(array &$attributes): void {
        if (!$this->timestamps) {
            return;
        }
        $time = $this->freshTimestamp();
        if (!array_key_exists($this->createdAtColumn, $attributes)) {
            $attributes[$this->createdAtColumn] = $time;
        }
        if (!array_key_exists($this->updatedAtColumn, $attributes)) {
            $attributes[$this->updatedAtColumn] = $time;
        }
    }


    protected function updateTimestampOnUpdate(array &$attributes): void {
        if (!$this->timestamps) {
            return;
        }
        $attributes[$this->updatedAtColumn] = $this->freshTimestamp();
    }


}