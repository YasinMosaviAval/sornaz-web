<?php

namespace Core\validation;

use Core\validation\Rules\RequiredRule;
use Core\validation\Rules\NullableRule;
use Core\validation\Rules\MinRule;
use Core\validation\Rules\MaxRule;
use Core\validation\Rules\EmailRule;
use Core\validation\Rules\InRule;
use Core\validation\Rules\UniqueRule;
use Core\validation\Rules\ExistsRule;
use Core\validation\Rules\NumericRule;
use Core\validation\Rules\IntegerRule;
use Core\validation\Rules\BooleanRule;
use Core\validation\Rules\SameRule;
use Core\validation\Rules\ConfirmedRule;
use Core\validation\Rules\PasswordStrengthRule;
use Exception;

class Validator {


    protected array $errors = [];



    public function validate(array $data, array $rules, array $messages = []): bool {
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
                /*
                |--------------------------------------------------------------------------
                | Rule هایی که کل فرم را لازم دارند
                |--------------------------------------------------------------------------
                */
                if (method_exists($instance, 'setData')) {
                    $instance->setData($data);
                }
                if (!$instance->validate($field, $value)) {
                    $key = $field.'.'.$this->ruleName($rule);
                    $this->errors[$field][] = $messages[$key] ?? $instance->message($field);
                }
            }
        }
        return empty($this->errors);
    }



    protected function ruleName(string $rule): string {
        return explode(':', $rule)[0];
    }



    public function firstErrors(): array {
        $result = [];
        foreach ($this->errors as $field=>$messages) {
            $result[$field] = $messages[0];
        }
        return $result;
    }



    public function errors(): array {
        return $this->errors;
    }



    public function passes(): bool {
        return empty($this->errors);
    }



    public function fails(): bool {
        return !$this->passes();
    }



    public function has(string $field): bool {
        return isset($this->errors[$field]);
    }



    public function first(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }



    protected function makeRule(string $rule): Rule {
        if ($rule === 'required') {return new RequiredRule();}
        if ($rule === 'nullable') {return new NullableRule();}
        if ($rule === 'email') {return new EmailRule();}
        if ($rule === 'numeric') {return new NumericRule();}
        if ($rule === 'integer') {return new IntegerRule();}
        if ($rule === 'boolean') {return new BooleanRule();}
        if (str_starts_with($rule,'min:')) {
            [, $value] = explode(':',$rule,2);
            return new MinRule((int)$value);
        }
        if (str_starts_with($rule,'max:')) {
            [, $value] = explode(':',$rule,2);
            return new MaxRule((int)$value);
        }
        if (str_starts_with($rule,'in:')) {
            [, $value] = explode(':',$rule,2);
            return new InRule(explode(',',$value));
        }
        if (str_starts_with($rule,'unique:')) {
            [, $value] = explode(':',$rule,2);
            [$table,$column] = explode(',',$value);
            return new UniqueRule($table, $column);
        }
        if (str_starts_with($rule,'exists:')) {
            [, $value] = explode(':',$rule,2);
            [$table,$column] = explode(',',$value);
            return new ExistsRule($table, $column);
        }
        if (str_starts_with($rule,'same:')) {
            [, $value] = explode(':',$rule,2);
            return new SameRule($value);
        }
        if ($rule === 'confirmed') {
            return new ConfirmedRule();
        }
        if ($rule === 'password_strength') {
            return new PasswordStrengthRule();
        }
        throw new Exception("Rule {$rule} not found");
    }






}
