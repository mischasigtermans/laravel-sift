<?php

namespace MischaSigtermans\Sift\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use MischaSigtermans\Sift\Sift;

class BusinessEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('The :attribute must be a valid email address.');

            return;
        }

        if (Sift::domain($value) === null) {
            $fail('The :attribute must be a business email address.');
        }
    }
}
