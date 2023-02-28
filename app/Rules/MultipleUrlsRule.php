<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class MultipleUrlsRule implements ValidationRule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (explode("\n", $value) as $url) {
            if (
                false !== filter_var($url, FILTER_VALIDATE_URL) &&
                preg_match('#\Ahttps?://#', $url)
            ) {
                continue;
            } else {
                $fail('validation.multiple_urls')->translate();
            }
        }
    }
}
