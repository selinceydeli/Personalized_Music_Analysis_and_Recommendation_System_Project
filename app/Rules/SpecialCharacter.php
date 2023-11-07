<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SpecialCharacter implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
{
    // Check if the password contains at least one special character
    if (!preg_match('/[!@#$%^&*()_+{}[\]:;<>,.?~\\-]/', $value)) {
        // If no special character is found, report an error using $fail
        $fail("The $attribute must contain at least one special character.");
        hjhjjh
    }
}

}
