<?php

namespace Core\Database\Concerns;

trait GuardsAttributes {

    protected array $fillable = [];
    protected array $guarded = ['*'];


    public function fill(array $attributes): static {return $this->forceFill($this->fillableFromArray($attributes));}


    public function forceFill(array $attributes): static {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }


    protected function fillableFromArray(array $attributes): array {
        if ($this->totallyGuarded()) {
            return [];
        }
        if (!empty($this->fillable)) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }
        return array_diff_key($attributes, array_flip($this->guarded));
    }


    public function isFillable(string $key): bool {
        if (in_array($key, $this->fillable, true)) {
            return true;
        }
        if ($this->isGuarded($key)) {
            return false;
        }
        return empty($this->fillable);
    }


    public function isGuarded(string $key): bool {
        if ($this->guarded === ['*']) {
            return true;
        }
        return in_array($key, $this->guarded, true);
    }


    protected function totallyGuarded(): bool {
        return empty($this->fillable) && $this->guarded === ['*'];
    }




}