<?php

namespace Core\Validation;

use Core\Validation\Rules\RequiredRule;
use Core\Validation\Rules\MinRule;
use Core\Validation\Rules\EmailRule;

class Validator
{
    protected array $errors = [];

    public function validate(
        array $data,
        array $rules
    ): bool {

        foreach ($rules as $field => $fieldRules) {

            $value =
                $data[$field]
                ?? null;

            foreach (
                explode('|', $fieldRules)
                as $rule
            ) {

                $instance =
                    $this->makeRule(
                        $rule
                    );

                if (
                    !$instance->validate(
                        $field,
                        $value
                    )
                ) {

                    $this->errors[$field][]
                        = $instance->message(
                            $field
                        );
                }
            }
        }

        return empty(
            $this->errors
        );
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function makeRule(
        string $rule
    ): Rule {

        if ($rule === 'required') {
            return new RequiredRule();
        }

        if ($rule === 'email') {
            return new EmailRule();
        }

        if (
            str_starts_with(
                $rule,
                'min:'
            )
        ) {

            return new MinRule(
                (int) str_replace(
                    'min:',
                    '',
                    $rule
                )
            );
        }

        throw new \Exception(
            "Rule {$rule} not found"
        );
    }
}