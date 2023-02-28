<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

class TaggableRule implements Taggable
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !preg_match(self::PATTERN, $value)) {
            $fail('validation.taggable')->translate();
        }
    }
}
