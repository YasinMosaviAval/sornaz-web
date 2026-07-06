<?php

namespace Core\Http\Resources\Concerns;

use Core\Http\Resources\MergeValue;
use Core\Http\Resources\MissingValue;

trait Conditionable
{
    protected function when(
        bool $condition,
        mixed $value,
        mixed $default = null
    ): mixed {

        if ($condition) {
            return value($value);
        }

        if ($default !== null) {
            return value($default);
        }

        return MissingValue::make();
    }


    protected function unless(
        bool $condition,
        mixed $value,
        mixed $default = null
    ): mixed {

        return $this->when(
            !$condition,
            $value,
            $default
        );
    }


    protected function whenNull(
        mixed $value,
        mixed $result
    ): mixed {

        return is_null($value)
            ? value($result)
            : MissingValue::make();
    }


    protected function whenNotNull(
        mixed $value
    ): mixed {

        return !is_null($value)
            ? $value
            : MissingValue::make();
    }


    protected function whenHas(
        mixed $value,
        mixed $result = null
    ): mixed {

        if (
            $value !== null &&
            $value !== '' &&
            $value !== []
        ) {
            return $result === null
                ? $value
                : value($result);
        }

        return MissingValue::make();
    }


    protected function merge(
        array $attributes
    ): MergeValue {

        return new MergeValue(
            $attributes
        );
    }


    protected function mergeWhen(
        bool $condition,
        array $attributes
    ): mixed {

        if ($condition) {
            return new MergeValue(
                $attributes
            );
        }

        return MissingValue::make();
    }


    protected function mergeUnless(
        bool $condition,
        array $attributes
    ): mixed {

        return $this->mergeWhen(
            !$condition,
            $attributes
        );
    }


    protected function value(
        mixed $value
    ): mixed {

        return is_callable($value)
            ? $value()
            : $value;
    }
}