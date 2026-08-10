<?php

namespace Core\validation\Rules;

use Core\validation\Rule;

class PasswordStrengthRule implements Rule {
    public function validate(string $field, mixed $value): bool {
        $password = (string)$value;
        $score = 0;
        $score += preg_match('/[A-Z]/', $password) === 1 ? 1 : 0;
        $score += preg_match('/[a-z]/', $password) === 1 ? 1 : 0;
        $score += preg_match('/[0-9]/', $password) === 1 ? 1 : 0;
        $score += preg_match('/[!@#$%^&*()\-_+=\[\]{}|;:,.<>?\/~]/', $password) === 1 ? 1 : 0;
        $score += strlen($password) > 8 ? 1 : 0;

        return $score >= 3;
    }

    public function message(string $field): string {
        return 'رمز عبور بسیار ضعیف است؛ حداقل ۳ مورد از معیارهای قدرت رمز را رعایت کنید.';
    }
}
