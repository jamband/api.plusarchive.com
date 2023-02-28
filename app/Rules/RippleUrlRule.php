<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Jamband\Ripple\Ripple;

class RippleUrlRule implements ValidationRule
{
    public function __construct(
        private readonly Ripple $ripple,
    ) {
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (null === $this->ripple->url()) {
            $fail('validation.ripple.url')->translate();
        }
    }
}
