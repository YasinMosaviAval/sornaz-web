<?php

namespace Core\Validation;

use Core\Validation\Rules\RequiredRule;
use Core\Validation\Rules\MinRule;
use Core\Validation\Rules\EmailRule;
use Core\Validation\Rules\NullableRule;
use Core\Validation\Rules\MaxRule;
use Core\Validation\Rules\InRule;
use Core\Validation\Rules\UniqueRule;

class Validator {


    protected array $errors = [];



    // public function validate(array $data, array $rules): bool {
    //     foreach ($rules as $field => $fieldRules) {
    //         $value = $data[$field] ?? null;
    //         foreach (explode('|', $fieldRules) as $rule) {
    //             $instance = $this->makeRule($rule);
    //             if (!$instance->validate($field, $value)) {
    //                 $this->errors[$field][] = $instance->message($field);
    //             }
    //         }
    //     }
    //     return empty($this->errors);
    // }
    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesList = explode('|', $fieldRules);
            $nullable = in_array('nullable', $rulesList, true);
            if ($nullable && ($value === null || $value === '')) {
                continue;
            }
            foreach ($rulesList as $rule) {
                $instance = $this->makeRule($rule);
                if (!$instance->validate($field, $value)) {
                    $this->errors[$field][] = $instance->message($field);
                }
            }
        }
        return empty($this->errors);
    }



    public function errors(): array {
        return $this->errors;
    }



    // protected function makeRule(string $rule): Rule {
    //     if ($rule === 'required') {
    //         return new RequiredRule();
    //     }
    //     if ($rule === 'email') {
    //         return new EmailRule();
    //     }
    //     if (str_starts_with($rule, 'min:')) {
    //         return new MinRule((int) str_replace('min:', '', $rule));
    //     }
    //     throw new \Exception(
    //         "Rule {$rule} not found"
    //     );
    // }
    protected function makeRule(string $rule): Rule {
        if ($rule === 'required') {
            return new RequiredRule();
        }
        if ($rule === 'nullable') {
            return new NullableRule();
        }
        if ($rule === 'email') {
            return new EmailRule();
        }
        if (str_starts_with($rule, 'min:')) {
            [, $value] = explode(':', $rule, 2);
            return new MinRule((int)$value);
        }
        if (str_starts_with($rule, 'max:')) {
            [, $value] = explode(':', $rule, 2);
            return new MaxRule((int)$value);
        }
        if (str_starts_with($rule, 'in:')) {
            [, $value] = explode(':', $rule, 2);
            return new InRule(explode(',', $value));
        }
        if (str_starts_with($rule, 'unique:')) {
            [, $value] = explode(':', $rule, 2);
            [$table, $column] = explode(',', $value);
            return new UniqueRule($table, $column);
        }
        throw new \Exception("Rule {$rule} not found");
    }






}