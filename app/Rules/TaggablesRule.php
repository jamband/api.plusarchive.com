<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

class TaggablesRule implements Taggable
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('validation.taggables.array')->translate();
            return;
        }

        foreach ($value as $element) {
            if (is_string($element) && preg_match(self::PATTERN, $element)) {
                continue;
            } else {
                $fail('validation.taggables.tag')->translate();
            }
        }
    }
}
